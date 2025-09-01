@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-12">
                            <h4>Welcome, {{ Auth::user()->name }}!</h4>
                            <p class="text-muted">You are successfully logged in to your account.</p>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="fas fa-user"></i> Profile
                                    </h5>
                                    <p class="card-text">Manage your account information</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="fas fa-cog"></i> Settings
                                    </h5>
                                    <p class="card-text">Configure your preferences</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Account Information</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Name:</strong></td>
                                            <td>{{ Auth::user()->name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Email:</strong></td>
                                            <td>{{ Auth::user()->email }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Member Since:</strong></td>
                                            <td>{{ Auth::user()->created_at->format('F j, Y') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Quick PDF Export</h5>
                                </div>
                                <div class="card-body">
                                    <form id="quickExportForm">
                                        <div class="mb-3">
                                            <label for="quickTemplateId" class="form-label">Template</label>
                                            <select class="form-control" id="quickTemplateId" required>
                                                <option value="">Select Template</option>
                                                @foreach(\App\Models\PdfTemplate::all() as $template)
                                                    <option value="{{ $template->id }}">{{ $template->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="quickInvoiceId" class="form-label">Invoice ID</label>
                                            <input type="number" class="form-control" id="quickInvoiceId" placeholder="Enter Invoice ID" required>
                                        </div>
                                        <button type="submit" class="btn btn-success w-100">
                                            <i class="fas fa-file-pdf"></i> Export PDF
                                        </button>
                                    </form>
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
document.getElementById('quickExportForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const templateId = document.getElementById('quickTemplateId').value;
    const invoiceId = document.getElementById('quickInvoiceId').value;
    
    if (!templateId) {
        alert('Please select a template');
        return;
    }
    
    if (!invoiceId) {
        alert('Please enter an Invoice ID');
        return;
    }
    
    // Use the DomPDF export route
    const url = `/export-pdf/${templateId}/invoice/${invoiceId}`;
    
    // Open in new tab
    window.open(url, '_blank');
    
    // Reset form
    document.getElementById('quickExportForm').reset();
});
</script>
@endsection
