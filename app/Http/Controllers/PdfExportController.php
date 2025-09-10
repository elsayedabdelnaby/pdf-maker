<?php

namespace App\Http\Controllers;

use App\Models\PdfTemplate;
use App\Models\Invoice;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Barryvdh\DomPDF\Facade\Pdf;
use ArPHP\I18N\Arabic;

class PdfExportController extends Controller
{
    /**
     * Generate PDF using DomPDF
     */
    public function generatePdf(Request $request, $templateId, $modelId)
    {
        try {
            $template = PdfTemplate::findOrFail($templateId);
            $invoice = Invoice::with([
                'invoiceCF',
                'contact',
                'contactCF',
                'paymentPlan',
                'paymentPlanCF',
                'paymentPlan.checksCF.crmEntity'
            ])->where('invoiceid', $modelId)->first();
            
            if (!$invoice) {
                return response()->json(['error' => 'Invoice not found'], 404);
            }

            // Generate HTML content
            $html = $this->generateHtml($template, $invoice);
            
            if (empty($html)) {
                return response()->json(['error' => 'HTML content is empty'], 500);
            }

            // Configure DomPDF options
            $pdf = Pdf::loadHTML($html);
            
            // Set paper size and orientation
            $pdf->setPaper($template->page_size, strtolower($template->orientation));
            
            // Set options with Arabic support
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => $template->rtl ? 'DejaVu Sans' : 'Arial',
                'dpi' => 96,
                'isFontSubsettingEnabled' => true,
                'isUnicode' => true,
                'debugKeepTemp' => false,
                'debugCss' => false,
                'debugLayout' => false,
                'debugLayoutLines' => false,
                'debugLayoutBlocks' => false,
                'debugLayoutInline' => false,
                'debugLayoutPaddingBox' => false,
                'fontCache' => storage_path('fonts/'),
                'tempDir' => storage_path('temp/'),
                'chroot' => public_path(),
                'logOutputFile' => storage_path('logs/dompdf.log'),
                'defaultMediaType' => 'print',
                'defaultPaperSize' => $template->page_size,
                'defaultPaperOrientation' => strtolower($template->orientation),
            ]);

            $filename = 'invoice_' . $modelId . '_' . date('Y-m-d_H-i-s') . '.pdf';
            
            return $pdf->download($filename);
            
        } catch (\Exception $e) {
            Log::error('PDF generation failed: ' . $e->getMessage());
            return response()->json([
                'error' => 'PDF generation failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test Arabic text processing
     */
    public function testArabicText(Request $request)
    {
        try {
            $testTexts = [
                'English text' => 'Hello World',
                'Arabic text' => 'مرحبا بالعالم',
                'Mixed text' => 'Hello مرحبا World',
                'Arabic numbers' => '١٢٣٤٥٦٧٨٩٠',
                'Arabic with English' => 'اسم المستخدم: John Doe',
            ];
            
            $results = [];
            foreach ($testTexts as $label => $text) {
                $processed = $this->processArabicText($text);
                $results[$label] = [
                    'original' => $text,
                    'processed' => $processed,
                    'contains_arabic' => preg_match('/[\x{0600}-\x{06FF}]/u', $text),
                ];
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Arabic text processing test completed',
                'results' => $results,
                'package_loaded' => class_exists('ArPHP\I18N\Arabic'),
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Arabic text test failed',
                'message' => $e->getMessage(),
                'package_loaded' => class_exists('ArPHP\I18N\Arabic'),
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
            $invoice = Invoice::with([
                'invoiceCF',
                'contact',
                'contactCF',
                'paymentPlan',
                'paymentPlanCF',
                'paymentPlan.checksCF.crmEntity'
            ])->where('invoiceid', $modelId)->first();
            
            if (!$invoice) {
                return response()->json(['error' => 'Invoice not found'], 404);
            }

            // Generate HTML content
            $html = $this->generateHtml($template, $invoice);
            
            // Get company info safely
            $company = Company::where('organization_id', 1)->first();
            $companyName = $company ? $company->organizationname : 'Not found';
            
            // Get checks data
            $checksData = [];
            if ($invoice->paymentPlan && $invoice->paymentPlan->checksCF) {
                $checksData = $invoice->paymentPlan->checksCF->where('crmEntity.deleted', 0)->map(function($check) {
                    return [
                        'id' => $check->checksid,
                        'bank_name' => $check->cf_1723 ?? '',
                        'check_number' => $check->cf_1711 ?? '',
                        'amount' => $check->cf_1721 ?? '',
                        'collection_date' => $check->cf_1703 ?? '',
                        'status' => $check->cf_1713 ?? '',
                        'note' => $check->cf_1715 ?? '',
                        'customer_name' => $check->cf_1717 ?? '',
                        'unit_number' => $check->cf_1719 ?? '',
                    ];
                })->toArray();
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
                    'invoicedate' => $invoice->invoicedate ? $invoice->invoicedate : '',
                    'customer' => $invoice->contact ? $invoice->contact->lastname : 'No customer',
                    'payment_plan_id' => $invoice->paymentPlan ? $invoice->paymentPlan->paymentplansid : 'No payment plan'
                ],
                'checks_count' => count($checksData),
                'checks_data' => $checksData,
                'generated_html' => [
                    'length' => strlen($html),
                    'preview' => substr($html, 0, 500) . '...'
                ],
                'company' => $companyName,
                'method' => 'DomPDF Export Controller'
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
        
        // Get contact information
        $contact = $invoice->contact;
        $contactCF = $invoice->contactCF;
        
        // Get payment plan information
        $paymentPlan = $invoice->paymentPlan;
        $paymentPlanCF = $invoice->paymentPlanCF;
        
        // Get checks information
        $checks = collect([]);
        if ($paymentPlan && $paymentPlan->checksCF) {
            $checks = $paymentPlan->checksCF->where('crmEntity.deleted', 0);
        }
        
        // Replace placeholders
        $replacements = [
            // Basic Invoice Fields (with and without spaces)
            '{{company_name}}' => $company ? $company->organizationname : 'Company Name',
            '{{ company_name }}' => $company ? $company->organizationname : 'Company Name',
            '{{invoice_number}}' => $invoice->invoice_no ? $invoice->invoice_no : ($invoice->invoiceid ? $invoice->invoiceid : ''),
            '{{ invoice_number }}' => $invoice->invoice_no ? $invoice->invoice_no : ($invoice->invoiceid ? $invoice->invoiceid : ''),
            '{{date}}' => $this->formatDate($invoice->invoicedate),
            '{{ date }}' => $this->formatDate($invoice->invoicedate),
            '{{amount}}' => $invoice->total ? $invoice->total : '0.00',
            '{{ amount }}' => $invoice->total ? $invoice->total : '0.00',
            '{{customer_name}}' => $contact ? $contact->lastname : 'Customer',
            '{{ customer_name }}' => $contact ? $contact->lastname : 'Customer',
            '{{model_type}}' => 'Invoice',
            '{{ model_type }}' => 'Invoice',
            '{{model_id}}' => $invoice->invoiceid ? $invoice->invoiceid : $invoice->id,
            '{{ model_id }}' => $invoice->invoiceid ? $invoice->invoiceid : $invoice->id,
            
            // Additional Invoice Fields
            '{{subject}}' => $invoice->subject ?: '',
            '{{ subject }}' => $invoice->subject ?: '',
            '{{contract_number}}' => $invoice->contract_number ?: '',
            '{{ contract_number }}' => $invoice->contract_number ?: '',
            '{{sales_order}}' => $invoice->salesorderid ?: '',
            '{{ sales_order }}' => $invoice->salesorderid ?: '',
            '{{customer_no}}' => $invoice->customerno ?: '',
            '{{ customer_no }}' => $invoice->customerno ?: '',
            '{{due_date}}' => $this->formatDate($invoice->duedate),
            '{{ due_date }}' => $this->formatDate($invoice->duedate),
            '{{purchase_order}}' => $invoice->purchaseorder ?: '',
            '{{ purchase_order }}' => $invoice->purchaseorder ?: '',
            '{{subtotal}}' => $invoice->subtotal ?: '0.00',
            '{{ subtotal }}' => $invoice->subtotal ?: '0.00',
            '{{pre_tax_total}}' => $invoice->pre_tax_total ?: '0.00',
            '{{ pre_tax_total }}' => $invoice->pre_tax_total ?: '0.00',
            '{{tax_type}}' => $invoice->taxtype ?: '',
            '{{ tax_type }}' => $invoice->taxtype ?: '',
            '{{discount_percent}}' => $invoice->discount_percent ?: '0',
            '{{ discount_percent }}' => $invoice->discount_percent ?: '0',
            '{{discount_amount}}' => $invoice->discount_amount ?: '0.00',
            '{{ discount_amount }}' => $invoice->discount_amount ?: '0.00',
            '{{shipping_amount}}' => $invoice->s_h_amount ?: '0.00',
            '{{ shipping_amount }}' => $invoice->s_h_amount ?: '0.00',
            '{{shipping_percent}}' => $invoice->s_h_percent ?: '0',
            '{{ shipping_percent }}' => $invoice->s_h_percent ?: '0',
            '{{received}}' => $invoice->received ?: '0.00',
            '{{ received }}' => $invoice->received ?: '0.00',
            '{{balance}}' => $invoice->balance ?: '0.00',
            '{{ balance }}' => $invoice->balance ?: '0.00',
            '{{currency}}' => $invoice->currency_id ?: '',
            '{{ currency }}' => $invoice->currency_id ?: '',
            '{{conversion_rate}}' => $invoice->conversion_rate ?: '1.00',
            '{{ conversion_rate }}' => $invoice->conversion_rate ?: '1.00',
            '{{terms_conditions}}' => $invoice->terms_conditions ?: '',
            '{{ terms_conditions }}' => $invoice->terms_conditions ?: '',
            '{{status}}' => $invoice->invoicestatus ?: '',
            '{{ status }}' => $invoice->invoicestatus ?: '',
            
            // Contact Fields
            '{{customer_first_name}}' => $contact ? $contact->firstname : '',
            '{{customer_phone}}' => $contact ? $contact->phone : '',
            '{{customer_mobile}}' => $contact ? $contact->mobile : '',
            '{{customer_email}}' => $contact ? $contact->email : '',
            '{{customer_title}}' => $contact ? $contact->title : '',
            '{{customer_department}}' => $contact ? $contact->department : '',
            '{{customer_fax}}' => $contact ? $contact->fax : '',
            '{{customer_secondary_email}}' => $contact ? $contact->secondaryemail : '',
            
            // Contact Custom Fields
            '{{customer_national_id}}' => $contactCF && $contactCF->cf_1282 ?: '',
            '{{ customer_national_id }}' => $contactCF && $contactCF->cf_1282 ?: '',
            '{{customer_type}}' => $contactCF && $contactCF->cf_1284 ?: '',
            '{{ customer_type }}' => $contactCF && $contactCF->cf_1284 ?: '',
            '{{customer_work_field}}' => $contactCF && $contactCF->cf_1393 ?: '',
            '{{ customer_work_field }}' => $contactCF && $contactCF->cf_1393 ?: '',
            '{{customer_amount_paid}}' => $contactCF && $contactCF->cf_1395 ?: '',
            '{{ customer_amount_paid }}' => $contactCF && $contactCF->cf_1395 ?: '',
            '{{customer_name_arabic}}' => $contactCF && $contactCF->cf_1679 ?: '',
            '{{ customer_name_arabic }}' => $contactCF && $contactCF->cf_1679 ?: '',
            '{{customer_first_degree_name}}' => $contactCF && $contactCF->cf_1681 ?: '',
            '{{ customer_first_degree_name }}' => $contactCF && $contactCF->cf_1681 ?: '',
            '{{customer_first_degree_relation}}' => $contactCF && $contactCF->cf_1683 ?: '',
            '{{ customer_first_degree_relation }}' => $contactCF && $contactCF->cf_1683 ?: '',
            '{{customer_first_degree_cellphone}}' => $contactCF && $contactCF->cf_1685 ?: '',
            '{{ customer_first_degree_cellphone }}' => $contactCF && $contactCF->cf_1685 ?: '',
            '{{customer_first_degree_email}}' => $contactCF && $contactCF->cf_1687 ?: '',
            '{{ customer_first_degree_email }}' => $contactCF && $contactCF->cf_1687 ?: '',
            '{{customer_employment_name}}' => $contactCF && $contactCF->cf_1689 ?: '',
            '{{ customer_employment_name }}' => $contactCF && $contactCF->cf_1689 ?: '',
            
            // Payment Plan Fields
            '{{payment_plan_id}}' => $paymentPlan ? $paymentPlan->paymentplansid : '',
            '{{ payment_plan_id }}' => $paymentPlan ? $paymentPlan->paymentplansid : '',
            '{{payment_options}}' => $paymentPlanCF && $paymentPlanCF->cf_1731 ?: '',
            '{{ payment_options }}' => $paymentPlanCF && $paymentPlanCF->cf_1731 ?: '',
            '{{down_payment}}' => $paymentPlanCF && $paymentPlanCF->cf_1733 ?: '',
            '{{ down_payment }}' => $paymentPlanCF && $paymentPlanCF->cf_1733 ?: '',
            '{{payment_method}}' => $paymentPlanCF && $paymentPlanCF->cf_1735 ?: '',
            '{{ payment_method }}' => $paymentPlanCF && $paymentPlanCF->cf_1735 ?: '',
            '{{unit_area_plan}}' => $paymentPlanCF && $paymentPlanCF->cf_1741 ?: '',
            '{{ unit_area_plan }}' => $paymentPlanCF && $paymentPlanCF->cf_1741 ?: '',
            '{{meter_unit_price}}' => $paymentPlanCF && $paymentPlanCF->cf_1743 ?: '',
            '{{ meter_unit_price }}' => $paymentPlanCF && $paymentPlanCF->cf_1743 ?: '',
            '{{garden_area_plan}}' => $paymentPlanCF && $paymentPlanCF->cf_1745 ?: '',
            '{{ garden_area_plan }}' => $paymentPlanCF && $paymentPlanCF->cf_1745 ?: '',
            '{{garden_meter_price}}' => $paymentPlanCF && $paymentPlanCF->cf_1747 ?: '',
            '{{ garden_meter_price }}' => $paymentPlanCF && $paymentPlanCF->cf_1747 ?: '',
            '{{unit_price_plan}}' => $paymentPlanCF && $paymentPlanCF->cf_1749 ?: '',
            '{{ unit_price_plan }}' => $paymentPlanCF && $paymentPlanCF->cf_1749 ?: '',
            '{{quarterly}}' => $paymentPlanCF && $paymentPlanCF->cf_1761 ?: '',
            '{{ quarterly }}' => $paymentPlanCF && $paymentPlanCF->cf_1761 ?: '',
            '{{half_yearly}}' => $paymentPlanCF && $paymentPlanCF->cf_1763 ?: '',
            '{{ half_yearly }}' => $paymentPlanCF && $paymentPlanCF->cf_1763 ?: '',
            '{{annual}}' => $paymentPlanCF && $paymentPlanCF->cf_1765 ?: '',
            '{{ annual }}' => $paymentPlanCF && $paymentPlanCF->cf_1765 ?: '',
            '{{handover_payment}}' => $paymentPlanCF && $paymentPlanCF->cf_1767 ?: '',
            '{{ handover_payment }}' => $paymentPlanCF && $paymentPlanCF->cf_1767 ?: '',
            '{{year_of_handover_payment}}' => $paymentPlanCF && $paymentPlanCF->cf_1769 ?: '',
            '{{ year_of_handover_payment }}' => $paymentPlanCF && $paymentPlanCF->cf_1769 ?: '',
            '{{maintenance_fee}}' => $paymentPlanCF && $paymentPlanCF->cf_1775 ?: '',
            '{{ maintenance_fee }}' => $paymentPlanCF && $paymentPlanCF->cf_1775 ?: '',
            '{{maintenance_fee_value}}' => $paymentPlanCF && $paymentPlanCF->cf_1777 ?: '',
            '{{ maintenance_fee_value }}' => $paymentPlanCF && $paymentPlanCF->cf_1777 ?: '',
            '{{maintenance_fee_collection_year}}' => $paymentPlanCF && $paymentPlanCF->cf_1779 ?: '',
            '{{ maintenance_fee_collection_year }}' => $paymentPlanCF && $paymentPlanCF->cf_1779 ?: '',
            '{{handover_payment_value}}' => $paymentPlanCF && $paymentPlanCF->cf_1783 ?: '',
            '{{ handover_payment_value }}' => $paymentPlanCF && $paymentPlanCF->cf_1783 ?: '',
            '{{first_installment_date}}' => $paymentPlanCF && $paymentPlanCF->cf_1785 ? $this->formatDate($paymentPlanCF->cf_1785) : '',
            '{{ first_installment_date }}' => $paymentPlanCF && $paymentPlanCF->cf_1785 ? $this->formatDate($paymentPlanCF->cf_1785) : '',
            '{{down_payment_percent}}' => $paymentPlanCF && $paymentPlanCF->cf_1787 ?: '',
            '{{ down_payment_percent }}' => $paymentPlanCF && $paymentPlanCF->cf_1787 ?: '',
            
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
            '{{ unit_area }}' => $invoiceCF && $invoiceCF->cf_1213 ?: '',
            '{{floors_count}}' => $invoiceCF && $invoiceCF->cf_1215 ?: '',
            '{{ floors_count }}' => $invoiceCF && $invoiceCF->cf_1215 ?: '',
            '{{incentive_percent}}' => $invoiceCF && $invoiceCF->cf_1217 ?: '',
            '{{ incentive_percent }}' => $invoiceCF && $invoiceCF->cf_1217 ?: '',
            '{{unit_number}}' => $invoiceCF && $invoiceCF->cf_1219 ?: '',
            '{{ unit_number }}' => $invoiceCF && $invoiceCF->cf_1219 ?: '',
            '{{advance_payment}}' => $invoiceCF && $invoiceCF->cf_1221 ?: '',
            '{{ advance_payment }}' => $invoiceCF && $invoiceCF->cf_1221 ?: '',
            '{{incentive_claim_date}}' => $invoiceCF && $invoiceCF->cf_1223 ? $this->formatDate($invoiceCF->cf_1223) : '',
            '{{ incentive_claim_date }}' => $invoiceCF && $invoiceCF->cf_1223 ? $this->formatDate($invoiceCF->cf_1223) : '',
            '{{incentive_man_percent}}' => $invoiceCF && $invoiceCF->cf_1225 ?: '',
            '{{ incentive_man_percent }}' => $invoiceCF && $invoiceCF->cf_1225 ?: '',
            '{{manager_id}}' => $invoiceCF && $invoiceCF->cf_1227 ?: '',
            '{{ manager_id }}' => $invoiceCF && $invoiceCF->cf_1227 ?: '',
            '{{manager_name}}' => $invoiceCF && $invoiceCF->cf_1229 ?: '',
            '{{ manager_name }}' => $invoiceCF && $invoiceCF->cf_1229 ?: '',
            '{{client_name}}' => $invoiceCF && $invoiceCF->cf_1231 ?: '',
            '{{ client_name }}' => $invoiceCF && $invoiceCF->cf_1231 ?: '',
            '{{eight_years}}' => $invoiceCF && $invoiceCF->cf_1233 ?: '',
            '{{ eight_years }}' => $invoiceCF && $invoiceCF->cf_1233 ?: '',
            '{{incentive_man_claim_date}}' => $invoiceCF && $invoiceCF->cf_1235 ? $this->formatDate($invoiceCF->cf_1235) : '',
            '{{ incentive_man_claim_date }}' => $invoiceCF && $invoiceCF->cf_1235 ? $this->formatDate($invoiceCF->cf_1235) : '',
            '{{ten_years}}' => $invoiceCF && $invoiceCF->cf_1237 ?: '',
            '{{ ten_years }}' => $invoiceCF && $invoiceCF->cf_1237 ?: '',
            '{{sixteen_years_20}}' => $invoiceCF && $invoiceCF->cf_1239 ?: '',
            '{{ sixteen_years_20 }}' => $invoiceCF && $invoiceCF->cf_1239 ?: '',
            '{{sixteen_years_10}}' => $invoiceCF && $invoiceCF->cf_1241 ?: '',
            '{{ sixteen_years_10 }}' => $invoiceCF && $invoiceCF->cf_1241 ?: '',
            '{{sixteen_years_15}}' => $invoiceCF && $invoiceCF->cf_1243 ?: '',
            '{{ sixteen_years_15 }}' => $invoiceCF && $invoiceCF->cf_1243 ?: '',
            '{{twelve_years}}' => $invoiceCF && $invoiceCF->cf_1245 ?: '',
            '{{ twelve_years }}' => $invoiceCF && $invoiceCF->cf_1245 ?: '',
            '{{net_value}}' => $invoiceCF && $invoiceCF->cf_1247 ?: '',
            '{{ net_value }}' => $invoiceCF && $invoiceCF->cf_1247 ?: '',
            '{{delivery_month}}' => $invoiceCF && $invoiceCF->cf_1249 ?: '',
            '{{ delivery_month }}' => $invoiceCF && $invoiceCF->cf_1249 ?: '',
            '{{eighty_eight_percent}}' => $invoiceCF && $invoiceCF->cf_1379 ?: '',
            '{{ eighty_eight_percent }}' => $invoiceCF && $invoiceCF->cf_1379 ?: '',
            '{{garden}}' => $invoiceCF && $invoiceCF->cf_1537 ?: '',
            '{{ garden }}' => $invoiceCF && $invoiceCF->cf_1537 ?: '',
            '{{building_no}}' => $invoiceCF && $invoiceCF->cf_1539 ?: '',
            '{{ building_no }}' => $invoiceCF && $invoiceCF->cf_1539 ?: '',
            '{{unit_price_in_words}}' => $invoiceCF && $invoiceCF->cf_1541 ?: '',
            '{{ unit_price_in_words }}' => $invoiceCF && $invoiceCF->cf_1541 ?: '',
            '{{remaining_balance}}' => $invoiceCF && $invoiceCF->cf_1543 ?: '',
            '{{ remaining_balance }}' => $invoiceCF && $invoiceCF->cf_1543 ?: '',
            '{{remaining_balance_amount}}' => $invoiceCF && $invoiceCF->cf_1545 ?: '',
            '{{ remaining_balance_amount }}' => $invoiceCF && $invoiceCF->cf_1545 ?: '',
            '{{reservation_payment}}' => $invoiceCF && $invoiceCF->cf_1547 ?: '',
            '{{ reservation_payment }}' => $invoiceCF && $invoiceCF->cf_1547 ?: '',
            '{{reservation_payment_amount}}' => $invoiceCF && $invoiceCF->cf_1549 ?: '',
            '{{ reservation_payment_amount }}' => $invoiceCF && $invoiceCF->cf_1549 ?: '',
            '{{payment_day}}' => $invoiceCF && $invoiceCF->cf_1551 ?: '',
            '{{ payment_day }}' => $invoiceCF && $invoiceCF->cf_1551 ?: '',
            '{{payment_month}}' => $invoiceCF && $invoiceCF->cf_1553 ?: '',
            '{{ payment_month }}' => $invoiceCF && $invoiceCF->cf_1553 ?: '',
            '{{maintenance_amount}}' => $invoiceCF && $invoiceCF->cf_1555 ?: '',
            '{{ maintenance_amount }}' => $invoiceCF && $invoiceCF->cf_1555 ?: '',
            '{{maintenance_amount_value}}' => $invoiceCF && $invoiceCF->cf_1557 ?: '',
            '{{ maintenance_amount_value }}' => $invoiceCF && $invoiceCF->cf_1557 ?: '',
            '{{amount_3}}' => $invoiceCF && $invoiceCF->cf_1559 ?: '',
            '{{ amount_3 }}' => $invoiceCF && $invoiceCF->cf_1559 ?: '',
            '{{amount_3_value}}' => $invoiceCF && $invoiceCF->cf_1561 ?: '',
            '{{ amount_3_value }}' => $invoiceCF && $invoiceCF->cf_1561 ?: '',
            '{{amount_4}}' => $invoiceCF && $invoiceCF->cf_1563 ?: '',
            '{{ amount_4 }}' => $invoiceCF && $invoiceCF->cf_1563 ?: '',
            '{{amount_4_value}}' => $invoiceCF && $invoiceCF->cf_1565 ?: '',
            '{{ amount_4_value }}' => $invoiceCF && $invoiceCF->cf_1565 ?: '',
            '{{unit_area_in_words}}' => $invoiceCF && $invoiceCF->cf_1567 ?: '',
            '{{ unit_area_in_words }}' => $invoiceCF && $invoiceCF->cf_1567 ?: '',
            '{{delivery_year}}' => $invoiceCF && $invoiceCF->cf_1569 ?: '',
            '{{ delivery_year }}' => $invoiceCF && $invoiceCF->cf_1569 ?: '',
            '{{day_number}}' => $invoiceCF && $invoiceCF->cf_1573 ?: '',
            '{{ day_number }}' => $invoiceCF && $invoiceCF->cf_1573 ?: '',
            '{{advance_adjustment}}' => $invoiceCF && $invoiceCF->cf_1575 ?: '',
            '{{ advance_adjustment }}' => $invoiceCF && $invoiceCF->cf_1575 ?: '',
            '{{garden_area_in_contract}}' => $invoiceCF && $invoiceCF->cf_1591 ?: '',
            '{{ garden_area_in_contract }}' => $invoiceCF && $invoiceCF->cf_1591 ?: '',
            '{{confirm}}' => $invoiceCF && $invoiceCF->cf_1629 ?: '',
            '{{ confirm }}' => $invoiceCF && $invoiceCF->cf_1629 ?: '',
            '{{confirm_comment}}' => $invoiceCF && $invoiceCF->cf_1631 ?: '',
            '{{ confirm_comment }}' => $invoiceCF && $invoiceCF->cf_1631 ?: '',
            '{{project_name}}' => $invoiceCF && $invoiceCF->cf_1669 ?: '',
            '{{ project_name }}' => $invoiceCF && $invoiceCF->cf_1669 ?: '',
            '{{project_category}}' => $invoiceCF && $invoiceCF->cf_1671 ?: '',
            '{{ project_category }}' => $invoiceCF && $invoiceCF->cf_1671 ?: '',
            '{{project_location}}' => $invoiceCF && $invoiceCF->cf_1673 ?: '',
            '{{ project_location }}' => $invoiceCF && $invoiceCF->cf_1673 ?: '',
            '{{unit_category}}' => $invoiceCF && $invoiceCF->cf_1675 ?: '',
            '{{ unit_category }}' => $invoiceCF && $invoiceCF->cf_1675 ?: '',
            '{{contract_status}}' => $invoiceCF && $invoiceCF->cf_1677 ?: '',
            '{{ contract_status }}' => $invoiceCF && $invoiceCF->cf_1677 ?: '',
            '{{payment_plan_module}}' => $invoiceCF && $invoiceCF->cf_1789 ?: '',
            '{{ payment_plan_module }}' => $invoiceCF && $invoiceCF->cf_1789 ?: '',
        ];

        // Process Arabic text in replacements
        $processedReplacements = [];
        foreach ($replacements as $key => $value) {
            $processedReplacements[$key] = $this->processArabicText($value);
        }
        
        $html = str_replace(array_keys($processedReplacements), array_values($processedReplacements), $html);

        // Handle checks table if present (with and without spaces)
        if (strpos($html, '{{checks_table}}') !== false) {
            $checksTable = $this->generateChecksTable($checks);
            $html = str_replace('{{checks_table}}', $checksTable, $html);
        }
        if (strpos($html, '{{ checks_table }}') !== false) {
            $checksTable = $this->generateChecksTable($checks);
            $html = str_replace('{{ checks_table }}', $checksTable, $html);
        }

        // Add CSS styling
        $css = $template->css ?: $this->getDefaultCss($template->rtl);
        
        // Combine header, body, and footer
        $fullHtml = '<!DOCTYPE html><html dir="' . ($template->rtl ? 'rtl' : 'ltr') . '">';
        $fullHtml .= '<head><meta charset="UTF-8"><style>' . $css . '</style></head>';
        $fullHtml .= '<body>';
        
        if ($template->header_html) {
            $headerHtml = str_replace(array_keys($processedReplacements), array_values($processedReplacements), $template->header_html);
            $fullHtml .= '<div class="header">' . $headerHtml . '</div>';
        }
        
        $fullHtml .= '<div class="content">' . $html . '</div>';
        
        if ($template->footer_html) {
            $footerHtml = str_replace(array_keys($processedReplacements), array_values($processedReplacements), $template->footer_html);
            $fullHtml .= '<div class="footer">' . $footerHtml . '</div>';
        }
        
        $fullHtml .= '</body></html>';
        
        return $fullHtml;
    }

    /**
     * Generate checks table HTML
     */
    private function generateChecksTable($checks)
    {
        if ($checks->isEmpty()) {
            return '<p style="text-align: center; color: #666; font-style: italic;">No checks found for this payment plan.</p>';
        }

        $table = '<table class="checks-table" style="width: 100%; border-collapse: collapse; margin: 20px 0; font-size: 10px;">';
        $table .= '<thead>';
        $table .= '<tr>';
        $table .= '<th style="border: 1px solid #ddd; padding: 8px; background-color: #e8f4f8; font-weight: bold; text-align: center;">Check ID</th>';
        $table .= '<th style="border: 1px solid #ddd; padding: 8px; background-color: #e8f4f8; font-weight: bold; text-align: center;">Bank Name</th>';
        $table .= '<th style="border: 1px solid #ddd; padding: 8px; background-color: #e8f4f8; font-weight: bold; text-align: center;">Check Number</th>';
        $table .= '<th style="border: 1px solid #ddd; padding: 8px; background-color: #e8f4f8; font-weight: bold; text-align: center;">Amount</th>';
        $table .= '<th style="border: 1px solid #ddd; padding: 8px; background-color: #e8f4f8; font-weight: bold; text-align: center;">Collection Date</th>';
        $table .= '<th style="border: 1px solid #ddd; padding: 8px; background-color: #e8f4f8; font-weight: bold; text-align: center;">Status</th>';
        $table .= '<th style="border: 1px solid #ddd; padding: 8px; background-color: #e8f4f8; font-weight: bold; text-align: center;">Customer</th>';
        $table .= '<th style="border: 1px solid #ddd; padding: 8px; background-color: #e8f4f8; font-weight: bold; text-align: center;">Unit</th>';
        $table .= '</tr>';
        $table .= '</thead>';
        $table .= '<tbody>';
        
        foreach ($checks as $check) {
            $table .= '<tr>';
            $table .= '<td style="border: 1px solid #ddd; padding: 6px; text-align: center;">' . ($check->checksid ?: '') . '</td>';
            $table .= '<td style="border: 1px solid #ddd; padding: 6px;">' . ($check->cf_1723 ?: '') . '</td>';
            $table .= '<td style="border: 1px solid #ddd; padding: 6px;">' . ($check->cf_1711 ?: '') . '</td>';
            $table .= '<td style="border: 1px solid #ddd; padding: 6px; text-align: right;">' . ($check->cf_1721 ?: '0.00') . '</td>';
            $table .= '<td style="border: 1px solid #ddd; padding: 6px; text-align: center;">' . ($check->cf_1703 ? $this->formatDate($check->cf_1703) : '') . '</td>';
            $table .= '<td style="border: 1px solid #ddd; padding: 6px; text-align: center;">' . ($check->cf_1713 ?: '') . '</td>';
            $table .= '<td style="border: 1px solid #ddd; padding: 6px;">' . ($check->cf_1717 ?: '') . '</td>';
            $table .= '<td style="border: 1px solid #ddd; padding: 6px; text-align: center;">' . ($check->cf_1719 ?: '') . '</td>';
            $table .= '</tr>';
        }
        
        $table .= '</tbody>';
        $table .= '</table>';
        
        return $table;
    }

    /**
     * Process Arabic text for proper rendering
     */
    private function processArabicText($text)
    {
        if (empty($text)) {
            return $text;
        }
        
        try {
            $arabic = new Arabic();
            
            // Check if text contains Arabic characters
            if (preg_match('/[\x{0600}-\x{06FF}]/u', $text)) {
                // Process Arabic text for proper display
                $text = $arabic->utf8Glyphs($text);
                
                // Add proper HTML attributes for Arabic text
                return '<span dir="rtl" lang="ar" style="font-family: \'Tahoma\', \'Arial Unicode MS\', sans-serif; unicode-bidi: bidi-override;">' . $text . '</span>';
            }
            
            return $text;
        } catch (\Exception $e) {
            Log::warning('Arabic text processing failed: ' . $e->getMessage());
            return $text;
        }
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
                font-family: 'DejaVu Sans', 'Tahoma', 'Arial Unicode MS', 'Arial', sans-serif; 
                direction: {$direction}; 
                text-align: {$textAlign}; 
                margin: 0; 
                padding: 20px; 
                font-size: 12px;
                line-height: 1.4;
                unicode-bidi: bidi-override;
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
            .checks-table {
                font-size: 10px;
            }
            .checks-table th {
                background-color: #e8f4f8;
                font-weight: bold;
                text-align: center;
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
            [lang=\"AR-EG\"], [lang=\"ar\"] {
                font-family: 'DejaVu Sans', 'Tahoma', 'Arial Unicode MS', sans-serif;
                direction: rtl;
                text-align: right;
                unicode-bidi: bidi-override;
            }
            [dir=\"rtl\"] {
                direction: rtl;
                text-align: right;
                unicode-bidi: bidi-override;
            }
            [dir=\"ltr\"] {
                direction: ltr;
                text-align: left;
            }
            span[dir=\"rtl\"], span[lang=\"ar\"] {
                font-family: 'DejaVu Sans', 'Tahoma', 'Arial Unicode MS', sans-serif;
                direction: rtl;
                unicode-bidi: bidi-override;
                display: inline-block;
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