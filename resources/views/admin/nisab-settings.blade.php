@extends('layouts.bootstrap')

@section('title', 'Nisab Settings')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-currency-exchange"></i> Nisab Settings</h2>
            <a href="{{ route('admin.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Admin
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-plus-circle"></i> Update Nisab Settings
            </div>
            <div class="card-body">
                @if($activeSetting)
                    <div class="alert alert-info mb-3">
                        <strong>Current Active Setting:</strong><br>
                        Gold Price: {{ number_format($activeSetting->gold_price_per_gram, 2) }} per gram<br>
                        Nisab Value: {{ number_format($activeSetting->nisab_value, 2) }}<br>
                        Effective From: {{ $activeSetting->effective_from->format('M d, Y') }}
                    </div>
                @endif
                
                <form action="{{ route('admin.nisab-settings.update') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="gold_price_per_gram" class="form-label">Gold Price Per Gram</label>
                        <input type="number" step="0.01" class="form-control" id="gold_price_per_gram" 
                               name="gold_price_per_gram" value="{{ old('gold_price_per_gram') }}" required>
                        @error('gold_price_per_gram')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="silver_price_per_gram" class="form-label">Silver Price Per Gram (Optional)</label>
                        <input type="number" step="0.01" class="form-control" id="silver_price_per_gram" 
                               name="silver_price_per_gram" value="{{ old('silver_price_per_gram') }}">
                        @error('silver_price_per_gram')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="source" class="form-label">Source (e.g., website, market)</label>
                        <input type="text" class="form-control" id="source" name="source" 
                               value="{{ old('source') }}" placeholder="e.g., Kitco, Local Market">
                        @error('source')
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
                    
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i> 
                        <strong>Note:</strong> Nisab will be calculated as: Gold Price × 87.48 grams. 
                        Setting a new value will deactivate the current active setting.
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Update Nisab Settings</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-clock-history"></i> Previous Settings
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Gold Price</th>
                            <th>Nisab Value</th>
                            <th>Effective From</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($settings as $setting)
                            <tr>
                                <td>{{ number_format($setting->gold_price_per_gram, 2) }}</td>
                                <td>{{ number_format($setting->nisab_value, 2) }}</td>
                                <td>{{ $setting->effective_from->format('M d, Y') }}</td>
                                <td>
                                    @if($setting->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">No settings found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

