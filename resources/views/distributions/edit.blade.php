@extends('layouts.bootstrap')

@section('title', 'Edit Distribution')

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-pencil"></i> Edit Distribution
            </div>
            <div class="card-body">
                <form action="{{ route('distributions.update', $distribution) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="recipient_id" class="form-label">Recipient</label>
                        <select class="form-select" id="recipient_id" name="recipient_id" required>
                            @foreach($recipients as $recipient)
                                <option value="{{ $recipient->id }}" {{ $distribution->recipient_id === $recipient->id ? 'selected' : '' }}>
                                    {{ $recipient->name }} ({{ \App\Models\Recipient::getValidCategories()[$recipient->category] ?? $recipient->category }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount</label>
                        <input type="number" step="0.01" class="form-control" id="amount" name="amount" 
                               value="{{ $distribution->amount }}" required>
                        @if($calculation)
                            <small class="text-muted">Available: {{ number_format($calculation->zakat_remaining + $distribution->amount, 2) }}</small>
                        @endif
                    </div>
                    
                    <div class="mb-3">
                        <label for="distribution_date" class="form-label">Distribution Date</label>
                        <input type="date" class="form-control" id="distribution_date" name="distribution_date" 
                               value="{{ $distribution->distribution_date->format('Y-m-d') }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3">{{ $distribution->notes }}</textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="receipt" class="form-label">Receipt (Optional)</label>
                        @if($distribution->receipt_path)
                            <div class="mb-2">
                                <a href="{{ asset('storage/' . $distribution->receipt_path) }}" target="_blank" class="btn btn-sm btn-info">
                                    <i class="bi bi-file-earmark"></i> View Current Receipt
                                </a>
                            </div>
                        @endif
                        <input type="file" class="form-control" id="receipt" name="receipt" accept=".pdf,.jpg,.jpeg,.png">
                        <small class="text-muted">Leave empty to keep current receipt</small>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Update Distribution</button>
                        <a href="{{ route('distributions.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

