@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Edit PDF Template: {{ $pdfTemplate->name }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('pdf-templates.update', $pdfTemplate) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Template Name *</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $pdfTemplate->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="engine">Template Engine *</label>
                                    <select class="form-control @error('engine') is-invalid @enderror" 
                                            id="engine" name="engine" required>
                                        <option value="handlebars" {{ old('engine', $pdfTemplate->engine) == 'handlebars' ? 'selected' : '' }}>
                                            Handlebars
                                        </option>
                                        <option value="mustache" {{ old('engine', $pdfTemplate->engine) == 'mustache' ? 'selected' : '' }}>
                                            Mustache
                                        </option>
                                    </select>
                                    @error('engine')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="page_size">Page Size *</label>
                                    <select class="form-control @error('page_size') is-invalid @enderror" 
                                            id="page_size" name="page_size" required>
                                        <option value="A4" {{ old('page_size', $pdfTemplate->page_size) == 'A4' ? 'selected' : '' }}>A4</option>
                                        <option value="A3" {{ old('page_size', $pdfTemplate->page_size) == 'A3' ? 'selected' : '' }}>A3</option>
                                        <option value="Letter" {{ old('page_size', $pdfTemplate->page_size) == 'Letter' ? 'selected' : '' }}>Letter</option>
                                        <option value="Legal" {{ old('page_size', $pdfTemplate->page_size) == 'Legal' ? 'selected' : '' }}>Legal</option>
                                    </select>
                                    @error('page_size')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="orientation">Orientation *</label>
                                    <select class="form-control @error('orientation') is-invalid @enderror" 
                                            id="orientation" name="orientation" required>
                                        <option value="portrait" {{ old('orientation', $pdfTemplate->orientation) == 'portrait' ? 'selected' : '' }}>
                                            Portrait
                                        </option>
                                        <option value="landscape" {{ old('orientation', $pdfTemplate->orientation) == 'landscape' ? 'selected' : '' }}>
                                            Landscape
                                        </option>
                                    </select>
                                    @error('orientation')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox mt-4">
                                        <input type="checkbox" class="custom-control-input" id="rtl" name="rtl" 
                                               {{ old('rtl', $pdfTemplate->rtl) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="rtl">
                                            Right-to-Left (RTL) for Arabic
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">
                                        Enable for Arabic text. Use LTR/RTL buttons in editor for mixed content.
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="margin_top">Top Margin (mm) *</label>
                                    <input type="number" class="form-control @error('margin_top') is-invalid @enderror" 
                                           id="margin_top" name="margin_top" value="{{ old('margin_top', $pdfTemplate->margin_top) }}" 
                                           min="0" required>
                                    @error('margin_top')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="margin_right">Right Margin (mm) *</label>
                                    <input type="number" class="form-control @error('margin_right') is-invalid @enderror" 
                                           id="margin_right" name="margin_right" value="{{ old('margin_right', $pdfTemplate->margin_right) }}" 
                                           min="0" required>
                                    @error('margin_right')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="margin_bottom">Bottom Margin (mm) *</label>
                                    <input type="number" class="form-control @error('margin_bottom') is-invalid @enderror" 
                                           id="margin_bottom" name="margin_bottom" value="{{ old('margin_bottom', $pdfTemplate->margin_bottom) }}" 
                                           min="0" required>
                                    @error('margin_bottom')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="margin_left">Left Margin (mm) *</label>
                                    <input type="number" class="form-control @error('margin_left') is-invalid @enderror" 
                                           id="margin_left" name="margin_left" value="{{ old('margin_left', $pdfTemplate->margin_left) }}" 
                                           min="0" required>
                                    @error('margin_left')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="header_html">Header HTML</label>
                            <textarea class="form-control @error('header_html') is-invalid @enderror" 
                                      id="header_html" name="header_html" rows="3" 
                                      placeholder="Optional header content">{{ old('header_html', $pdfTemplate->header_html) }}</textarea>
                            @error('header_html')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="body_html">Main Template Content *</label>
                            <textarea class="form-control @error('body_html') is-invalid @enderror" 
                                      id="body_html" name="body_html" rows="15" required>{{ old('body_html', $pdfTemplate->body_html) }}</textarea>
                            @error('body_html')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Use placeholders like <code>@{{company_name}}</code>, <code>@{{invoice_number}}</code>, etc.
                            </small>
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
                                    
                                    <div class="alert alert-warning">
                                        <strong>Example Template:</strong><br>
                                        <code>&lt;h1&gt;Invoice: @{{invoice_number}}&lt;/h1&gt;</code><br>
                                        <code>&lt;p&gt;Customer: @{{customer_name}}&lt;/p&gt;</code><br>
                                        <code>&lt;p&gt;Amount: @{{amount}} @{{currency}}&lt;/p&gt;</code><br>
                                        <code>&lt;p&gt;Unit: @{{unit_number}} (@{{unit_area}} m²)&lt;/p&gt;</code>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="footer_html">Footer HTML</label>
                            <textarea class="form-control @error('footer_html') is-invalid @enderror" 
                                      id="footer_html" name="footer_html" rows="3" 
                                      placeholder="Optional footer content">{{ old('footer_html', $pdfTemplate->footer_html) }}</textarea>
                            @error('footer_html')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="css">Custom CSS</label>
                            <textarea class="form-control @error('css') is-invalid @enderror" 
                                      id="css" name="css" rows="8" 
                                      placeholder="Optional custom CSS styles">{{ old('css', $pdfTemplate->css) }}</textarea>
                            @error('css')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Template
                            </button>
                            <a href="{{ route('pdf-templates.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Templates
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- TinyMCE CDN -->
<script src="https://cdn.tiny.cloud/1/4m78n7o3orjvbv6nk85kyfqwbro9vh5uspvkdixdx8z6rjvz/tinymce/8/tinymce.min.js" referrerpolicy="origin" crossorigin="anonymous"></script>
<script>
    tinymce.init({
        selector: '#body_html',
        height: 400,
        plugins: 'advlist anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount directionality',
        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat | ltr rtl directionality',
        directionality: document.getElementById('rtl').checked ? 'rtl' : 'ltr',
        language: 'en',
        menubar: false,
        branding: false,
        promotion: false,
        content_style: 'body { font-family: Arial, Tahoma, sans-serif; }',
        setup: function(editor) {
            // Hide the original textarea after TinyMCE is initialized
            editor.on('init', function() {
                document.getElementById('body_html').style.display = 'none';
            });
            
            // Update direction when RTL checkbox changes
            document.getElementById('rtl').addEventListener('change', function() {
                editor.execCommand('mceDirection', false, this.checked ? 'rtl' : 'ltr');
            });
            
            // Sync TinyMCE content with hidden textarea before form submission
            editor.on('change keyup', function() {
                editor.save(); // This updates the hidden textarea
            });
            
            // Ensure content is synced on form submission
            document.querySelector('form').addEventListener('submit', function() {
                editor.save(); // Save content to hidden textarea before submit
            });
        }
    });

    // Initialize header and footer editors
    tinymce.init({
        selector: '#header_html',
        height: 200,
        plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount directionality',
        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat | ltr rtl directionality',
        directionality: document.getElementById('rtl').checked ? 'rtl' : 'ltr',
        language: 'en',
        menubar: false,
        branding: false,
        promotion: false,
        content_style: 'body { font-family: Arial, Tahoma, sans-serif; }',
        setup: function(editor) {
            // Sync content with textarea
            editor.on('change keyup', function() {
                editor.save();
            });
        }
    });

    tinymce.init({
        selector: '#footer_html',
        height: 200,
        plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount directionality',
        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat | ltr rtl directionality',
        directionality: document.getElementById('rtl').checked ? 'rtl' : 'ltr',
        language: 'en',
        menubar: false,
        branding: false,
        promotion: false,
        content_style: 'body { font-family: Arial, sans-serif; }',
        setup: function(editor) {
            // Sync content with textarea
            editor.on('change keyup', function() {
                editor.save();
            });
        }
    });
</script>
@endsection
