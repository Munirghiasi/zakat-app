@extends('layouts.bootstrap')

@section('title', 'Dashboard')

@section('content')
@php
    $currencyService = app(\App\Services\CurrencyService::class);
    $user = auth()->user();
@endphp

<div class="row">
    <div class="col-12">
        <h2 class="mb-4"><i class="bi bi-house-door"></i> Dashboard</h2>
    </div>
</div>

@if($calculation)
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-value">{{ $currencyService->format($calculation->total_assets, $user->currency, $user) }}</div>
            <div class="stat-label">Total Assets</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-value">{{ $currencyService->format($calculation->total_debts, $user->currency, $user) }}</div>
            <div class="stat-label">Total Debts</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-value">{{ $currencyService->format($calculation->net_zakatable_wealth, $user->currency, $user) }}</div>
            <div class="stat-label">Net Zakatable Wealth</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-value text-danger">{{ $currencyService->format($calculation->zakat_due, $user->currency, $user) }}</div>
            <div class="stat-label">Zakat Due (2.5%)</div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="stat-card">
            <div class="stat-value text-success">{{ $currencyService->format($calculation->zakat_paid, $user->currency, $user) }}</div>
            <div class="stat-label">Zakat Paid</div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="stat-card">
            <div class="stat-value text-warning">{{ $currencyService->format($calculation->zakat_remaining, $user->currency, $user) }}</div>
            <div class="stat-label">Zakat Remaining</div>
        </div>
    </div>
</div>
@endif

@if($calculation && isset($assetBreakdown) && count($assetBreakdown) > 0)
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-calculator"></i> Asset Breakdown with Zakat Calculation
            </div>
            <div class="card-body">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Asset Category</th>
                            <th class="text-end">Total Amount</th>
                            <th class="text-end">Zakat (2.5%)</th>
                            <th class="text-end">Remaining After Zakat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($assetBreakdown as $item)
                            <tr>
                                <td>{{ $item['category'] }}</td>
                                <td class="text-end">{{ $currencyService->format($item['total'], $user->currency, $user) }}</td>
                                <td class="text-end text-danger">{{ $currencyService->format($item['zakat_amount'], $user->currency, $user) }}</td>
                                <td class="text-end text-success">{{ $currencyService->format($item['remaining_after_zakat'], $user->currency, $user) }}</td>
                            </tr>
                        @endforeach
                        <tr class="table-primary fw-bold">
                            <td><strong>Total</strong></td>
                            <td class="text-end"><strong>{{ $currencyService->format($calculation->total_assets, $user->currency, $user) }}</strong></td>
                            <td class="text-end"><strong>{{ $currencyService->format($calculation->zakat_due, $user->currency, $user) }}</strong></td>
                            <td class="text-end"><strong>{{ $currencyService->format($calculation->total_assets - $calculation->zakat_due, $user->currency, $user) }}</strong></td>
                        </tr>
                    </tbody>
                </table>
                <div class="alert alert-info mt-3 mb-0">
                    <i class="bi bi-info-circle"></i> 
                    <strong>Note:</strong> This breakdown shows the Zakat amount calculated for each asset category based on the total Zakat due (2.5% of net zakatable wealth). 
                    The calculation is proportional to each category's share of total assets.
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-wallet2"></i> Recent Assets
            </div>
            <div class="card-body">
                @if($recentAssets->count() > 0)
                    <ul class="list-group list-group-flush">
                        @foreach($recentAssets as $asset)
                            <li class="list-group-item d-flex justify-content-between">
                                <span>{{ $asset->category }} - {{ $currencyService->format($asset->amount, $user->currency, $user) }}</span>
                                <small class="text-muted">{{ $asset->created_at->format('M d') }}</small>
                            </li>
                        @endforeach
                    </ul>
                    <a href="{{ route('assets.index') }}" class="btn btn-sm btn-primary mt-2">View All</a>
                @else
                    <p class="text-muted">No assets recorded yet.</p>
                    <a href="{{ route('assets.create') }}" class="btn btn-sm btn-primary">Add Asset</a>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-credit-card"></i> Recent Debts
            </div>
            <div class="card-body">
                @if($recentDebts->count() > 0)
                    <ul class="list-group list-group-flush">
                        @foreach($recentDebts as $debt)
                            <li class="list-group-item d-flex justify-content-between">
                                <span>{{ $debt->type }} - {{ $currencyService->format($debt->amount, $user->currency, $user) }}</span>
                                <small class="text-muted">{{ $debt->created_at->format('M d') }}</small>
                            </li>
                        @endforeach
                    </ul>
                    <a href="{{ route('debts.index') }}" class="btn btn-sm btn-primary mt-2">View All</a>
                @else
                    <p class="text-muted">No debts recorded yet.</p>
                    <a href="{{ route('debts.create') }}" class="btn btn-sm btn-primary">Add Debt</a>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-cash-coin"></i> Recent Distributions
            </div>
            <div class="card-body">
                @if($recentDistributions->count() > 0)
                    <ul class="list-group list-group-flush">
                        @foreach($recentDistributions as $distribution)
                            <li class="list-group-item d-flex justify-content-between">
                                <span>{{ $distribution->recipient->name ?? 'N/A' }} - {{ $currencyService->format($distribution->amount, $user->currency, $user) }}</span>
                                <small class="text-muted">{{ $distribution->distribution_date->format('M d') }}</small>
                            </li>
                        @endforeach
                    </ul>
                    <a href="{{ route('distributions.index') }}" class="btn btn-sm btn-primary mt-2">View All</a>
                @else
                    <p class="text-muted">No distributions recorded yet.</p>
                    <a href="{{ route('distributions.create') }}" class="btn btn-sm btn-primary">Add Distribution</a>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-info-circle"></i> Quick Actions
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-2">
                        <a href="{{ route('assets.create') }}" class="btn btn-outline-primary w-100">
                            <i class="bi bi-plus-circle"></i> Add Asset
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="{{ route('debts.create') }}" class="btn btn-outline-primary w-100">
                            <i class="bi bi-plus-circle"></i> Add Debt
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="{{ route('zakat.summary') }}" class="btn btn-outline-success w-100">
                            <i class="bi bi-calculator"></i> View Zakat Summary
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="{{ route('distributions.create') }}" class="btn btn-outline-success w-100">
                            <i class="bi bi-cash-coin"></i> Record Distribution
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
