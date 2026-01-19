@extends('layouts.bootstrap')

@section('title', 'Admin Panel')

@section('content')
<div class="row">
    <div class="col-12">
        <h2 class="mb-4"><i class="bi bi-shield-check"></i> Admin Panel</h2>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-value">{{ $stats['total_users'] }}</div>
            <div class="stat-label">Total Users</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-value">{{ number_format($stats['total_zakat_paid'], 2) }}</div>
            <div class="stat-label">Total Zakat Paid</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-value">
                @if($stats['active_nisab'])
                    {{ number_format($stats['active_nisab']->nisab_value, 2) }}
                @else
                    N/A
                @endif
            </div>
            <div class="stat-label">Current Nisab</div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-gear"></i> Quick Actions
            </div>
            <div class="card-body">
                <a href="{{ route('admin.nisab-settings') }}" class="btn btn-primary w-100 mb-2">
                    <i class="bi bi-currency-exchange"></i> Manage Nisab Settings
                </a>
                <a href="{{ route('admin.users') }}" class="btn btn-primary w-100 mb-2">
                    <i class="bi bi-people"></i> Manage Users
                </a>
                <a href="{{ route('admin.exchange-rates') }}" class="btn btn-primary w-100">
                    <i class="bi bi-currency-exchange"></i> Manage Exchange Rates
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-info-circle"></i> System Information
            </div>
            <div class="card-body">
                <p><strong>Current Nisab:</strong> 
                    @if($stats['active_nisab'])
                        {{ number_format($stats['active_nisab']->nisab_value, 2) }} 
                        (Gold: {{ number_format($stats['active_nisab']->gold_price_per_gram, 2) }}/gram)
                    @else
                        Not set
                    @endif
                </p>
                <p><strong>Effective From:</strong> 
                    @if($stats['active_nisab'])
                        {{ $stats['active_nisab']->effective_from->format('M d, Y') }}
                    @else
                        N/A
                    @endif
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

