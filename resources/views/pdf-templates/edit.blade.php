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
                                    <div class="form-group mb-3">
                                        <label for="name">Template Name *</label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                            id="name" name="name" value="{{ old('name', $pdfTemplate->name) }}"
                                            required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="engine">Template Engine *</label>
                                        <select class="form-control @error('engine') is-invalid @enderror" id="engine"
                                            name="engine" required>
                                            <option value="handlebars"
                                                {{ old('engine', $pdfTemplate->engine) == 'handlebars' ? 'selected' : '' }}>
                                                Handlebars
                                            </option>
                                            <option value="mustache"
                                                {{ old('engine', $pdfTemplate->engine) == 'mustache' ? 'selected' : '' }}>
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
                                    <div class="form-group mb-3">
                                        <label for="page_size">Page Size *</label>
                                        <select class="form-control @error('page_size') is-invalid @enderror" id="page_size"
                                            name="page_size" required>
                                            <option value="A4"
                                                {{ old('page_size', $pdfTemplate->page_size) == 'A4' ? 'selected' : '' }}>A4
                                            </option>
                                            <option value="A3"
                                                {{ old('page_size', $pdfTemplate->page_size) == 'A3' ? 'selected' : '' }}>A3
                                            </option>
                                            <option value="Letter"
                                                {{ old('page_size', $pdfTemplate->page_size) == 'Letter' ? 'selected' : '' }}>
                                                Letter</option>
                                            <option value="Legal"
                                                {{ old('page_size', $pdfTemplate->page_size) == 'Legal' ? 'selected' : '' }}>
                                                Legal</option>
                                        </select>
                                        @error('page_size')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="orientation">Orientation *</label>
                                        <select class="form-control @error('orientation') is-invalid @enderror"
                                            id="orientation" name="orientation" required>
                                            <option value="portrait"
                                                {{ old('orientation', $pdfTemplate->orientation) == 'portrait' ? 'selected' : '' }}>
                                                Portrait
                                            </option>
                                            <option value="landscape"
                                                {{ old('orientation', $pdfTemplate->orientation) == 'landscape' ? 'selected' : '' }}>
                                                Landscape
                                            </option>
                                        </select>
                                        @error('orientation')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <div class="form-check mt-4">
                                            <input type="checkbox" class="form-check-input" id="rtl" name="rtl"
                                                {{ old('rtl', $pdfTemplate->rtl) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="rtl">
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
                                    <div class="form-group mb-3">
                                        <label for="margin_top">Top Margin (mm) *</label>
                                        <input type="number" class="form-control @error('margin_top') is-invalid @enderror"
                                            id="margin_top" name="margin_top"
                                            value="{{ old('margin_top', $pdfTemplate->margin_top) }}" min="0"
                                            required>
                                        @error('margin_top')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group mb-3">
                                        <label for="margin_right">Right Margin (mm) *</label>
                                        <input type="number"
                                            class="form-control @error('margin_right') is-invalid @enderror"
                                            id="margin_right" name="margin_right"
                                            value="{{ old('margin_right', $pdfTemplate->margin_right) }}" min="0"
                                            required>
                                        @error('margin_right')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group mb-3">
                                        <label for="margin_bottom">Bottom Margin (mm) *</label>
                                        <input type="number"
                                            class="form-control @error('margin_bottom') is-invalid @enderror"
                                            id="margin_bottom" name="margin_bottom"
                                            value="{{ old('margin_bottom', $pdfTemplate->margin_bottom) }}" min="0"
                                            required>
                                        @error('margin_bottom')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group mb-3">
                                        <label for="margin_left">Left Margin (mm) *</label>
                                        <input type="number"
                                            class="form-control @error('margin_left') is-invalid @enderror" id="margin_left"
                                            name="margin_left" value="{{ old('margin_left', $pdfTemplate->margin_left) }}"
                                            min="0" required>
                                        @error('margin_left')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="header_html">Header HTML</label>
                                <textarea class="form-control @error('header_html') is-invalid @enderror" id="header_html" name="header_html"
                                    rows="3" placeholder="Optional header content">{{ old('header_html', $pdfTemplate->header_html) }}</textarea>
                                @error('header_html')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="body_html">Main Template Content *</label>
                                <textarea class="form-control @error('body_html') is-invalid @enderror" id="body_html" name="body_html"
                                    rows="15" required>{{ old('body_html', $pdfTemplate->body_html) }}</textarea>
                                @error('body_html')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    Use placeholders like <code>{<!-- -->{ company_name }}</code>, <code>{<!-- -->{
                                        invoice_number }}</code>, etc.
                                </small>
                            </div>

                            <!-- Same placeholder reference as create.blade.php -->
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
                                        <!-- Include all placeholder references here - same as create.blade.php -->
                                        <div class="alert alert-info">
                                            <strong>Most Common Fields:</strong><br>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <h6>Invoice Fields:</h6>
                                                    <code>&#123;&#123; company_name &#125;&#125;</code>, <code>&#123;&#123; invoice_number &#125;&#125;</code>, <code>&#123;&#123; date &#125;&#125;</code>, <code>&#123;&#123; amount &#125;&#125;</code>, <code>&#123;&#123; customer_name &#125;&#125;</code><br>
                                                    <code>&#123;&#123; subject &#125;&#125;</code>, <code>&#123;&#123; contract_number &#125;&#125;</code>, <code>&#123;&#123; due_date &#125;&#125;</code>, <code>&#123;&#123; subtotal &#125;&#125;</code>, <code>&#123;&#123; balance &#125;&#125;</code>
                                                </div>
                                                <div class="col-md-4">
                                                    <h6>Payment Plan Fields:</h6>
                                                    <code>&#123;&#123; payment_options &#125;&#125;</code>, <code>&#123;&#123; down_payment &#125;&#125;</code>, <code>&#123;&#123; payment_method &#125;&#125;</code><br>
                                                    <code>&#123;&#123; unit_price &#125;&#125;</code>, <code>&#123;&#123; maintenance_fee &#125;&#125;</code>, <code>&#123;&#123; handover_payment &#125;&#125;</code><br>
                                                    <code>&#123;&#123; first_installment_date &#125;&#125;</code>, <code>&#123;&#123; down_payment_percent &#125;&#125;</code><br>
                                                    <code>&#123;&#123; customer_employment_name &#125;&#125;</code>, <code>&#123;&#123; bank_name &#125;&#125;</code>, <code>&#123;&#123; quarterly &#125;&#125;</code><br>
                                                    <code>&#123;&#123; half_yearly &#125;&#125;</code>, <code>&#123;&#123; annual &#125;&#125;</code>, <code>&#123;&#123; year_of_handover_payment &#125;&#125;</code>
                                                </div>
                                                <div class="col-md-4">
                                                    <h6>Project Fields:</h6>
                                                    <code>&#123;&#123; project_name &#125;&#125;</code>, <code>&#123;&#123; unit_number &#125;&#125;</code>, <code>&#123;&#123; unit_area &#125;&#125;</code><br>
                                                    <code>&#123;&#123; contract_date &#125;&#125;</code>, <code>&#123;&#123; developer_name &#125;&#125;</code>, <code>&#123;&#123; unit_type &#125;&#125;</code><br>
                                                    <code>&#123;&#123; project_category &#125;&#125;</code>, <code>&#123;&#123; project_location &#125;&#125;</code>, <code>&#123;&#123; unit_category &#125;&#125;</code><br>
                                                    <code>&#123;&#123; contract_status &#125;&#125;</code>
                                                </div>
                                            </div>
                                            <div class="mt-2">
                                                <strong>Special:</strong> <code>&#123;&#123; checks_table &#125;&#125;</code> - Generates automatic checks table
                                            </div>
                                        </div>

                                        <div class="alert alert-warning">
                                            <strong>Complete Field Reference:</strong><br>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <strong>Invoice Model:</strong><br>
                                                    invoiceid, subject, contract_number, salesorderid, customerno, contactid, invoicedate, duedate, purchaseorder, adjustment, salescommission, exciseduty, subtotal, total, taxtype, discount_percent, discount_amount, s_h_amount, accountid, invoicestatus, currency_id, conversion_rate, terms_conditions, invoice_no, pre_tax_total, received, balance, s_h_percent, potential_id, tags, region_id
                                                </div>
                                                <div class="col-md-4">
                                                    <strong>InvoiceCF Model:</strong><br>
                                                    cf_1183 (Check Date), cf_1185 (Unit Type), cf_1187 (Tax Start Date), cf_1189 (Next Date), cf_1191 (Contract Date), cf_1193 (Partial invoice), cf_1195 (Payment Plan), cf_1197 (تعديل الباقى), cf_1199 (Currency type), cf_1201 (Next Follow Details), cf_1203 (Developer Name), cf_1207 (Unit No), cf_1209 (Company%), cf_1211 (اجمالى ثمن الوحدة), cf_1213 (Unit Area), cf_1215 (No. of Floors), cf_1217 (Incentive%), cf_1219 (Unit Number), cf_1221 (المقدم), cf_1223 (Incentive Caim Date), cf_1225 (Incentive % Man), cf_1227 (Manager ID), cf_1229 (Manager Name), cf_1231 (Client Name), cf_1233 (8 years), cf_1235 (Incentive Man Caim Date), cf_1237 (10 Years), cf_1239 (16 yrs 20%), cf_1241 (16 yrs 10%), cf_1243 (16 yrs 15%), cf_1245 (12 Years), cf_1247 (Net value), cf_1249 (شهر التسليم), cf_1379 (88%), cf_1537 (Garden), cf_1539 (Building No.), cf_1541 (ثمن الوحدة بالحروف), cf_1543 (الباقى), cf_1545 (الباقى و قدره), cf_1547 (دفعة الحجز), cf_1549 (دفعة الحجز وقدره), cf_1551 (انه فى يوم), cf_1553 (ﻣﻦ ﺷﻬﺮ), cf_1555 (مبلغ صيانة), cf_1557 (ﻣﺒﻠﻎ ﺻﻴﺎﻧﻪ وقدره), cf_1559 (مبلغ 3), cf_1561 (مبلغ 3 وقدره), cf_1563 (مبلغ 4), cf_1565 (مبلغ 4 وقدره), cf_1567 (Unit Area بالحروف), cf_1569 (سنة التسليم), cf_1573 (يوم رقم), cf_1575 (تعديل المقدم), cf_1591 (اضافة مساحة الجاردن فى العقد), cf_1629 (Confirm), cf_1631 (Confirm Comment), cf_1669 (Project Name), cf_1671 (Project Category), cf_1673 (Project Location), cf_1675 (Unit Category), cf_1677 (Contract Status), cf_1789 (Payment Plan Module)
                                                </div>
                                                <div class="col-md-4">
                                                    <strong>PaymentPlan Model:</strong><br>
                                                    paymentplansid, unit<br><br>
                                                    <strong>PaymentPlanCF Model:</strong><br>
                                                    cf_1731 (Payment Options), cf_1733 (Down Payment), cf_1735 (Payment Method), cf_1739 (Reset), cf_1741 (Unit Area), cf_1743 (Meter Unit Price), cf_1745 (Garden Area), cf_1747 (Garden Meter Price), cf_1749 (Unit Price), cf_1757 (Customer Employment Name), cf_1759 (Bank Name), cf_1761 (Quarterly), cf_1763 (Half Yearly), cf_1765 (Annual), cf_1767 (Handover Payment), cf_1769 (Year of Handover Payment), cf_1773 (Contract), cf_1775 (Maintenance Fee), cf_1777 (Maintenance Fee Value), cf_1779 (Maintenance Fee Collection Year), cf_1783 (Handover Payment Value), cf_1785 (1st Installment Date), cf_1787 (Down Payment %)
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Quick reference for most commonly used -->
                                        <div class="mb-3">
                                            <h6 class="text-primary">Most Common Fields:</h6>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <ul class="list-unstyled small">
                                                        <li><code>&#123;&#123; company_name &#125;&#125;</code> - Company name</li>
                                                        <li><code>&#123;&#123; invoice_number &#125;&#125;</code> - Invoice number</li>
                                                        <li><code>&#123;&#123; date &#125;&#125;</code> - Invoice date</li>
                                                        <li><code>&#123;&#123; amount &#125;&#125;</code> - Total amount</li>
                                                        <li><code>&#123;&#123; customer_name &#125;&#125;</code> - Customer full name</li>
                                                        <li><code>&#123;&#123; customer_name_arabic &#125;&#125;</code> - Customer name in
                                                            Arabic</li>
                                                        <li><code>&#123;&#123; project_name &#125;&#125;</code> - Project name</li>
                                                        <li><code>&#123;&#123; unit_number &#125;&#125;</code> - Unit number</li>
                                                    </ul>
                                                </div>
                                                <div class="col-md-6">
                                                    <ul class="list-unstyled small">
                                                        <li><code>&#123;&#123; unit_area &#125;&#125;</code> - Unit area</li>
                                                        <li><code>&#123;&#123; payment_plan_id &#125;&#125;</code> - Payment plan ID</li>
                                                        <li><code>&#123;&#123; down_payment &#125;&#125;</code> - Down payment</li>
                                                        <li><code>&#123;&#123; contract_date &#125;&#125;</code> - Contract date</li>
                                                        <li><code>&#123;&#123; customer_phone &#125;&#125;</code> - Customer phone</li>
                                                        <li><code>&#123;&#123; customer_mobile &#125;&#125;</code> - Customer mobile</li>
                                                        <li><code>&#123;&#123; project_location &#125;&#125;</code> - Project location</li>
                                                        <li><code>&#123;&#123; checks_table &#125;&#125;</code> - <strong>Checks
                                                                table</strong></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="footer_html">Footer HTML</label>
                                <textarea class="form-control @error('footer_html') is-invalid @enderror" id="footer_html" name="footer_html"
                                    rows="3" placeholder="Optional footer content">{{ old('footer_html', $pdfTemplate->footer_html) }}</textarea>
                                @error('footer_html')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="css">Custom CSS</label>
                                <textarea class="form-control @error('css') is-invalid @enderror" id="css" name="css" rows="8"
                                    placeholder="Optional custom CSS styles">{{ old('css', $pdfTemplate->css) }}</textarea>
                                @error('css')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
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

    <!-- Google Fonts for Arabic -->
    <link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&family=Noto+Sans+Arabic:wght@100;200;300;400;500;600;700;800;900&family=Cairo:wght@200;300;400;500;600;700;800;900&family=Tajawal:wght@200;300;400;500;700;800;900&family=Scheherazade+New:wght@400;500;600;700&family=IBM+Plex+Sans+Arabic:wght@100;200;300;400;500;600;700&family=Changa:wght@200;300;400;500;600;700;800&family=Rubik:wght@300;400;500;600;700;800;900&family=Almarai:wght@300;400;700;800&family=El+Messiri:wght@400;500;600;700&family=Markazi+Text:wght@400;500;600;700&family=Reem+Kufi:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- TinyMCE CDN -->
    <script src="https://cdn.tiny.cloud/1/4m78n7o3orjvbv6nk85kyfqwbro9vh5uspvkdixdx8z6rjvz/tinymce/6/tinymce.min.js"
        referrerpolicy="origin"></script>
    <script>
        tinymce.init({
            selector: '#body_html',
            height: 400,
            plugins: 'advlist anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount directionality pagebreak',
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat | ltr rtl directionality | pagebreak custompagebreak',
            directionality: 'ltr',
            language: 'en',
            menubar: false,
            branding: false,
            promotion: false,
            font_family_formats: 'Arial=arial,helvetica,sans-serif; Courier New=courier new,courier; Georgia=georgia,palatino; Helvetica=helvetica; Impact=impact,chicago; Tahoma=tahoma,arial,helvetica,sans-serif; Times New Roman=times new roman,times; Trebuchet MS=trebuchet ms,geneva; Verdana=verdana,geneva; Amiri=Amiri,serif; Noto Sans Arabic=Noto Sans Arabic,sans-serif; Cairo=Cairo,sans-serif; Tajawal=Tajawal,sans-serif; Scheherazade New=Scheherazade New,serif; IBM Plex Sans Arabic=IBM Plex Sans Arabic,sans-serif; Changa=Changa,sans-serif; Rubik=Rubik,sans-serif; Almarai=Almarai,sans-serif; El Messiri=El Messiri,serif; Markazi Text=Markazi Text,serif; Reem Kufi=Reem Kufi,sans-serif',
            content_style: 'body { font-family: Arial, Tahoma, sans-serif; } .page-break { page-break-before: always; margin-top: 20px; border-top: 2px dashed #ccc; padding-top: 10px; } .no-break { page-break-inside: avoid; } @media print { .page-break { border: none; background: none; padding: 0; margin: 0; height: 0; overflow: hidden; } }',
            setup: function(editor) {
                // Hide the original textarea after TinyMCE is initialized
                editor.on('init', function() {
                    document.getElementById('body_html').style.display = 'none';
                });

                // Update direction when RTL checkbox changes
                const rtlCheckbox = document.getElementById('rtl');
                if (rtlCheckbox) {
                    rtlCheckbox.addEventListener('change', function() {
                        editor.execCommand('mceDirection', false, this.checked ? 'rtl' : 'ltr');
                    });
                }

                // Sync TinyMCE content with hidden textarea before form submission
                editor.on('change keyup', function() {
                    editor.save(); // This updates the hidden textarea
                });

                // Ensure content is synced on form submission
                document.querySelector('form').addEventListener('submit', function() {
                    editor.save(); // Save content to hidden textarea before submit
                });

                // Add custom page break button
                editor.ui.registry.addButton('custompagebreak', {
                    text: '📄 Page Break',
                    tooltip: 'Insert Page Break',
                    onAction: function() {
                        editor.insertContent(
                            '<div class="page-break" style="page-break-before: always; margin-top: 20px; border-top: 2px dashed #ccc; padding-top: 10px; text-align: center; color: #666; font-size: 12px;">&nbsp;</div>'
                            );
                    }
                });
            }
        });


        // Initialize header and footer editors
        tinymce.init({
            selector: '#header_html',
            height: 200,
            plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount directionality pagebreak',
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat | ltr rtl directionality | pagebreak',
            directionality: 'ltr',
            language: 'en',
            menubar: false,
            branding: false,
            promotion: false,
            font_family_formats: 'Arial=arial,helvetica,sans-serif; Courier New=courier new,courier; Georgia=georgia,palatino; Helvetica=helvetica; Impact=impact,chicago; Tahoma=tahoma,arial,helvetica,sans-serif; Times New Roman=times new roman,times; Trebuchet MS=trebuchet ms,geneva; Verdana=verdana,geneva; Amiri=Amiri,serif; Noto Sans Arabic=Noto Sans Arabic,sans-serif; Cairo=Cairo,sans-serif; Tajawal=Tajawal,sans-serif; Scheherazade New=Scheherazade New,serif; IBM Plex Sans Arabic=IBM Plex Sans Arabic,sans-serif; Changa=Changa,sans-serif; Rubik=Rubik,sans-serif; Almarai=Almarai,sans-serif; El Messiri=El Messiri,serif; Markazi Text=Markazi Text,serif; Reem Kufi=Reem Kufi,sans-serif',
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
            plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount directionality pagebreak',
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat | ltr rtl directionality | pagebreak',
            directionality: 'ltr',
            language: 'en',
            menubar: false,
            branding: false,
            promotion: false,
            font_family_formats: 'Arial=arial,helvetica,sans-serif; Courier New=courier new,courier; Georgia=georgia,palatino; Helvetica=helvetica; Impact=impact,chicago; Tahoma=tahoma,arial,helvetica,sans-serif; Times New Roman=times new roman,times; Trebuchet MS=trebuchet ms,geneva; Verdana=verdana,geneva; Amiri=Amiri,serif; Noto Sans Arabic=Noto Sans Arabic,sans-serif; Cairo=Cairo,sans-serif; Tajawal=Tajawal,sans-serif; Scheherazade New=Scheherazade New,serif; IBM Plex Sans Arabic=IBM Plex Sans Arabic,sans-serif; Changa=Changa,sans-serif; Rubik=Rubik,sans-serif; Almarai=Almarai,sans-serif; El Messiri=El Messiri,serif; Markazi Text=Markazi Text,serif; Reem Kufi=Reem Kufi,sans-serif',
            content_style: 'body { font-family: Arial, Tahoma, sans-serif; }',
            setup: function(editor) {
                // Sync content with textarea
                editor.on('change keyup', function() {
                    editor.save();
                });
            }
        });
    </script>
@endsection
