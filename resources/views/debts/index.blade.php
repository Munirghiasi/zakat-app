@extends('layouts.bootstrap')

@section('title', 'Debts')

@section('content')
@php
    $currencyService = app(\App\Services\CurrencyService::class);
    $user = auth()->user();
@endphp

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-credit-card"></i> Debts</h2>
            <a href="{{ route('debts.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Add Debt
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($debts as $debt)
                            <tr>
                                <td>{{ ucfirst(str_replace('_', ' ', $debt->type)) }}</td>
                                <td>{{ $currencyService->format($debt->amount, $user->currency, $user) }}</td>
                                <td>{{ Str::limit($debt->description, 50) }}</td>
                                <td>
                                    <a href="{{ route('debts.edit', $debt) }}" class="btn btn-sm btn-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('debts.destroy', $debt) }}" method="POST" class="d-inline">
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
                                <td colspan="4" class="text-center text-muted">No debts recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

