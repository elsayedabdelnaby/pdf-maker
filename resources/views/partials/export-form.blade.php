<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-file-pdf"></i> PDF Export
        </h5>
    </div>
    <div class="card-body">
        <form id="exportForm{{ $uniqueId ?? 'Default' }}" class="export-form">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="templateId{{ $uniqueId ?? 'Default' }}" class="form-label">Template</label>
                        <select class="form-control" id="templateId{{ $uniqueId ?? 'Default' }}" name="template_id" required>
                            <option value="">Select Template</option>
                            @foreach(\App\Models\PdfTemplate::all() as $template)
                                <option value="{{ $template->id }}">{{ $template->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="invoiceId{{ $uniqueId ?? 'Default' }}" class="form-label">Invoice ID</label>
                        <input type="number" class="form-control" id="invoiceId{{ $uniqueId ?? 'Default' }}" name="invoice_id" placeholder="Enter Invoice ID" required>
                    </div>
                </div>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-file-pdf"></i> Export PDF
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('exportForm{{ $uniqueId ?? 'Default' }}').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const templateId = document.getElementById('templateId{{ $uniqueId ?? 'Default' }}').value;
    const invoiceId = document.getElementById('invoiceId{{ $uniqueId ?? 'Default' }}').value;
    
    if (!templateId) {
        alert('Please select a template');
        return;
    }
    
    if (!invoiceId) {
        alert('Please enter an Invoice ID');
        return;
    }
    
    // Use the DomPDF export route with correct base path
    const url = `${window.appConfig.baseUrl}/export-pdf/${templateId}/invoice/${invoiceId}`;
    
    // Open in new tab
    window.open(url, '_blank');
    
    // Reset form
    this.reset();
});
</script>
