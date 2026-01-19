@extends('layouts.bootstrap')

@section('title', 'Record Distribution')

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-plus-circle"></i> Record Distribution
            </div>
            <div class="card-body">
                @if($calculation && $calculation->zakat_remaining <= 0)
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i> You have no remaining Zakat to distribute.
                    </div>
                @endif
                
                <form action="{{ route('distributions.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="recipient_id" class="form-label">Recipient</label>
                        <select class="form-select" id="recipient_id" name="recipient_id" required>
                            <option value="">Select Recipient</option>
                            @foreach($recipients as $recipient)
                                <option value="{{ $recipient->id }}">{{ $recipient->name }} ({{ \App\Models\Recipient::getValidCategories()[$recipient->category] ?? $recipient->category }})</option>
                            @endforeach
                        </select>
                        @error('recipient_id')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount</label>
                        <input type="number" step="0.01" class="form-control" id="amount" name="amount" 
                               value="{{ old('amount') }}" 
                               max="{{ $calculation ? $calculation->zakat_remaining : '' }}"
                               required>
                        @if($calculation)
                            <small class="text-muted">Maximum available: {{ number_format($calculation->zakat_remaining, 2) }}</small>
                        @endif
                        @error('amount')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="distribution_date" class="form-label">Distribution Date</label>
                        <input type="date" class="form-control" id="distribution_date" name="distribution_date" 
                               value="{{ old('distribution_date', date('Y-m-d')) }}" required>
                        @error('distribution_date')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="receipt" class="form-label">Receipt (Optional)</label>
                        <input type="file" class="form-control" id="receipt" name="receipt" accept=".pdf,.jpg,.jpeg,.png">
                        <small class="text-muted">Max size: 5MB. Formats: PDF, JPG, PNG</small>
                        @error('receipt')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Record Distribution</button>
                        <a href="{{ route('distributions.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const amountInput = document.getElementById('amount');
        const maxAmount = {{ $calculation ? $calculation->zakat_remaining : 0 }};
        
        amountInput.addEventListener('input', function() {
            const value = parseFloat(this.value) || 0;
            if (value > maxAmount) {
                this.setCustomValidity('Amount cannot exceed remaining Zakat: ' + maxAmount.toFixed(2));
                this.classList.add('is-invalid');
            } else {
                this.setCustomValidity('');
                this.classList.remove('is-invalid');
            }
        });
    });
</script>
@endpush
@endsection

