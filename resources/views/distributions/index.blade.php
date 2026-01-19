@extends('layouts.bootstrap')

@section('title', 'Distributions')

@section('content')
@php
    $currencyService = app(\App\Services\CurrencyService::class);
    $user = auth()->user();
@endphp

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-cash-coin"></i> Distributions</h2>
            <a href="{{ route('distributions.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Record Distribution
            </a>
        </div>
    </div>
</div>

@if($calculation)
<div class="row mb-4">
    <div class="col-md-12">
        <div class="alert alert-info">
            <strong>Zakat Remaining:</strong> {{ $currencyService->format($calculation->zakat_remaining, $user->currency, $user) }} | 
            <strong>Zakat Paid:</strong> {{ $currencyService->format($calculation->zakat_paid, $user->currency, $user) }} | 
            <strong>Total Due:</strong> {{ $currencyService->format($calculation->zakat_due, $user->currency, $user) }}
        </div>
    </div>
</div>
@endif

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Recipient</th>
                            <th>Category</th>
                            <th>Amount</th>
                            <th>Notes</th>
                            <th>Actions</th>
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
                                <td>{{ $currencyService->format($distribution->amount, $user->currency, $user) }}</td>
                                <td>{{ Str::limit($distribution->notes, 30) }}</td>
                                <td>
                                    @if($distribution->receipt_path)
                                        <a href="{{ asset('storage/' . $distribution->receipt_path) }}" target="_blank" class="btn btn-sm btn-info">
                                            <i class="bi bi-file-earmark"></i>
                                        </a>
                                    @endif
                                    <a href="{{ route('distributions.edit', $distribution) }}" class="btn btn-sm btn-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('distributions.destroy', $distribution) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">No distributions recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

