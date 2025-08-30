@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Create New PDF Template</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('pdf-templates.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Template Name *</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" required>
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
                                        <option value="handlebars" {{ old('engine') == 'handlebars' ? 'selected' : '' }}>
                                            Handlebars
                                        </option>
                                        <option value="mustache" {{ old('engine') == 'mustache' ? 'selected' : '' }}>
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
                                        <option value="A4" {{ old('page_size') == 'A4' ? 'selected' : '' }}>A4</option>
                                        <option value="A3" {{ old('page_size') == 'A3' ? 'selected' : '' }}>A3</option>
                                        <option value="Letter" {{ old('page_size') == 'Letter' ? 'selected' : '' }}>Letter</option>
                                        <option value="Legal" {{ old('page_size') == 'Legal' ? 'selected' : '' }}>Legal</option>
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
                                        <option value="portrait" {{ old('orientation') == 'portrait' ? 'selected' : '' }}>
                                            Portrait
                                        </option>
                                        <option value="landscape" {{ old('orientation') == 'landscape' ? 'selected' : '' }}>
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
                                               {{ old('rtl') ? 'checked' : '' }}>
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
                                           id="margin_top" name="margin_top" value="{{ old('margin_top', 20) }}" 
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
                                           id="margin_right" name="margin_right" value="{{ old('margin_right', 15) }}" 
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
                                           id="margin_bottom" name="margin_bottom" value="{{ old('margin_bottom', 20) }}" 
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
                                           id="margin_left" name="margin_left" value="{{ old('margin_left', 15) }}" 
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
                                      placeholder="Optional header content">{{ old('header_html') }}</textarea>
                            @error('header_html')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="body_html">Main Template Content *</label>
                            <textarea class="form-control @error('body_html') is-invalid @enderror" 
                                      id="body_html" name="body_html" rows="15" required>{{ old('body_html') }}</textarea>
                            @error('body_html')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Use placeholders like
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="footer_html">Footer HTML</label>
                            <textarea class="form-control @error('footer_html') is-invalid @enderror" 
                                      id="footer_html" name="footer_html" rows="3" 
                                      placeholder="Optional footer content">{{ old('footer_html') }}</textarea>
                            @error('footer_html')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="css">Custom CSS</label>
                            <textarea class="form-control @error('css') is-invalid @enderror" 
                                      id="css" name="css" rows="8" 
                                      placeholder="Optional custom CSS styles">{{ old('css') }}</textarea>
                            @error('css')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create Template
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
