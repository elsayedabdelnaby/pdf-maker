@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Template: {{ $pdfTemplate->name }}</h4>
                    <div>
                        <a href="{{ route('pdf-templates.edit', $pdfTemplate) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Edit Template
                        </a>
                        <a href="{{ route('pdf-templates.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Templates
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Template Details</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Name:</strong></td>
                                    <td>{{ $pdfTemplate->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Engine:</strong></td>
                                    <td>{{ ucfirst($pdfTemplate->engine) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Page Size:</strong></td>
                                    <td>{{ $pdfTemplate->page_size }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Orientation:</strong></td>
                                    <td>{{ ucfirst($pdfTemplate->orientation) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>RTL Support:</strong></td>
                                    <td>
                                        @if($pdfTemplate->rtl)
                                            <span class="badge badge-success">Yes (Arabic Support)</span>
                                        @else
                                            <span class="badge badge-secondary">No</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Margins:</strong></td>
                                    <td>
                                        Top: {{ $pdfTemplate->margin_top }}mm, 
                                        Right: {{ $pdfTemplate->margin_right }}mm, 
                                        Bottom: {{ $pdfTemplate->margin_bottom }}mm, 
                                        Left: {{ $pdfTemplate->margin_left }}mm
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Created:</strong></td>
                                    <td>{{ $pdfTemplate->created_at->format('Y-m-d H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Updated:</strong></td>
                                    <td>{{ $pdfTemplate->updated_at->format('Y-m-d H:i:s') }}</td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="col-md-6">
                            <h5>Generate PDF</h5>
                            <div class="card">
                                <div class="card-body">
                                    <form id="generatePdfForm" method="GET" target="_blank">
                                        <div class="form-group">
                                            <label for="model_type">Model Type</label>
                                            <select class="form-control" id="model_type" name="model_type" required>
                                                <option value="invoice" selected>Invoice</option>
                                            </select>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="model_id">Model ID</label>
                                            <input type="number" class="form-control" id="model_id" name="model_id" 
                                                   placeholder="Enter the ID of the record" required>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-success btn-block">
                                            <i class="fas fa-file-pdf"></i> Generate PDF (New Method)
                                        </button>
                                        
                                        <button type="button" class="btn btn-info btn-block mt-2" onclick="testOldMethod()">
                                            <i class="fas fa-file-pdf"></i> Test Old Method (WKHTMLTOPDF)
                                        </button>
                                        
                                        <hr class="my-3">
                                        
                                        <h6 class="text-primary">DomPDF Generation Options:</h6>
                                        
                                        <button type="button" class="btn btn-success btn-block mt-2" onclick="generateDomPdf()">
                                            <i class="fas fa-file-pdf"></i> Generate PDF with DomPDF (Download)
                                        </button>
                                        
                                        <button type="button" class="btn btn-warning btn-block mt-2" onclick="generateDomPdfInline()">
                                            <i class="fas fa-eye"></i> Preview PDF with DomPDF (Inline)
                                        </button>
                                        
                                        <button type="button" class="btn btn-secondary btn-block mt-2" onclick="debugDomPdf()">
                                            <i class="fas fa-bug"></i> Debug DomPDF Generation
                                        </button>
                                        
                                        <hr class="my-3">
                                        
                                        <h6 class="text-info">Custom Page Numbering:</h6>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <select class="form-control mb-2" id="pageNumberingPosition">
                                                    <option value="bottom">Bottom</option>
                                                    <option value="top">Top</option>
                                                    <option value="header">Below Header</option>
                                                    <option value="footer">Above Footer</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <select class="form-control mb-2" id="pageNumberingStyle">
                                                    <option value="centered">Centered</option>
                                                    <option value="left">Left Aligned</option>
                                                    <option value="right">Right Aligned</option>
                                                    <option value="simple">Simple Number</option>
                                                    <option value="default">Default</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <button type="button" class="btn btn-info btn-block mt-2" onclick="generateCustomDomPdf()">
                                            <i class="fas fa-cog"></i> Generate with Custom Page Numbering
                                        </button>
                                    </form>
                                    
                                    <script>
                                        document.getElementById('generatePdfForm').addEventListener('submit', function(e) {
                                            e.preventDefault();
                                            
                                            const templateId = {{ $pdfTemplate->id }};
                                            const modelType = document.getElementById('model_type').value;
                                            const modelId = document.getElementById('model_id').value;
                                            
                                            if (!modelId) {
                                                alert('Please enter a Model ID');
                                                return;
                                            }
                                            
                                            // Use the new export route for better reliability
                                            const url = `/export-pdf/${templateId}/invoice/${modelId}`;
                                            
                                            // Open in new tab
                                            window.open(url, '_blank');
                                        });
                                        
                                        function testOldMethod() {
                                            const templateId = {{ $pdfTemplate->id }};
                                            const modelId = document.getElementById('model_id').value;
                                            
                                            if (!modelId) {
                                                alert('Please enter a Model ID');
                                                return;
                                            }
                                            
                                            // Use the old WKHTMLTOPDF method
                                            const url = `/generate-pdf/${templateId}/invoice/${modelId}`;
                                            window.open(url, '_blank');
                                        }
                                        
                                        function generateDomPdf() {
                                            const templateId = {{ $pdfTemplate->id }};
                                            const modelId = document.getElementById('model_id').value;
                                            
                                            if (!modelId) {
                                                alert('Please enter a Model ID');
                                                return;
                                            }
                                            
                                            // Use DomPDF for PDF generation
                                            const url = `/dompdf/${templateId}/invoice/${modelId}`;
                                            window.open(url, '_blank');
                                        }
                                        
                                        function generateDomPdfInline() {
                                            const templateId = {{ $pdfTemplate->id }};
                                            const modelId = document.getElementById('model_id').value;
                                            
                                            if (!modelId) {
                                                alert('Please enter a Model ID');
                                                return;
                                            }
                                            
                                            // Use DomPDF for inline PDF preview
                                            const url = `/dompdf-inline/${templateId}/invoice/${modelId}`;
                                            window.open(url, '_blank');
                                        }
                                        
                                        function debugDomPdf() {
                                            const templateId = {{ $pdfTemplate->id }};
                                            const modelId = document.getElementById('model_id').value;
                                            
                                            if (!modelId) {
                                                alert('Please enter a Model ID');
                                                return;
                                            }
                                            
                                            // Debug DomPDF generation
                                            const url = `/debug-dompdf/${templateId}/invoice/${modelId}`;
                                            window.open(url, '_blank');
                                        }
                                        
                                        function generateCustomDomPdf() {
                                            const templateId = {{ $pdfTemplate->id }};
                                            const modelId = document.getElementById('model_id').value;
                                            const position = document.getElementById('pageNumberingPosition').value;
                                            const style = document.getElementById('pageNumberingStyle').value;
                                            
                                            if (!modelId) {
                                                alert('Please enter a Model ID');
                                                return;
                                            }
                                            
                                            // Generate PDF with custom page numbering
                                            const url = `/dompdf-custom/${templateId}/invoice/${modelId}/${position}/${style}`;
                                            window.open(url, '_blank');
                                        }
                                    </script>
                                </div>
                            </div>
                            
                            <!-- Placeholder Reference -->
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h6 class="mb-0">
                                        <i class="fas fa-info-circle"></i> Available Placeholders Reference
                                        <button class="btn btn-sm btn-outline-secondary float-end" type="button" 
                                                data-bs-toggle="collapse" data-bs-target="#placeholderReference">
                                            Show/Hide
                                        </button>
                                    </h6>
                                </div>
                                <div class="collapse" id="placeholderReference">
                                    <div class="card-body">
                                        <!-- Basic Invoice Fields -->
                                        <div class="mb-3">
                                            <h6 class="text-primary">Basic Invoice Fields:</h6>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <ul class="list-unstyled small">
                                                        <li><code>@{{company_name}}</code> - Company name</li>
                                                        <li><code>@{{invoice_number}}</code> - Invoice number</li>
                                                        <li><code>@{{date}}</code> - Invoice date</li>
                                                        <li><code>@{{amount}}</code> - Total amount</li>
                                                        <li><code>@{{customer_name}}</code> - Customer name</li>
                                                        <li><code>@{{subject}}</code> - Subject</li>
                                                        <li><code>@{{contract_number}}</code> - Contract number</li>
                                                        <li><code>@{{sales_order}}</code> - Sales order</li>
                                                        <li><code>@{{customer_no}}</code> - Customer number</li>
                                                        <li><code>@{{due_date}}</code> - Due date</li>
                                                        <li><code>@{{contract_date}}</code> - Contract date</li>
                                                        <li><code>@{{purchase_order}}</code> - Purchase order</li>
                                                        <li><code>@{{subtotal}}</code> - Subtotal</li>
                                                        <li><code>@{{pre_tax_total}}</code> - Pre-tax total</li>
                                                        <li><code>@{{tax_type}}</code> - Tax type</li>
                                                        <li><code>@{{discount_percent}}</code> - Discount percent</li>
                                                        <li><code>@{{discount_amount}}</code> - Discount amount</li>
                                                        <li><code>@{{shipping_amount}}</code> - Shipping amount</li>
                                                        <li><code>@{{shipping_percent}}</code> - Shipping percent</li>
                                                        <li><code>@{{received}}</code> - Amount received</li>
                                                        <li><code>@{{balance}}</code> - Balance</li>
                                                        <li><code>@{{currency}}</code> - Currency</li>
                                                        <li><code>@{{conversion_rate}}</code> - Conversion rate</li>
                                                        <li><code>@{{terms_conditions}}</code> - Terms & conditions</li>
                                                        <li><code>@{{status}}</code> - Invoice status</li>
                                                    </ul>
                                                </div>
                                                <div class="col-md-6">
                                                    <ul class="list-unstyled small">
                                                        <li><code>@{{model_type}}</code> - Model type</li>
                                                        <li><code>@{{model_id}}</code> - Invoice ID</li>
                                                        <li><code>@{{page_number}}</code> - Current page number</li>
                                                        <li><code>@{{total_pages}}</code> - Total pages</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Custom Fields -->
                                        <div class="mb-3">
                                            <h6 class="text-success">Custom Fields (CF):</h6>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <ul class="list-unstyled small">
                                                        <li><code>@{{check_date}}</code> - Check date</li>
                                                        <li><code>@{{unit_type}}</code> - Unit type</li>
                                                        <li><code>@{{tax_start_date}}</code> - Tax start date</li>
                                                        <li><code>@{{next_date}}</code> - Next date</li>
                                                        <li><code>@{{contract_date}}</code> - Contract date</li>
                                                        <li><code>@{{partial_invoice}}</code> - Partial invoice</li>
                                                        <li><code>@{{payment_plan}}</code> - Payment plan</li>
                                                        <li><code>@{{balance_adjustment}}</code> - Balance adjustment</li>
                                                        <li><code>@{{currency_type}}</code> - Currency type</li>
                                                        <li><code>@{{next_follow_details}}</code> - Next follow details</li>
                                                        <li><code>@{{developer_name}}</code> - Developer name</li>
                                                        <li><code>@{{unit_no}}</code> - Unit number</li>
                                                        <li><code>@{{company_percent}}</code> - Company percent</li>
                                                        <li><code>@{{unit_total_price}}</code> - Unit total price</li>
                                                        <li><code>@{{unit_area}}</code> - Unit area</li>
                                                        <li><code>@{{floors_count}}</code> - Number of floors</li>
                                                        <li><code>@{{incentive_percent}}</code> - Incentive percent</li>
                                                        <li><code>@{{unit_number}}</code> - Unit number</li>
                                                        <li><code>@{{advance_payment}}</code> - Advance payment</li>
                                                        <li><code>@{{incentive_claim_date}}</code> - Incentive claim date</li>
                                                        <li><code>@{{incentive_man_percent}}</code> - Incentive man percent</li>
                                                        <li><code>@{{manager_id}}</code> - Manager ID</li>
                                                        <li><code>@{{manager_name}}</code> - Manager name</li>
                                                        <li><code>@{{client_name}}</code> - Client name</li>
                                                    </ul>
                                                </div>
                                                <div class="col-md-6">
                                                    <ul class="list-unstyled small">
                                                        <li><code>@{{eight_years}}</code> - 8 years</li>
                                                        <li><code>@{{incentive_man_claim_date}}</code> - Incentive man claim date</li>
                                                        <li><code>@{{ten_years}}</code> - 10 years</li>
                                                        <li><code>@{{sixteen_years_20}}</code> - 16 years 20%</li>
                                                        <li><code>@{{sixteen_years_10}}</code> - 16 years 10%</li>
                                                        <li><code>@{{sixteen_years_15}}</code> - 16 years 15%</li>
                                                        <li><code>@{{twelve_years}}</code> - 12 years</li>
                                                        <li><code>@{{net_value}}</code> - Net value</li>
                                                        <li><code>@{{delivery_month}}</code> - Delivery month</li>
                                                        <li><code>@{{eighty_eight_percent}}</code> - 88%</li>
                                                        <li><code>@{{garden}}</code> - Garden</li>
                                                        <li><code>@{{building_no}}</code> - Building number</li>
                                                        <li><code>@{{unit_price_in_words}}</code> - Unit price in words</li>
                                                        <li><code>@{{remaining_balance}}</code> - Remaining balance</li>
                                                        <li><code>@{{remaining_balance_amount}}</code> - Remaining balance amount</li>
                                                        <li><code>@{{reservation_payment}}</code> - Reservation payment</li>
                                                        <li><code>@{{reservation_payment_amount}}</code> - Reservation payment amount</li>
                                                        <li><code>@{{payment_day}}</code> - Payment day</li>
                                                        <li><code>@{{payment_month}}</code> - Payment month</li>
                                                        <li><code>@{{maintenance_amount}}</code> - Maintenance amount</li>
                                                        <li><code>@{{maintenance_amount_value}}</code> - Maintenance amount value</li>
                                                        <li><code>@{{amount_3}}</code> - Amount 3</li>
                                                        <li><code>@{{amount_3_value}}</code> - Amount 3 value</li>
                                                        <li><code>@{{amount_4}}</code> - Amount 4</li>
                                                        <li><code>@{{amount_4_value}}</code> - Amount 4 value</li>
                                                        <li><code>@{{unit_area_in_words}}</code> - Unit area in words</li>
                                                        <li><code>@{{delivery_year}}</code> - Delivery year</li>
                                                        <li><code>@{{day_number}}</code> - Day number</li>
                                                        <li><code>@{{advance_adjustment}}</code> - Advance adjustment</li>
                                                        <li><code>@{{garden_area_in_contract}}</code> - Garden area in contract</li>
                                                        <li><code>@{{confirm}}</code> - Confirm</li>
                                                        <li><code>@{{confirm_comment}}</code> - Confirm comment</li>
                                                        <li><code>@{{project_name}}</code> - Project name</li>
                                                        <li><code>@{{project_category}}</code> - Project category</li>
                                                        <li><code>@{{project_location}}</code> - Project location</li>
                                                        <li><code>@{{unit_category}}</code> - Unit category</li>
                                                        <li><code>@{{contract_status}}</code> - Contract status</li>
                                                        <li><code>@{{payment_plan_module}}</code> - Payment plan module</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="alert alert-info">
                                            <strong>Tip:</strong> Use these placeholders in your template content. They will be automatically replaced with actual invoice data when generating PDFs.
                                        </div>
                                        
                                        <div class="alert alert-success">
                                            <strong>Page Numbering:</strong> Page numbers are automatically added to every page in the format "Page X of Y" using WKHTMLTOPDF's native footer support. You can also use <code>&lt;div class="page-break"&gt;&lt;/div&gt;</code> to force new pages.
                                        </div>
                                        
                                        <div class="alert alert-warning">
                                            <strong>Footer on All Pages:</strong> Your footer content will automatically appear on every page using WKHTMLTOPDF's native footer support. Use the footer field to add content that should appear on all pages (company information, contact details, terms & conditions).
                                        </div>
                                        
                                        <small class="text-muted">Note: Currently supporting Invoice model only. Enter the Invoice ID to generate PDF. Use these placeholders in your template content.</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Template Preview</h5>
                            <div class="card">
                                <div class="card-header">
                                    <ul class="nav nav-tabs card-header-tabs" id="templateTabs" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" id="body-tab" data-bs-toggle="tab" href="#body" role="tab">
                                                Main Content
                                            </a>
                                        </li>
                                        @if($pdfTemplate->header_html)
                                            <li class="nav-item">
                                                <a class="nav-link" id="header-tab" data-bs-toggle="tab" href="#header" role="tab">
                                                    Header
                                                </a>
                                            </li>
                                        @endif
                                        @if($pdfTemplate->footer_html)
                                            <li class="nav-item">
                                                <a class="nav-link" id="footer-tab" data-bs-toggle="tab" href="#footer" role="tab">
                                                    Footer
                                                </a>
                                            </li>
                                        @endif
                                        @if($pdfTemplate->css)
                                            <li class="nav-item">
                                                <a class="nav-link" id="css-tab" data-bs-toggle="tab" href="#css" role="tab">
                                                    CSS
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                                <div class="card-body">
                                    <div class="tab-content" id="templateTabsContent">
                                        <div class="tab-pane fade show active" id="body" role="tabpanel">
                                            <div class="border p-3 bg-light" style="direction: {{ $pdfTemplate->rtl ? 'rtl' : 'ltr' }};">
                                                {!! $pdfTemplate->body_html !!}
                                            </div>
                                        </div>
                                        @if($pdfTemplate->header_html)
                                            <div class="tab-pane fade" id="header" role="tabpanel">
                                                <div class="border p-3 bg-light" style="direction: {{ $pdfTemplate->rtl ? 'rtl' : 'ltr' }};">
                                                    {!! $pdfTemplate->header_html !!}
                                                </div>
                                            </div>
                                        @endif
                                        @if($pdfTemplate->footer_html)
                                            <div class="tab-pane fade" id="footer" role="tabpanel">
                                                <div class="border p-3 bg-light" style="direction: {{ $pdfTemplate->rtl ? 'rtl' : 'ltr' }};">
                                                    {!! $pdfTemplate->footer_html !!}
                                                </div>
                                            </div>
                                        @endif
                                        @if($pdfTemplate->css)
                                            <div class="tab-pane fade" id="css" role="tabpanel">
                                                <pre><code>{{ $pdfTemplate->css }}</code></pre>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Update form action when model type changes
    document.getElementById('model_type').addEventListener('change', function() {
        const modelType = this.value;
        const form = this.closest('form');
        const action = form.action.split('/');
        action[action.length - 2] = modelType;
        form.action = action.join('/');
    });
});
</script>
@endsection
