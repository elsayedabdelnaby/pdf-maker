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
                                            <i class="fas fa-file-pdf"></i> Generate PDF
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
                                            
                                            // Construct the URL based on the route structure
                                            const url = `/generate-pdf/${templateId}/${modelType}/${modelId}`;
                                            
                                            // Open in new tab
                                            window.open(url, '_blank');
                                        });
                                    </script>
                                </div>
                            </div>
                            
                            <div class="mt-3">
                                <h6>Available Placeholders:</h6>
                                <ul class="list-unstyled">
                                    <li><code>company_name</code> - Company name</li>
                                    <li><code>invoice_number</code> - Invoice/record number</li>
                                    <li><code>date</code> - Creation date</li>
                                    <li><code>amount</code> - Total amount</li>
                                    <li><code>customer_name</code> - Customer name</li>
                                    <li><code>model_type</code> - Model type</li>
                                    <li><code>model_id</code> - Model ID</li>
                                </ul>
                                <small class="text-muted">Note: Currently supporting Invoice model only. Enter the Invoice ID to generate PDF.</small>
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
                                            <a class="nav-link active" id="body-tab" data-toggle="tab" href="#body" role="tab">
                                                Main Content
                                            </a>
                                        </li>
                                        @if($pdfTemplate->header_html)
                                            <li class="nav-item">
                                                <a class="nav-link" id="header-tab" data-toggle="tab" href="#header" role="tab">
                                                    Header
                                                </a>
                                            </li>
                                        @endif
                                        @if($pdfTemplate->footer_html)
                                            <li class="nav-item">
                                                <a class="nav-link" id="footer-tab" data-toggle="tab" href="#footer" role="tab">
                                                    Footer
                                                </a>
                                            </li>
                                        @endif
                                        @if($pdfTemplate->css)
                                            <li class="nav-item">
                                                <a class="nav-link" id="css-tab" data-toggle="tab" href="#css" role="tab">
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
