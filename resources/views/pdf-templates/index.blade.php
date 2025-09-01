@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>PDF Templates</h4>
                    <a href="{{ route('pdf-templates.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create New Template
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($templates->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Engine</th>
                                        <th>Page Size</th>
                                        <th>Orientation</th>
                                        <th>RTL</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($templates as $template)
                                        <tr>
                                            <td>{{ $template->name }}</td>
                                            <td>{{ ucfirst($template->engine) }}</td>
                                            <td>{{ $template->page_size }}</td>
                                            <td>{{ ucfirst($template->orientation) }}</td>
                                            <td>
                                                @if($template->rtl)
                                                    <span class="badge badge-success">Yes</span>
                                                @else
                                                    <span class="badge badge-secondary">No</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('pdf-templates.edit', $template) }}" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </a>
                                                    <a href="{{ route('pdf-templates.show', $template) }}" 
                                                       class="btn btn-sm btn-outline-info">
                                                        <i class="fas fa-eye"></i> View
                                                    </a>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-success"
                                                            onclick="showExportModal({{ $template->id }}, '{{ $template->name }}')">
                                                        <i class="fas fa-file-pdf"></i> Export
                                                    </button>
                                                    <form action="{{ route('pdf-templates.destroy', $template) }}" 
                                                          method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                                onclick="return confirm('Are you sure?')">
                                                            <i class="fas fa-trash"></i> Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="text-muted">No PDF templates found.</p>
                            <a href="{{ route('pdf-templates.create') }}" class="btn btn-primary">
                                Create Your First Template
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportModalLabel">Export PDF</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="exportForm">
                    <div class="mb-3">
                        <label for="templateName" class="form-label">Template</label>
                        <input type="text" class="form-control" id="templateName" readonly>
                        <input type="hidden" id="templateId">
                    </div>
                    <div class="mb-3">
                        <label for="invoiceId" class="form-label">Invoice ID</label>
                        <input type="number" class="form-control" id="invoiceId" placeholder="Enter Invoice ID" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="exportPdf()">
                    <i class="fas fa-file-pdf"></i> Export PDF
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function showExportModal(templateId, templateName) {
    document.getElementById('templateId').value = templateId;
    document.getElementById('templateName').value = templateName;
    document.getElementById('invoiceId').value = '';
    
    const modal = new bootstrap.Modal(document.getElementById('exportModal'));
    modal.show();
}

function exportPdf() {
    const templateId = document.getElementById('templateId').value;
    const invoiceId = document.getElementById('invoiceId').value;
    
    if (!invoiceId) {
        alert('Please enter an Invoice ID');
        return;
    }
    
    // Use the DomPDF export route
    const url = `/export-pdf/${templateId}/invoice/${invoiceId}`;
    
    // Open in new tab
    window.open(url, '_blank');
    
    // Close modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('exportModal'));
    modal.hide();
}
</script>
@endsection
