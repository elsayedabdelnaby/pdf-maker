<?php

namespace App\Http\Controllers;

use App\Models\PdfTemplate;
use App\Models\Invoice;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;
use Barryvdh\Snappy\Facades\SnappyPdf;
use mikehaertl\wkhtmlto\Pdf;

class PdfTemplateController extends Controller
{
    public function index()
    {
        $templates = PdfTemplate::all();
        return view('pdf-templates.index', compact('templates'));
    }

    public function create()
    {
        return view('pdf-templates.create');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'engine' => 'required|in:handlebars,mustache',
                'page_size' => 'required|string',
                'orientation' => 'required|in:portrait,landscape',
                'margin_top' => 'required|integer|min:0',
                'margin_right' => 'required|integer|min:0',
                'margin_bottom' => 'required|integer|min:0',
                'margin_left' => 'required|integer|min:0',
                'css' => 'nullable|string',
                'header_html' => 'nullable|string',
                'footer_html' => 'nullable|string',
                'body_html' => 'required|string',
            ]);

            $validated['rtl'] = $request->has('rtl');
            $validated['fonts'] = ['Arial', 'Tahoma', 'Times New Roman'];

            PdfTemplate::create($validated);

            return redirect()->route('pdf-templates.index')
                ->with('success', 'Template created successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Debug: Show request data AND validation errors
            dd([
                'REQUEST DATA' => $request->all(),
                'VALIDATION ERRORS' => $e->errors(),
                'VALIDATION MESSAGES' => $e->getMessage()
            ]);
        }
    }

    public function show(PdfTemplate $pdfTemplate)
    {
        return view('pdf-templates.show', compact('pdfTemplate'));
    }

    public function edit(PdfTemplate $pdfTemplate)
    {
        return view('pdf-templates.edit', compact('pdfTemplate'));
    }

    public function update(Request $request, PdfTemplate $pdfTemplate)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'engine' => 'required|in:handlebars,mustache',
                'page_size' => 'required|string',
                'orientation' => 'required|in:portrait,landscape',
                'margin_top' => 'required|integer|min:0',
                'margin_right' => 'required|integer|min:0',
                'margin_bottom' => 'required|integer|min:0',
                'margin_left' => 'required|integer|min:0',
                'css' => 'nullable|string',
                'header_html' => 'nullable|string',
                'footer_html' => 'nullable|string',
                'body_html' => 'required|string',
            ]);

            $validated['rtl'] = $request->has('rtl');

            $pdfTemplate->update($validated);

            return redirect()->route('pdf-templates.index')
                ->with('success', 'Template updated successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Debug: Show request data AND validation errors
            dd([
                'REQUEST DATA' => $request->all(),
                'VALIDATION ERRORS' => $e->errors(),
                'VALIDATION MESSAGES' => $e->getMessage(),
                'CURRENT TEMPLATE' => $pdfTemplate->toArray()
            ]);
        }
    }

    public function destroy(PdfTemplate $pdfTemplate)
    {
        $pdfTemplate->delete();
        return redirect()->route('pdf-templates.index')
            ->with('success', 'Template deleted successfully!');
    }

    // Replace your generatePdf method with this updated version
    public function generatePdf(Request $request, $templateId, $modelId)
    {
        try {
            $template = PdfTemplate::findOrFail($templateId);

            // Get the invoice data
            $invoice = Invoice::where('invoiceid', $modelId)->first();

            if (!$invoice) {
                return response()->json(['error' => 'Invoice not found'], 404);
            }

            // Generate HTML content
            $html = $this->generateHtml($template, $invoice);

            // Debug: Check if HTML is generated
            if (empty($html)) {
                return response()->json(['error' => 'HTML content is empty'], 500);
            }

            // Generate PDF using direct shell execution
            $binaryPath = '/home/thenewc1/bin/usr/local/bin/wkhtmltopdf';

            // Check if binary exists first
            if (!file_exists($binaryPath)) {
                Log::error('WKHTMLTOPDF binary not found at: ' . $binaryPath);
                return response()->json([
                    'error' => 'WKHTMLTOPDF binary not found',
                    'binary_path' => $binaryPath,
                    'file_exists' => false
                ], 500);
            }

            // Create temporary HTML file
            $tempHtmlFile = tempnam(sys_get_temp_dir(), 'pdf_template_') . '.html';
            $tempPdfFile = tempnam(sys_get_temp_dir(), 'pdf_template_') . '.pdf';

            file_put_contents($tempHtmlFile, $html);
            Log::info('HTML saved to temp file: ' . $tempHtmlFile);

            // Build WKHTMLTOPDF command
            $command = '"' . $binaryPath . '"';
            $command .= ' --page-size "' . $template->page_size . '"';
            $command .= ' --orientation "' . $template->orientation . '"';
            $command .= ' --margin-top "' . $template->margin_top . '"';
            $command .= ' --margin-right "' . $template->margin_right . '"';
            $command .= ' --margin-bottom "' . ($template->margin_bottom + 15) . '"'; // Add space for footer
            $command .= ' --margin-left "' . $template->margin_left . '"';
            $command .= ' --encoding "UTF-8"';
            $command .= ' --enable-local-file-access';
            $command .= ' --no-outline';
            $command .= ' --quiet';
            $command .= ' --enable-javascript';
            $command .= ' --javascript-delay "1000"';
            $command .= ' --no-stop-slow-scripts';
            $command .= ' --disable-smart-shrinking';
            $command .= ' --print-media-type';
            $command .= ' --dpi "96"';

            // Handle footer and page numbering based on template
            if ($template->footer_html && !empty(trim($template->footer_html))) {
                // Template has custom footer content - create footer HTML file
                $footerHtml = $this->generateFooterHtml($template);
                $tempFooterFile = tempnam(sys_get_temp_dir(), 'pdf_footer_') . '.html';
                file_put_contents($tempFooterFile, $footerHtml);
                Log::info('Footer HTML saved to temp file: ' . $tempFooterFile);

                $command .= ' --footer-html "' . $tempFooterFile . '"';
                $command .= ' --footer-spacing "5"';
            } else {
                // No custom footer - use simple center footer
                $command .= ' --footer-center "Page [page] of [topage]"';
                $command .= ' --footer-spacing "5"';
                $command .= ' --footer-font-size "10"';
            }

            $command .= ' "' . $tempHtmlFile . '" "' . $tempPdfFile . '"';

            Log::info('Executing command: ' . $command);

            // Execute command
            $output = shell_exec($command . ' 2>&1');
            $returnCode = shell_exec('echo %ERRORLEVEL%');

            Log::info('Command output: ' . $output);
            Log::info('Return code: ' . $returnCode);

            // Check if PDF was generated
            if (file_exists($tempPdfFile) && filesize($tempPdfFile) > 0) {
                $pdfContent = file_get_contents($tempPdfFile);
                Log::info('PDF generated successfully, size: ' . strlen($pdfContent));

                // Clean up temp files
                unlink($tempHtmlFile);
                unlink($tempPdfFile);
                if (isset($tempFooterFile) && file_exists($tempFooterFile)) {
                    unlink($tempFooterFile);
                }

                $filename = 'invoice_' . $modelId . '_' . date('Y-m-d_H-i-s') . '.pdf';

                return response($pdfContent, 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                    'Content-Length' => strlen($pdfContent)
                ]);
            } else {
                Log::warning('WKHTMLTOPDF failed, trying Laravel Snappy as fallback...');

                // Clean up temp files
                if (file_exists($tempHtmlFile)) unlink($tempHtmlFile);
                if (file_exists($tempPdfFile)) unlink($tempPdfFile);
                if (isset($tempFooterFile) && file_exists($tempFooterFile)) unlink($tempFooterFile);

                // Fallback to Laravel Snappy (without duplicate page numbering)
                try {
                    $pdfContent = SnappyPdf::loadHTML($html)
                        ->setOption('page-size', $template->page_size)
                        ->setOption('orientation', $template->orientation)
                        ->setOption('margin-top', $template->margin_top)
                        ->setOption('margin-right', $template->margin_right)
                        ->setOption('margin-bottom', $template->margin_bottom + 15)
                        ->setOption('margin-left', $template->margin_left)
                        ->setOption('encoding', 'UTF-8')
                        ->setOption('enable-local-file-access', true)
                        ->setOption('footer-center', 'Page [page] of [topage]')
                        ->setOption('footer-font-size', '10')
                        ->output();

                    Log::info('Laravel Snappy fallback successful, PDF length: ' . strlen($pdfContent));

                    if (empty($pdfContent)) {
                        return response()->json([
                            'error' => 'Both WKHTMLTOPDF and Laravel Snappy failed',
                            'html_length' => strlen($html),
                            'template_info' => $template->only(['id', 'name', 'body_html']),
                            'wkhtmltopdf_error' => 'Direct execution failed: ' . $output,
                            'return_code' => $returnCode
                        ], 500);
                    }

                    $filename = 'invoice_' . $modelId . '_' . date('Y-m-d_H-i-s') . '.pdf';

                    return response($pdfContent, 200, [
                        'Content-Type' => 'application/pdf',
                        'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                        'Content-Length' => strlen($pdfContent)
                    ]);
                } catch (\Exception $e) {
                    Log::error('Laravel Snappy fallback failed: ' . $e->getMessage());
                    return response()->json([
                        'error' => 'Both PDF generators failed',
                        'html_length' => strlen($html),
                        'template_info' => $template->only(['id', 'name', 'body_html']),
                        'wkhtmltopdf_error' => 'Direct execution failed: ' . $output,
                        'return_code' => $returnCode,
                        'snappy_error' => $e->getMessage()
                    ], 500);
                }
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'PDF generation failed',
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    public function debugPdf(Request $request, $templateId, $modelId)
    {
        try {
            $template = PdfTemplate::findOrFail($templateId);
            $invoice = Invoice::where('invoiceid', $modelId)->first();

            if (!$invoice) {
                return response()->json(['error' => 'Invoice not found'], 404);
            }

            // Generate HTML content
            $html = $this->generateHtml($template, $invoice);

            // Generate footer HTML to check for page numbering issues
            $footerHtml = $this->generateFooterHtml($template);

            // Get company info safely
            $company = Company::where('organization_id', 1)->first();
            $companyName = $company ? $company->organizationname : 'Not found';

            // Check for page numbering patterns in template content
            $pageNumberingIssues = [];
            $templateContent = [
                'header' => $template->header_html ?: '',
                'body' => $template->body_html ?: '',
                'footer' => $template->footer_html ?: ''
            ];

            foreach ($templateContent as $section => $content) {
                if (strpos($content, '[Page [page] of [topag]') !== false) {
                    $pageNumberingIssues[] = "Found malformed page numbering in template {$section}: [Page [page] of [topag]";
                }
                if (strpos($content, '[Page [page] of [topage]') !== false) {
                    $pageNumberingIssues[] = "Found malformed page numbering in template {$section}: [Page [page] of [topage]";
                }
                if (strpos($content, '[topag]') !== false) {
                    $pageNumberingIssues[] = "Found malformed 'topag' in template {$section}";
                }
                if (strpos($content, 'Page [page] of [topage]') !== false) {
                    $pageNumberingIssues[] = "Found correct page numbering in template {$section}";
                }
            }

            // Return debug information
            return response()->json([
                'template' => [
                    'id' => $template->id,
                    'name' => $template->name,
                    'body_html_length' => strlen($template->body_html),
                    'header_html_length' => strlen($template->header_html ? $template->header_html : ''),
                    'footer_html_length' => strlen($template->footer_html ? $template->footer_html : ''),
                    'rtl' => $template->rtl,
                    'page_size' => $template->page_size,
                    'orientation' => $template->orientation,
                    'margins' => [
                        'top' => $template->margin_top,
                        'right' => $template->margin_right,
                        'bottom' => $template->margin_bottom,
                        'left' => $template->margin_left,
                    ]
                ],
                'invoice' => [
                    'id' => $invoice->invoiceid ? $invoice->invoiceid : $invoice->id,
                    'invoice_no' => $invoice->invoice_no ? $invoice->invoice_no : '',
                    'total' => $invoice->total ? $invoice->total : '',
                    'invoicedate' => $invoice->invoicedate ? $invoice->invoicedate : ''
                ],
                'generated_html' => [
                    'length' => strlen($html),
                    'preview' => substr($html, 0, 500) . '...'
                ],
                'footer_html' => [
                    'length' => strlen($footerHtml),
                    'content' => $footerHtml
                ],
                'raw_template_content' => [
                    'header' => $template->header_html,
                    'body' => $template->body_html,
                    'footer' => $template->footer_html
                ],
                'page_numbering_issues' => $pageNumberingIssues,
                'company' => $companyName
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Debug failed',
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    public function testWkhtmltopdf()
    {
        try {
            $longPath = 'C:\Program Files\wkhtmltopdf\bin\wkhtmltopdf.exe';

            // Check if binary exists
            $fileExists = file_exists($longPath);

            // Try to get version using quoted path
            $version = null;
            if ($fileExists) {
                $output = shell_exec('"' . $longPath . '" --version 2>&1');
                if ($output) {
                    $version = trim($output);
                }
            }

            return response()->json([
                'long_path' => $longPath,
                'file_exists' => $fileExists,
                'version' => $version,
                'php_os' => PHP_OS,
                'temp_dir' => sys_get_temp_dir(),
                'current_dir' => getcwd()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Test failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }


    // Updated generateHtml method to completely remove page numbering from content
    private function generateHtml($template, $invoice)
    {
        // Simple template engine replacement
        $html = $template->body_html;
        $company = Company::where('organization_id', 1)->first();

        // Debug: Log template and invoice data
        Log::info('Template data:', [
            'template_id' => $template->id,
            'body_html_length' => strlen($template->body_html),
            'header_html_length' => strlen($template->header_html ? $template->header_html : ''),
            'footer_html_length' => strlen($template->footer_html ? $template->footer_html : ''),
            'rtl' => $template->rtl
        ]);

        Log::info('Invoice data:', [
            'invoice_id' => $invoice->invoiceid ? $invoice->invoiceid : $invoice->id,
            'invoice_no' => $invoice->invoice_no ? $invoice->invoice_no : '',
            'total' => $invoice->total ? $invoice->total : '',
            'invoicedate' => $invoice->invoicedate ? $invoice->invoicedate : '',
            'invoicedate_type' => $invoice->invoicedate ? gettype($invoice->invoicedate) : 'null',
            'invoicedate_raw' => $invoice->invoicedate ? (string)$invoice->invoicedate : 'null'
        ]);

        // Get invoice custom fields
        $invoiceCF = $invoice->invoiceCF;

        // Replace common placeholders for Invoice model
        $replacements = [
            // Basic Invoice Fields
            '{{company_name}}' => $company ? $company->organizationname : 'Company Name',
            '{{invoice_number}}' => $invoice->invoice_no ? $invoice->invoice_no : ($invoice->invoiceid ? $invoice->invoiceid : ''),
            '{{date}}' => $this->formatDate($invoice->invoicedate),
            '{{amount}}' => $invoice->total ? $invoice->total : '0.00',
            '{{customer_name}}' => $invoice->contact ? $invoice->contact->lastname : 'Customer',
            '{{model_type}}' => 'Invoice',
            '{{model_id}}' => $invoice->invoiceid ? $invoice->invoiceid : $invoice->id,

            // Additional Invoice Fields (keeping your existing ones)
            '{{subject}}' => $invoice->subject ?: '',
            '{{contract_number}}' => $invoice->contract_number ?: '',
            '{{sales_order}}' => $invoice->salesorderid ?: '',
            '{{customer_no}}' => $invoice->customerno ?: '',
            '{{due_date}}' => $this->formatDate($invoice->duedate),
            '{{purchase_order}}' => $invoice->purchaseorder ?: '',
            '{{subtotal}}' => $invoice->subtotal ?: '0.00',
            '{{pre_tax_total}}' => $invoice->pre_tax_total ?: '0.00',
            '{{tax_type}}' => $invoice->taxtype ?: '',
            '{{discount_percent}}' => $invoice->discount_percent ?: '0',
            '{{discount_amount}}' => $invoice->discount_amount ?: '0.00',
            '{{shipping_amount}}' => $invoice->s_h_amount ?: '0.00',
            '{{shipping_percent}}' => $invoice->s_h_percent ?: '0',
            '{{received}}' => $invoice->received ?: '0.00',
            '{{balance}}' => $invoice->balance ?: '0.00',
            '{{currency}}' => $invoice->currency_id ?: '',
            '{{conversion_rate}}' => $invoice->conversion_rate ?: '1.00',
            '{{terms_conditions}}' => $invoice->terms_conditions ?: '',
            '{{status}}' => $invoice->invoicedestatus ?: '',

            // Invoice Custom Fields (keeping your existing ones)
            // ... (all your existing custom field replacements)
        ];

        $html = str_replace(array_keys($replacements), array_values($replacements), $html);

        // COMPLETELY remove all page numbering patterns from the body content
        $pageNumberingPatterns = [
            '/\[Page \[page\] of \[topag\]\]?/',
            '/\[Page \[page\] of \[topage\]\]?/',
            '/Page \[page\] of \[topage\]/',
            '/Page \[page\] of \[topag\]/',
            '/\[topag\]/',
            '/\[page\]/',
            '/\[topage\]/',
            // Match any text that looks like page numbering
            '/Page\s+\d+\s+of\s+\d+/',
            '/صفحة\s+\d+\s+من\s+\d+/', // Arabic page numbering if applicable
        ];

        foreach ($pageNumberingPatterns as $pattern) {
            $html = preg_replace($pattern, '', $html);
        }

        // Clean up extra whitespace that might be left
        $html = preg_replace('/\s+/', ' ', $html);
        $html = trim($html);

        // Add CSS styling
        $css = $template->css ?: $this->getDefaultCss($template->rtl);

        // Combine header, body, and footer
        $fullHtml = '<!DOCTYPE html><html dir="' . ($template->rtl ? 'rtl' : 'ltr') . '">';
        $fullHtml .= '<head><meta charset="UTF-8"><style>' . $css . '</style></head>';
        $fullHtml .= '<body>';

        if ($template->header_html) {
            // Clean header HTML from page numbering patterns
            $processedHeader = $template->header_html;
            foreach ($pageNumberingPatterns as $pattern) {
                $processedHeader = preg_replace($pattern, '', $processedHeader);
            }
            $processedHeader = trim($processedHeader);

            if (!empty($processedHeader)) {
                $fullHtml .= '<div class="header">' . $processedHeader . '</div>';
            }
        }

        $fullHtml .= '<div class="content">' . $html . '</div>';

        // Don't include footer in main HTML - it will be handled by wkhtmltopdf

        $fullHtml .= '</body></html>';

        // Debug: Log final HTML length
        Log::info('Generated HTML length: ' . strlen($fullHtml));

        return $fullHtml;
    }


    private function generateFooterHtml($template)
    {
        $direction = $template->rtl ? 'rtl' : 'ltr';

        $footerHtml = '<!DOCTYPE html>
<html dir="' . $direction . '">
<head>
    <meta charset="UTF-8">
    <style>
        body { 
            margin: 0; 
            padding: 5px 20px; 
            font-family: Arial, sans-serif; 
            font-size: 10px; 
            color: #666;
            direction: ' . $direction . ';
            text-align: center;
        }
        .footer-content {
            margin-bottom: 5px;
        }
        .page-numbers {
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>';

        // Add template footer content if it exists (cleaned of page numbering)
        if ($template->footer_html && !empty(trim($template->footer_html))) {
            $processedFooter = $template->footer_html;

            // Remove all page numbering patterns from footer content
            $pageNumberingPatterns = [
                '/\[Page \[page\] of \[topag\]\]?/',
                '/\[Page \[page\] of \[topage\]\]?/',
                '/Page \[page\] of \[topage\]/',
                '/Page \[page\] of \[topag\]/',
                '/\[topag\]/',
                '/\[page\]/',
                '/\[topage\]/',
                '/Page\s+\d+\s+of\s+\d+/',
                '/صفحة\s+\d+\s+من\s+\d+/', // Arabic if applicable
            ];

            foreach ($pageNumberingPatterns as $pattern) {
                $processedFooter = preg_replace($pattern, '', $processedFooter);
            }

            // Clean up extra whitespace
            $processedFooter = preg_replace('/\s+/', ' ', $processedFooter);
            $processedFooter = trim($processedFooter);

            if (!empty($processedFooter)) {
                $footerHtml .= '<div class="footer-content">' . $processedFooter . '</div>';
            }
        }

        // Always add clean page numbering at the bottom
        $footerHtml .= '<div class="page-numbers">Page [page] of [topage]</div>';

        $footerHtml .= '</body></html>';

        // Log the generated footer HTML for debugging
        Log::info('Generated footer HTML: ' . $footerHtml);

        return $footerHtml;
    }

    private function formatDate($date)
    {
        if (!$date) {
            return date('Y-m-d');
        }

        // If it's already a string, try to parse it
        if (is_string($date)) {
            $timestamp = strtotime($date);
            if ($timestamp !== false) {
                return date('Y-m-d', $timestamp);
            }
        }

        // If it's a Carbon/DateTime object
        if (method_exists($date, 'format')) {
            return $date->format('Y-m-d');
        }

        // Fallback to current date
        return date('Y-m-d');
    }

    private function getDefaultCss($rtl = false)
    {
        $direction = $rtl ? 'rtl' : 'ltr';
        $textAlign = $rtl ? 'right' : 'left';

        return "
            body { 
                font-family: 'Arial', 'Tahoma', 'Times New Roman', sans-serif; 
                direction: {$direction}; 
                text-align: {$textAlign}; 
                margin: 0; 
                padding: 20px; 
                font-size: 12px;
                line-height: 1.4;
            }
            .header { 
                border-bottom: 2px solid #333; 
                padding-bottom: 10px; 
                margin-bottom: 20px; 
                text-align: {$textAlign};
            }
            .footer { 
                border-top: 1px solid #ccc; 
                padding-top: 10px; 
                margin-top: 20px; 
                text-align: center; 
            }
            .content { 
                min-height: 400px; 
                text-align: {$textAlign};
            }
            table { 
                width: 100%; 
                border-collapse: collapse; 
                margin: 20px 0; 
            }
            th, td { 
                border: 1px solid #ddd; 
                padding: 8px; 
                text-align: {$textAlign}; 
            }
            th { 
                background-color: #f2f2f2; 
                font-weight: bold; 
            }
            .rtl { 
                direction: rtl; 
                text-align: right; 
                unicode-bidi: bidi-override;
            }
            .ltr { 
                direction: ltr; 
                text-align: left; 
            }
            /* Arabic text specific styles */
            [lang=\"AR-EG\"] {
                font-family: 'Tahoma', 'Arial', sans-serif;
                direction: rtl;
                text-align: right;
            }
            /* Mixed content handling */
            [dir=\"rtl\"] {
                direction: rtl;
                text-align: right;
            }
            [dir=\"ltr\"] {
                direction: ltr;
                text-align: left;
            }
            /* Page break support */
            .page-break {
                page-break-before: always;
                break-before: page;
                margin-top: 20px;
                clear: both;
            }
            .page-break-after {
                page-break-after: always;
                break-after: page;
                margin-bottom: 20px;
            }
            /* Ensure content doesn't break awkwardly */
            .no-break {
                page-break-inside: avoid;
                break-inside: avoid;
            }
            /* Force page breaks for specific elements */
            .force-page-break {
                page-break-before: always;
                break-before: page;
            }
            /* Hide page break visual elements in PDF */
            @media print {
                .page-break {
                    border: none !important;
                    background: none !important;
                    padding: 0 !important;
                    margin: 0 !important;
                    height: 0 !important;
                    overflow: hidden !important;
                    color: transparent !important;
                }
            }
        ";
    }
}
