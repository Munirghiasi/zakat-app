@extends('layouts.bootstrap')

@section('title', 'Zakat Summary')

@section('content')
@php
    $currencyService = app(\App\Services\CurrencyService::class);
    $user = auth()->user();
@endphp

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-calculator"></i> Zakat Summary</h2>
            <form action="{{ route('zakat.recalculate') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-arrow-clockwise"></i> Recalculate
                </button>
            </form>
        </div>
    </div>
</div>

@if($calculation)
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-wallet2"></i> Assets Breakdown
            </div>
            <div class="card-body">
                <table class="table">
                    <tr>
                        <td><strong>Total Assets:</strong></td>
                        <td class="text-end">{{ $currencyService->format($calculation->total_assets, $user->currency, $user) }}</td>
                    </tr>
                    <tr>
                        <td><strong>Total Debts:</strong></td>
                        <td class="text-end text-danger">-{{ $currencyService->format($calculation->total_debts, $user->currency, $user) }}</td>
                    </tr>
                    <tr class="table-primary">
                        <td><strong>Net Zakatable Wealth:</strong></td>
                        <td class="text-end"><strong>{{ $currencyService->format($calculation->net_zakatable_wealth, $user->currency, $user) }}</strong></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-calculator"></i> Zakat Calculation
            </div>
            <div class="card-body">
                <table class="table">
                    <tr>
                        <td><strong>Nisab:</strong></td>
                        <td class="text-end">{{ $currencyService->format($calculation->nisab, $user->currency, $user) }}</td>
                    </tr>
                    <tr>
                        <td><strong>Zakat Due (2.5%):</strong></td>
                        <td class="text-end text-danger"><strong>{{ $currencyService->format($calculation->zakat_due, $user->currency, $user) }}</strong></td>
                    </tr>
                    <tr>
                        <td><strong>Zakat Paid:</strong></td>
                        <td class="text-end text-success">{{ $currencyService->format($calculation->zakat_paid, $user->currency, $user) }}</td>
                    </tr>
                    <tr class="table-warning">
                        <td><strong>Zakat Remaining:</strong></td>
                        <td class="text-end"><strong>{{ $currencyService->format($calculation->zakat_remaining, $user->currency, $user) }}</strong></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> 
            <strong>Note:</strong> Zakat is calculated at 2.5% of your net zakatable wealth if it exceeds the Nisab threshold. 
            The Nisab is calculated based on the current gold price (87.48 grams of gold).
        </div>
    </div>
</div>
@else
<div class="row">
    <div class="col-12">
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle"></i> No calculation found. Please add assets and debts first.
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
    // Auto-refresh calculation every 30 seconds
    setInterval(function() {
        // Optionally refresh the page or make an AJAX call
    }, 30000);
</script>
@endpush
@endsection

