<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PdfTemplate;

class PdfTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Invoice Template (English)
        PdfTemplate::create([
            'name' => 'Standard Invoice Template',
            'engine' => 'handlebars',
            'page_size' => 'A4',
            'orientation' => 'portrait',
            'margin_top' => 20,
            'margin_right' => 15,
            'margin_bottom' => 20,
            'margin_left' => 15,
            'rtl' => false,
            'fonts' => ['Arial', 'Tahoma', 'Times New Roman'],
            'css' => '
                body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
                .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 20px; margin-bottom: 30px; }
                .company-info { margin-bottom: 30px; }
                .invoice-details { margin-bottom: 30px; }
                .customer-info { margin-bottom: 30px; }
                table { width: 100%; border-collapse: collapse; margin: 20px 0; }
                th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
                th { background-color: #f8f9fa; font-weight: bold; }
                .total { text-align: right; font-weight: bold; font-size: 18px; margin-top: 20px; }
                .footer { text-align: center; margin-top: 40px; padding-top: 20px; border-top: 1px solid #ddd; }
            ',
            'header_html' => '<h1>INVOICE</h1>',
            'body_html' => '
                <div class="company-info">
                    <h2>{{company_name}}</h2>
                    <p>123 Business Street<br>City, State 12345<br>Phone: (555) 123-4567</p>
                </div>
                
                <div class="invoice-details">
                    <div style="display: flex; justify-content: space-between;">
                        <div>
                            <strong>Invoice Number:</strong> {{invoice_number}}<br>
                            <strong>Date:</strong> {{date}}
                        </div>
                        <div>
                            <strong>Customer:</strong> {{customer_name}}
                        </div>
                    </div>
                </div>
                
                <table>
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Product/Service Description</td>
                            <td>1</td>
                            <td>${{amount}}</td>
                            <td>${{amount}}</td>
                        </tr>
                    </tbody>
                </table>
                
                <div class="total">
                    <strong>Total Amount: ${{amount}}</strong>
                </div>
                
                <div class="footer">
                    <p>Thank you for your business!</p>
                    <p>Payment is due within 30 days</p>
                </div>
            ',
            'footer_html' => '<p style="text-align: center; color: #666; font-size: 12px;">Page 1 of 1</p>'
        ]);

        // Invoice Template (Arabic/RTL)
        PdfTemplate::create([
            'name' => 'Arabic Invoice Template',
            'engine' => 'handlebars',
            'page_size' => 'A4',
            'orientation' => 'portrait',
            'margin_top' => 20,
            'margin_right' => 15,
            'margin_bottom' => 20,
            'margin_left' => 15,
            'rtl' => true,
            'fonts' => ['Arial', 'Tahoma', 'Times New Roman'],
            'css' => '
                body { font-family: Tahoma, Arial, sans-serif; margin: 0; padding: 20px; direction: rtl; text-align: right; }
                .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 20px; margin-bottom: 30px; }
                .company-info { margin-bottom: 30px; }
                .invoice-details { margin-bottom: 30px; }
                .customer-info { margin-bottom: 30px; }
                table { width: 100%; border-collapse: collapse; margin: 20px 0; }
                th, td { border: 1px solid #ddd; padding: 12px; text-align: right; }
                th { background-color: #f8f9fa; font-weight: bold; }
                .total { text-align: left; font-weight: bold; font-size: 18px; margin-top: 20px; }
                .footer { text-align: center; margin-top: 40px; padding-top: 20px; border-top: 1px solid #ddd; }
            ',
            'header_html' => '<h1>فاتورة</h1>',
            'body_html' => '
                <div class="company-info">
                    <h2>{{company_name}}</h2>
                    <p>123 شارع الأعمال<br>المدينة، الولاية 12345<br>الهاتف: (555) 123-4567</p>
                </div>
                
                <div class="invoice-details">
                    <div style="display: flex; justify-content: space-between;">
                        <div>
                            <strong>رقم الفاتورة:</strong> {{invoice_number}}<br>
                            <strong>التاريخ:</strong> {{date}}
                        </div>
                        <div>
                            <strong>العميل:</strong> {{customer_name}}
                        </div>
                    </div>
                </div>
                
                <table>
                    <thead>
                        <tr>
                            <th>الوصف</th>
                            <th>الكمية</th>
                            <th>سعر الوحدة</th>
                            <th>المجموع</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>وصف المنتج/الخدمة</td>
                            <td>1</td>
                            <td>${{amount}}</td>
                            <td>${{amount}}</td>
                        </tr>
                    </tbody>
                </table>
                
                <div class="total">
                    <strong>المبلغ الإجمالي: ${{amount}}</strong>
                </div>
                
                <div class="footer">
                    <p>شكراً لتعاملكم معنا!</p>
                    <p>الدفع مستحق خلال 30 يوماً</p>
                </div>
            ',
            'footer_html' => '<p style="text-align: center; color: #666; font-size: 12px;">الصفحة 1 من 1</p>'
        ]);

        // Payment Plan Template
        PdfTemplate::create([
            'name' => 'Payment Plan Template',
            'engine' => 'handlebars',
            'page_size' => 'A4',
            'orientation' => 'portrait',
            'margin_top' => 20,
            'margin_right' => 15,
            'margin_bottom' => 20,
            'margin_left' => 15,
            'rtl' => false,
            'fonts' => ['Arial', 'Tahoma', 'Times New Roman'],
            'css' => '
                body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
                .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 20px; margin-bottom: 30px; }
                .plan-details { margin-bottom: 30px; }
                .payment-schedule { margin-bottom: 30px; }
                table { width: 100%; border-collapse: collapse; margin: 20px 0; }
                th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
                th { background-color: #f8f9fa; font-weight: bold; }
                .total { text-align: right; font-weight: bold; font-size: 18px; margin-top: 20px; }
                .footer { text-align: center; margin-top: 40px; padding-top: 20px; border-top: 1px solid #ddd; }
            ',
            'header_html' => '<h1>PAYMENT PLAN</h1>',
            'body_html' => '
                <div class="plan-details">
                    <h2>{{company_name}}</h2>
                    <p><strong>Plan ID:</strong> {{model_id}}<br>
                    <strong>Customer:</strong> {{customer_name}}<br>
                    <strong>Total Amount:</strong> ${{amount}}<br>
                    <strong>Start Date:</strong> {{date}}</p>
                </div>
                
                <div class="payment-schedule">
                    <h3>Payment Schedule</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Payment #</th>
                                <th>Due Date</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>{{date}}</td>
                                <td>${{amount}}</td>
                                <td>Pending</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div class="total">
                    <strong>Total Plan Amount: ${{amount}}</strong>
                </div>
                
                <div class="footer">
                    <p>Please ensure all payments are made on time</p>
                    <p>Contact us for any questions about your payment plan</p>
                </div>
            ',
            'footer_html' => '<p style="text-align: center; color: #666; font-size: 12px;">Payment Plan Document</p>'
        ]);
    }
}
