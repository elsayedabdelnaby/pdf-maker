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
                                    Use placeholders like, etc.
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
                                            <strong>Note:</strong> All the same placeholders from the create template are
                                            available for editing.
                                            <a href="{{ route('pdf-templates.create') }}" target="_blank">View full
                                                placeholder reference</a>
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
