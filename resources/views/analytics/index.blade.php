@extends('layouts.bootstrap')

@section('title', 'Analytics')

@section('content')
<div class="row">
    <div class="col-12">
        <h2 class="mb-4"><i class="bi bi-graph-up"></i> Analytics & Insights</h2>
    </div>
</div>

@php
    $currencyService = app(\App\Services\CurrencyService::class);
    $user = auth()->user();
@endphp

<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-bar-chart"></i> Asset Trends (Last 12 Months)
            </div>
            <div class="card-body">
                <canvas id="assetTrendsChart" height="100"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-pie-chart"></i> Distribution by Category
            </div>
            <div class="card-body">
                <canvas id="distributionCategoryChart"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-graph-up-arrow"></i> Monthly Distributions
            </div>
            <div class="card-body">
                <canvas id="monthlyDistributionsChart"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-calendar-range"></i> Year-over-Year Comparison
            </div>
            <div class="card-body">
                <canvas id="yearComparisonChart" height="100"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-pie-chart-fill"></i> Asset Breakdown by Type
            </div>
            <div class="card-body">
                <canvas id="assetBreakdownChart"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-pie-chart-fill"></i> Debt Breakdown by Type
            </div>
            <div class="card-body">
                <canvas id="debtBreakdownChart"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-people"></i> Top Recipients
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Recipient</th>
                            <th>Number of Distributions</th>
                            <th>Total Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recipientStats as $stat)
                            <tr>
                                <td>{{ $stat->name }}</td>
                                <td>{{ $stat->count }}</td>
                                <td>{{ $currencyService->format($stat->total, $user->currency, $user) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted">No distributions recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // Asset Trends Chart
    const assetTrendsCtx = document.getElementById('assetTrendsChart').getContext('2d');
    new Chart(assetTrendsCtx, {
        type: 'line',
        data: {
            labels: @json($assetTrends['labels']),
            datasets: [{
                label: 'Assets',
                data: @json($assetTrends['data']),
                borderColor: 'rgb(0, 102, 51)',
                backgroundColor: 'rgba(0, 102, 51, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Distribution by Category Chart
    const distributionCategoryCtx = document.getElementById('distributionCategoryChart').getContext('2d');
    new Chart(distributionCategoryCtx, {
        type: 'doughnut',
        data: {
            labels: @json($distributionByCategory['labels']),
            datasets: [{
                data: @json($distributionByCategory['data']),
                backgroundColor: [
                    'rgba(0, 102, 51, 0.8)',
                    'rgba(212, 175, 55, 0.8)',
                    'rgba(0, 77, 38, 0.8)',
                    'rgba(40, 167, 69, 0.8)',
                    'rgba(25, 135, 84, 0.8)',
                    'rgba(20, 108, 67, 0.8)',
                    'rgba(15, 81, 50, 0.8)',
                    'rgba(10, 54, 33, 0.8)',
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true
        }
    });

    // Monthly Distributions Chart
    const monthlyDistributionsCtx = document.getElementById('monthlyDistributionsChart').getContext('2d');
    new Chart(monthlyDistributionsCtx, {
        type: 'bar',
        data: {
            labels: @json($monthlyDistributions['labels']),
            datasets: [{
                label: 'Distributions',
                data: @json($monthlyDistributions['data']),
                backgroundColor: 'rgba(0, 102, 51, 0.8)'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Year Comparison Chart
    const yearComparisonCtx = document.getElementById('yearComparisonChart').getContext('2d');
    new Chart(yearComparisonCtx, {
        type: 'bar',
        data: {
            labels: @json($yearComparison['labels']),
            datasets: [{
                label: 'Zakat Due',
                data: @json($yearComparison['zakatDue']),
                backgroundColor: 'rgba(220, 53, 69, 0.8)'
            }, {
                label: 'Zakat Paid',
                data: @json($yearComparison['zakatPaid']),
                backgroundColor: 'rgba(40, 167, 69, 0.8)'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Asset Breakdown Chart
    const assetBreakdownCtx = document.getElementById('assetBreakdownChart').getContext('2d');
    new Chart(assetBreakdownCtx, {
        type: 'pie',
        data: {
            labels: @json($assetBreakdown['labels']),
            datasets: [{
                data: @json($assetBreakdown['data']),
                backgroundColor: [
                    'rgba(0, 102, 51, 0.8)',
                    'rgba(212, 175, 55, 0.8)',
                    'rgba(0, 77, 38, 0.8)',
                    'rgba(40, 167, 69, 0.8)',
                    'rgba(25, 135, 84, 0.8)',
                    'rgba(20, 108, 67, 0.8)',
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true
        }
    });

    // Debt Breakdown Chart
    const debtBreakdownCtx = document.getElementById('debtBreakdownChart').getContext('2d');
    new Chart(debtBreakdownCtx, {
        type: 'pie',
        data: {
            labels: @json($debtBreakdown['labels']),
            datasets: [{
                data: @json($debtBreakdown['data']),
                backgroundColor: [
                    'rgba(220, 53, 69, 0.8)',
                    'rgba(255, 193, 7, 0.8)',
                    'rgba(255, 152, 0, 0.8)',
                    'rgba(255, 87, 34, 0.8)',
                    'rgba(244, 67, 54, 0.8)',
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true
        }
    });
</script>
@endpush
@endsection

