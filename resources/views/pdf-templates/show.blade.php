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
                                            @if ($pdfTemplate->rtl)
                                                <span class="badge bg-success">Yes (Arabic Support)</span>
                                            @else
                                                <span class="badge bg-secondary">No</span>
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
                                            <div class="form-group mb-3">
                                                <label for="model_type">Model Type</label>
                                                <select class="form-control" id="model_type" name="model_type" required>
                                                    <option value="invoice" selected>Invoice</option>
                                                </select>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="model_id">Model ID</label>
                                                <input type="number" class="form-control" id="model_id" name="model_id"
                                                    placeholder="Enter the Invoice ID" required>
                                            </div>

                                            <div class="btn-group-vertical w-100">
                                                <button type="submit" class="btn btn-success mb-2">
                                                    <i class="fas fa-file-pdf"></i> Generate PDF (DomPDF - Recommended)
                                                </button>

                                                <button type="button" class="btn btn-info mb-2" onclick="testOldMethod()">
                                                    <i class="fas fa-file-pdf"></i> Test Old Method (WKHTMLTOPDF)
                                                </button>

                                                <button type="button" class="btn btn-outline-primary mb-2"
                                                    onclick="debugPdf()">
                                                    <i class="fas fa-bug"></i> Debug PDF Generation
                                                </button>

                                                <button type="button" class="btn btn-outline-secondary"
                                                    onclick="debugOldMethod()">
                                                    <i class="fas fa-bug"></i> Debug Old Method
                                                </button>
                                            </div>
                                        </form>

                                        <script>
                                            document.getElementById('generatePdfForm').addEventListener('submit', function(e) {
                                                e.preventDefault();

                                                const templateId = {{ $pdfTemplate->id }};
                                                const modelType = document.getElementById('model_type').value;
                                                const modelId = document.getElementById('model_id').value;

                                                if (!modelId) {
                                                    alert('Please enter an Invoice ID');
                                                    return;
                                                }

                                                // Use the new DomPDF export route (recommended) with correct base path
                                                const url = `${window.appConfig.baseUrl}/export-pdf/${templateId}/invoice/${modelId}`;

                                                // Open in new tab
                                                window.open(url, '_blank');
                                            });

                                            function testOldMethod() {
                                                const templateId = {{ $pdfTemplate->id }};
                                                const modelId = document.getElementById('model_id').value;

                                                if (!modelId) {
                                                    alert('Please enter an Invoice ID');
                                                    return;
                                                }

                                                // Use the old WKHTMLTOPDF method with correct base path
                                                const url = `${window.appConfig.baseUrl}/generate-pdf/${templateId}/invoice/${modelId}`;
                                                window.open(url, '_blank');
                                            }

                                            function debugPdf() {
                                                const templateId = {{ $pdfTemplate->id }};
                                                const modelId = document.getElementById('model_id').value;

                                                if (!modelId) {
                                                    alert('Please enter an Invoice ID');
                                                    return;
                                                }

                                                // Debug the new DomPDF method with correct base path
                                                const url = `${window.appConfig.baseUrl}/debug-export/${templateId}/invoice/${modelId}`;
                                                window.open(url, '_blank');
                                            }

                                            function debugOldMethod() {
                                                const templateId = {{ $pdfTemplate->id }};
                                                const modelId = document.getElementById('model_id').value;

                                                if (!modelId) {
                                                    alert('Please enter an Invoice ID');
                                                    return;
                                                }

                                                // Debug the old WKHTMLTOPDF method with correct base path
                                                const url = `${window.appConfig.baseUrl}/debug-pdf/${templateId}/invoice/${modelId}`;
                                                window.open(url, '_blank');
                                            }
                                        </script>
                                    </div>
                                </div>

                                <!-- Test Results Display -->
                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h6 class="mb-0">
                                            <i class="fas fa-info-circle"></i> Template Usage Information
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="alert alert-info">
                                            <strong>DomPDF Method (Recommended):</strong><br>
                                            • Better Arabic/RTL support<br>
                                            • More reliable PDF generation<br>
                                            • Built-in Laravel integration<br>
                                            • Works without external dependencies
                                        </div>

                                        <div class="alert alert-warning">
                                            <strong>WKHTMLTOPDF Method (Legacy):</strong><br>
                                            • Requires wkhtmltopdf binary installation<br>
                                            • May have issues on some servers<br>
                                            • Better for complex layouts<br>
                                            • Native page numbering support
                                        </div>

                                        <div class="alert alert-success">
                                            <strong>Available Fields:</strong><br>
                                            • All Invoice fields (&#123;&#123; invoice_number &#125;&#125;, &#123;&#123; amount &#125;&#125;, etc.)<br>
                                            • All Customer fields (&#123;&#123; customer_name &#125;&#125;, &#123;&#123; customer_phone &#125;&#125;, etc.)<br>
                                            • All Payment Plan fields (&#123;&#123; payment_plan_id &#125;&#125;, &#123;&#123; down_payment &#125;&#125;,
                                            etc.)<br>
                                            • Special &#123;&#123; checks_table &#125;&#125; for automatic checks listing<br>
                                            • Project & Unit fields (&#123;&#123; project_name &#125;&#125;, &#123;&#123; unit_area &#125;&#125;, etc.)
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
                                                <a class="nav-link active" id="body-tab" data-bs-toggle="tab"
                                                    href="#body" role="tab">
                                                    Main Content
                                                </a>
                                            </li>
                                            @if ($pdfTemplate->header_html)
                                                <li class="nav-item">
                                                    <a class="nav-link" id="header-tab" data-bs-toggle="tab" href="#header"
                                                        role="tab">
                                                        Header
                                                    </a>
                                                </li>
                                            @endif
                                            @if ($pdfTemplate->footer_html)
                                                <li class="nav-item">
                                                    <a class="nav-link" id="footer-tab" data-bs-toggle="tab" href="#footer"
                                                        role="tab">
                                                        Footer
                                                    </a>
                                                </li>
                                            @endif
                                            @if ($pdfTemplate->css)
                                                <li class="nav-item">
                                                    <a class="nav-link" id="css-tab" data-bs-toggle="tab" href="#css"
                                                        role="tab">
                                                        CSS
                                                    </a>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                    <div class="card-body">
                                        <div class="tab-content" id="templateTabsContent">
                                            <div class="tab-pane fade show active" id="body" role="tabpanel">
                                                <div class="border p-3 bg-light"
                                                    style="direction: {{ $pdfTemplate->rtl ? 'rtl' : 'ltr' }}; max-height: 400px; overflow-y: auto;">
                                                    {!! $pdfTemplate->body_html !!}
                                                </div>
                                            </div>
                                            @if ($pdfTemplate->header_html)
                                                <div class="tab-pane fade" id="header" role="tabpanel">
                                                    <div class="border p-3 bg-light"
                                                        style="direction: {{ $pdfTemplate->rtl ? 'rtl' : 'ltr' }};">
                                                        {!! $pdfTemplate->header_html !!}
                                                    </div>
                                                </div>
                                            @endif
                                            @if ($pdfTemplate->footer_html)
                                                <div class="tab-pane fade" id="footer" role="tabpanel">
                                                    <div class="border p-3 bg-light"
                                                        style="direction: {{ $pdfTemplate->rtl ? 'rtl' : 'ltr' }};">
                                                        {!! $pdfTemplate->footer_html !!}
                                                    </div>
                                                </div>
                                            @endif
                                            @if ($pdfTemplate->css)
                                                <div class="tab-pane fade" id="css" role="tabpanel">
                                                    <pre style="max-height: 400px; overflow-y: auto;"><code>{{ $pdfTemplate->css }}</code></pre>
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
@endsection
