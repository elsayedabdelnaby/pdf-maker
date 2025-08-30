<?php

namespace App\Http\Controllers;

use App\Models\PdfTemplate;
use App\Models\Invoice;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

class PdfExportController extends Controller
{
    /**
     * Generate PDF using pure PHP approach
     */
    public function generatePdf(Request $request, $templateId, $modelId)
    {
        try {
            $template = PdfTemplate::findOrFail($templateId);
            $invoice = Invoice::where('invoiceid', $modelId)->first();
            
            if (!$invoice) {
                return response()->json(['error' => 'Invoice not found'], 404);
            }

            // Generate HTML content
            $html = $this->generateHtml($template, $invoice);
            
            if (empty($html)) {
                return response()->json(['error' => 'HTML content is empty'], 500);
            }

            // Generate PDF using pure PHP approach
            $pdfContent = $this->generatePdfFromHtml($html, $template);
            
            if (empty($pdfContent)) {
                return response()->json(['error' => 'PDF generation failed'], 500);
            }

            $filename = 'invoice_' . $modelId . '_' . date('Y-m-d_H-i-s') . '.pdf';
            
            return Response::make($pdfContent, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Content-Length' => strlen($pdfContent)
            ]);
            
        } catch (\Exception $e) {
            Log::error('PDF generation failed: ' . $e->getMessage());
            return response()->json([
                'error' => 'PDF generation failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Debug PDF generation
     */
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
            
            // Get company info safely
            $company = Company::where('organization_id', 1)->first();
            $companyName = $company ? $company->organizationname : 'Not found';
            
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
                'company' => $companyName,
                'method' => 'New Export Controller (Pure PHP + Snappy Fallback)'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Debug failed',
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Generate HTML content with placeholders replaced
     */
    private function generateHtml($template, $invoice)
    {
        $html = $template->body_html;
        $company = Company::where('organization_id', 1)->first();
        
        // Get invoice custom fields
        $invoiceCF = $invoice->invoiceCF;
        
        // Replace placeholders
        $replacements = [
            // Basic Invoice Fields
            '{{company_name}}' => $company ? $company->organizationname : 'Company Name',
            '{{invoice_number}}' => $invoice->invoice_no ? $invoice->invoice_no : ($invoice->invoiceid ? $invoice->invoiceid : ''),
            '{{date}}' => $this->formatDate($invoice->invoicedate),
            '{{amount}}' => $invoice->total ? $invoice->total : '0.00',
            '{{customer_name}}' => $invoice->contact ? $invoice->contact->lastname : 'Customer',
            '{{model_type}}' => 'Invoice',
            '{{model_id}}' => $invoice->invoiceid ? $invoice->invoiceid : $invoice->id,
            
            // Additional Invoice Fields
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
            
            // Invoice Custom Fields (CF)
            '{{check_date}}' => $invoiceCF && $invoiceCF->cf_1183 ? $this->formatDate($invoiceCF->cf_1183) : '',
            '{{unit_type}}' => $invoiceCF && $invoiceCF->cf_1185 ?: '',
            '{{tax_start_date}}' => $invoiceCF && $invoiceCF->cf_1187 ? $this->formatDate($invoiceCF->cf_1187) : '',
            '{{next_date}}' => $invoiceCF && $invoiceCF->cf_1189 ? $this->formatDate($invoiceCF->cf_1189) : '',
            '{{contract_date}}' => $invoiceCF && $invoiceCF->cf_1191 ? $this->formatDate($invoiceCF->cf_1191) : '',
            '{{partial_invoice}}' => $invoiceCF && $invoiceCF->cf_1193 ?: '',
            '{{payment_plan}}' => $invoiceCF && $invoiceCF->cf_1195 ?: '',
            '{{balance_adjustment}}' => $invoiceCF && $invoiceCF->cf_1197 ?: '',
            '{{currency_type}}' => $invoiceCF && $invoiceCF->cf_1199 ?: '',
            '{{next_follow_details}}' => $invoiceCF && $invoiceCF->cf_1201 ?: '',
            '{{developer_name}}' => $invoiceCF && $invoiceCF->cf_1203 ?: '',
            '{{unit_no}}' => $invoiceCF && $invoiceCF->cf_1207 ?: '',
            '{{company_percent}}' => $invoiceCF && $invoiceCF->cf_1209 ?: '',
            '{{unit_total_price}}' => $invoiceCF && $invoiceCF->cf_1211 ?: '',
            '{{unit_area}}' => $invoiceCF && $invoiceCF->cf_1213 ?: '',
            '{{floors_count}}' => $invoiceCF && $invoiceCF->cf_1215 ?: '',
            '{{incentive_percent}}' => $invoiceCF && $invoiceCF->cf_1217 ?: '',
            '{{unit_number}}' => $invoiceCF && $invoiceCF->cf_1219 ?: '',
            '{{advance_payment}}' => $invoiceCF && $invoiceCF->cf_1221 ?: '',
            '{{incentive_claim_date}}' => $invoiceCF && $invoiceCF->cf_1223 ? $this->formatDate($invoiceCF->cf_1223) : '',
            '{{incentive_man_percent}}' => $invoiceCF && $invoiceCF->cf_1225 ?: '',
            '{{manager_id}}' => $invoiceCF && $invoiceCF->cf_1227 ?: '',
            '{{manager_name}}' => $invoiceCF && $invoiceCF->cf_1229 ?: '',
            '{{client_name}}' => $invoiceCF && $invoiceCF->cf_1231 ?: '',
            '{{eight_years}}' => $invoiceCF && $invoiceCF->cf_1233 ?: '',
            '{{incentive_man_claim_date}}' => $invoiceCF && $invoiceCF->cf_1235 ? $this->formatDate($invoiceCF->cf_1235) : '',
            '{{ten_years}}' => $invoiceCF && $invoiceCF->cf_1237 ?: '',
            '{{sixteen_years_20}}' => $invoiceCF && $invoiceCF->cf_1239 ?: '',
            '{{sixteen_years_10}}' => $invoiceCF && $invoiceCF->cf_1241 ?: '',
            '{{sixteen_years_15}}' => $invoiceCF && $invoiceCF->cf_1243 ?: '',
            '{{twelve_years}}' => $invoiceCF && $invoiceCF->cf_1245 ?: '',
            '{{net_value}}' => $invoiceCF && $invoiceCF->cf_1247 ?: '',
            '{{delivery_month}}' => $invoiceCF && $invoiceCF->cf_1249 ?: '',
            '{{eighty_eight_percent}}' => $invoiceCF && $invoiceCF->cf_1379 ?: '',
            '{{garden}}' => $invoiceCF && $invoiceCF->cf_1537 ?: '',
            '{{building_no}}' => $invoiceCF && $invoiceCF->cf_1539 ?: '',
            '{{unit_price_in_words}}' => $invoiceCF && $invoiceCF->cf_1541 ?: '',
            '{{remaining_balance}}' => $invoiceCF && $invoiceCF->cf_1543 ?: '',
            '{{remaining_balance_amount}}' => $invoiceCF && $invoiceCF->cf_1545 ?: '',
            '{{reservation_payment}}' => $invoiceCF && $invoiceCF->cf_1547 ?: '',
            '{{reservation_payment_amount}}' => $invoiceCF && $invoiceCF->cf_1549 ?: '',
            '{{payment_day}}' => $invoiceCF && $invoiceCF->cf_1551 ?: '',
            '{{payment_month}}' => $invoiceCF && $invoiceCF->cf_1553 ?: '',
            '{{maintenance_amount}}' => $invoiceCF && $invoiceCF->cf_1555 ?: '',
            '{{maintenance_amount_value}}' => $invoiceCF && $invoiceCF->cf_1557 ?: '',
            '{{amount_3}}' => $invoiceCF && $invoiceCF->cf_1559 ?: '',
            '{{amount_3_value}}' => $invoiceCF && $invoiceCF->cf_1561 ?: '',
            '{{amount_4}}' => $invoiceCF && $invoiceCF->cf_1563 ?: '',
            '{{amount_4_value}}' => $invoiceCF && $invoiceCF->cf_1565 ?: '',
            '{{unit_area_in_words}}' => $invoiceCF && $invoiceCF->cf_1567 ?: '',
            '{{delivery_year}}' => $invoiceCF && $invoiceCF->cf_1569 ?: '',
            '{{day_number}}' => $invoiceCF && $invoiceCF->cf_1573 ?: '',
            '{{advance_adjustment}}' => $invoiceCF && $invoiceCF->cf_1575 ?: '',
            '{{garden_area_in_contract}}' => $invoiceCF && $invoiceCF->cf_1591 ?: '',
            '{{confirm}}' => $invoiceCF && $invoiceCF->cf_1629 ?: '',
            '{{confirm_comment}}' => $invoiceCF && $invoiceCF->cf_1631 ?: '',
            '{{project_name}}' => $invoiceCF && $invoiceCF->cf_1669 ?: '',
            '{{project_category}}' => $invoiceCF && $invoiceCF->cf_1671 ?: '',
            '{{project_location}}' => $invoiceCF && $invoiceCF->cf_1673 ?: '',
            '{{unit_category}}' => $invoiceCF && $invoiceCF->cf_1675 ?: '',
            '{{contract_status}}' => $invoiceCF && $invoiceCF->cf_1677 ?: '',
            '{{payment_plan_module}}' => $invoiceCF && $invoiceCF->cf_1789 ?: '',
        ];

        $html = str_replace(array_keys($replacements), array_values($replacements), $html);

        // Add CSS styling
        $css = $template->css ?: $this->getDefaultCss($template->rtl);
        
        // Combine header, body, and footer with page numbering
        $fullHtml = '<!DOCTYPE html><html dir="' . ($template->rtl ? 'rtl' : 'ltr') . '">';
        $fullHtml .= '<head><meta charset="UTF-8"><style>' . $css . '</style></head>';
        $fullHtml .= '<body>';
        
        if ($template->header_html) {
            $fullHtml .= '<div class="header">' . $template->header_html . '</div>';
        }
        
        $fullHtml .= '<div class="content">' . $html . '</div>';
        
        // Add footer with page numbering
        if ($template->footer_html) {
            $fullHtml .= '<div class="footer">' . $template->footer_html . '</div>';
        }
        
        // Add page numbering
        $fullHtml .= '<div class="page-numbering">Page <span class="page"></span> of <span class="topage"></span></div>';
        
        $fullHtml .= '</body></html>';
        
        return $fullHtml;
    }

    /**
     * Generate PDF from HTML using pure PHP approach
     */
    private function generatePdfFromHtml($html, $template)
    {
        // Try to use Laravel Snappy first (if available)
        try {
            if (class_exists('Barryvdh\Snappy\Facades\SnappyPdf')) {
                $pdf = \Barryvdh\Snappy\Facades\SnappyPdf::loadHTML($html);
                $pdf->setOption('page-size', $template->page_size);
                $pdf->setOption('orientation', $template->orientation);
                $pdf->setOption('margin-top', $template->margin_top);
                $pdf->setOption('margin-right', $template->margin_right);
                $pdf->setOption('margin-bottom', $template->margin_bottom);
                $pdf->setOption('margin-left', $template->margin_left);
                $pdf->setOption('encoding', 'UTF-8');
                $pdf->setOption('enable-local-file-access', true);
                
                return $pdf->output();
            }
        } catch (\Exception $e) {
            Log::warning('Laravel Snappy not available, using fallback method: ' . $e->getMessage());
        }
        
        // Fallback to simple HTML to PDF conversion
        $pdfContent = $this->simpleHtmlToPdf($html);
        
        return $pdfContent;
    }

    /**
     * Simple HTML to PDF conversion (basic implementation)
     */
    private function simpleHtmlToPdf($html)
    {
        // This is a basic implementation - you can replace this with any PDF library
        // For now, we'll create a simple text-based PDF structure
        
        $lines = explode("\n", strip_tags($html));
        $pdfContent = "%PDF-1.4\n";
        $pdfContent .= "1 0 obj\n";
        $pdfContent .= "<<\n";
        $pdfContent .= "/Type /Catalog\n";
        $pdfContent .= "/Pages 2 0 R\n";
        $pdfContent .= ">>\n";
        $pdfContent .= "endobj\n";
        
        // Add page content
        $pdfContent .= "2 0 obj\n";
        $pdfContent .= "<<\n";
        $pdfContent .= "/Type /Pages\n";
        $pdfContent .= "/Kids [3 0 R]\n";
        $pdfContent .= "/Count 1\n";
        $pdfContent .= ">>\n";
        $pdfContent .= "endobj\n";
        
        // Add page object
        $pdfContent .= "3 0 obj\n";
        $pdfContent .= "<<\n";
        $pdfContent .= "/Type /Page\n";
        $pdfContent .= "/Parent 2 0 R\n";
        $pdfContent .= "/MediaBox [0 0 595 842]\n";
        $pdfContent .= "/Contents 4 0 R\n";
        $pdfContent .= ">>\n";
        $pdfContent .= "endobj\n";
        
        // Add content stream
        $content = "/Helvetica 12 Tf\n";
        $content .= "72 750 Td\n";
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (!empty($line)) {
                $content .= "(" . $this->escapePdfString($line) . ") Tj\n";
                $content .= "0 -20 Td\n";
            }
        }
        
        $pdfContent .= "4 0 obj\n";
        $pdfContent .= "<<\n";
        $pdfContent .= "/Length " . strlen($content) . "\n";
        $pdfContent .= ">>\n";
        $pdfContent .= "stream\n";
        $pdfContent .= $content;
        $pdfContent .= "endstream\n";
        $pdfContent .= "endobj\n";
        
        // Add page numbering
        $pdfContent .= "5 0 obj\n";
        $pdfContent .= "<<\n";
        $pdfContent .= "/Type /Font\n";
        $pdfContent .= "/Subtype /Type1\n";
        $pdfContent .= "/BaseFont /Helvetica\n";
        $pdfContent .= ">>\n";
        $pdfContent .= "endobj\n";
        
        // Add xref table
        $pdfContent .= "xref\n";
        $pdfContent .= "0 6\n";
        $pdfContent .= "0000000000 65535 f \n";
        $pdfContent .= "0000000009 00000 n \n";
        $pdfContent .= "0000000058 00000 n \n";
        $pdfContent .= "0000000117 00000 n \n";
        $pdfContent .= "0000000200 00000 n \n";
        $pdfContent .= "0000000300 00000 n \n";
        
        // Add trailer
        $pdfContent .= "trailer\n";
        $pdfContent .= "<<\n";
        $pdfContent .= "/Size 6\n";
        $pdfContent .= "/Root 1 0 R\n";
        $pdfContent .= ">>\n";
        $pdfContent .= "startxref\n";
        $pdfContent .= strlen($pdfContent) . "\n";
        $pdfContent .= "%%EOF\n";
        
        return $pdfContent;
    }

    /**
     * Escape PDF string
     */
    private function escapePdfString($string)
    {
        return str_replace(['(', ')', '\\'], ['\\(', '\\)', '\\\\'], $string);
    }

    /**
     * Format date
     */
    private function formatDate($date)
    {
        if (!$date) {
            return date('Y-m-d');
        }
        
        if (is_string($date)) {
            $timestamp = strtotime($date);
            if ($timestamp !== false) {
                return date('Y-m-d', $timestamp);
            }
        }
        
        if (method_exists($date, 'format')) {
            return $date->format('Y-m-d');
        }
        
        return date('Y-m-d');
    }

    /**
     * Get default CSS
     */
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
            .page-numbering {
                position: fixed;
                bottom: 20px;
                left: 50%;
                transform: translateX(-50%);
                font-size: 10px;
                color: #666;
                text-align: center;
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
            [lang=\"AR-EG\"] {
                font-family: 'Tahoma', 'Arial', sans-serif;
                direction: rtl;
                text-align: right;
            }
            [dir=\"rtl\"] {
                direction: rtl;
                text-align: right;
            }
            [dir=\"ltr\"] {
                direction: ltr;
                text-align: left;
            }
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
            .no-break {
                page-break-inside: avoid;
                break-inside: avoid;
            }
            .force-page-break {
                page-break-before: always;
                break-before: page;
            }
        ";
    }
}
