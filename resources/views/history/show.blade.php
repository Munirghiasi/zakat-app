@extends('layouts.bootstrap')

@section('title', 'History - ' . $zakatYear->year)

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-clock-history"></i> Zakat Year {{ $zakatYear->year }}</h2>
            <a href="{{ route('history.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to History
            </a>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-info-circle"></i> Year Information
            </div>
            <div class="card-body">
                <p><strong>Period:</strong> {{ $zakatYear->start_date->format('M d, Y') }} - {{ $zakatYear->end_date->format('M d, Y') }}</p>
                <p><strong>Status:</strong> 
                    @if($zakatYear->is_locked)
                        <span class="badge bg-danger">Locked</span>
                    @else
                        <span class="badge bg-success">Active</span>
                    @endif
                </p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-cash-coin"></i> Distributions
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Recipient</th>
                            <th>Category</th>
                            <th>Amount</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($distributions as $distribution)
                            <tr>
                                <td>{{ $distribution->distribution_date->format('M d, Y') }}</td>
                                <td>{{ $distribution->recipient->name ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-success">
                                        {{ \App\Models\Recipient::getValidCategories()[$distribution->category] ?? $distribution->category }}
                                    </span>
                                </td>
                                <td>{{ number_format($distribution->amount, 2) }}</td>
                                <td>{{ $distribution->notes }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">No distributions recorded for this year.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($distributions->count() > 0)
                        <tfoot>
                            <tr class="table-primary">
                                <td colspan="3"><strong>Total:</strong></td>
                                <td><strong>{{ number_format($distributions->sum('amount'), 2) }}</strong></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

