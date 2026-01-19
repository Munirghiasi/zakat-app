@extends('layouts.bootstrap')

@section('title', 'Exchange Rates')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-currency-exchange"></i> Exchange Rates Management</h2>
            <a href="{{ route('admin.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Admin
            </a>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-plus-circle"></i> Update Exchange Rate
            </div>
            <div class="card-body">
                <form action="{{ route('admin.exchange-rates.update') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="currency" class="form-label">Currency</label>
                        <select class="form-select" id="currency" name="currency" required>
                            <option value="">Select Currency</option>
                            @foreach($currencies as $code => $info)
                                <option value="{{ $code }}">{{ $info['name'] }} ({{ $code }})</option>
                            @endforeach
                        </select>
                        @error('currency')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="rate" class="form-label">Exchange Rate (relative to USD)</label>
                        <input type="number" step="0.000001" class="form-control" id="rate" name="rate" 
                               value="{{ old('rate') }}" required>
                        <small class="text-muted">Enter the rate relative to USD (base currency). For example, if 1 USD = 3.75 SAR, enter 3.75</small>
                        @error('rate')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="effective_from" class="form-label">Effective From</label>
                        <input type="date" class="form-control" id="effective_from" name="effective_from" 
                               value="{{ old('effective_from', date('Y-m-d')) }}" required>
                        @error('effective_from')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="source" class="form-label">Source (Optional)</label>
                        <input type="text" class="form-control" id="source" name="source" 
                               value="{{ old('source') }}" placeholder="e.g., API, Manual, Central Bank">
                        @error('source')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i> 
                        <strong>Note:</strong> Setting a new exchange rate will deactivate the current active rate for this currency.
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Update Exchange Rate</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-list"></i> Current Exchange Rates
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Currency</th>
                            <th>Rate (to USD)</th>
                            <th>Effective From</th>
                            <th>Source</th>
                            <th>Status</th>
                            <th>Updated By</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rates as $rate)
                            <tr>
                                <td><strong>{{ $rate->currency }}</strong> - {{ $currencies[$rate->currency]['name'] ?? 'N/A' }}</td>
                                <td>{{ number_format($rate->rate, 6) }}</td>
                                <td>{{ $rate->effective_from->format('M d, Y') }}</td>
                                <td>{{ $rate->source ?? 'N/A' }}</td>
                                <td>
                                    @if($rate->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>{{ $rate->updatedBy->name ?? 'System' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">No exchange rates found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

