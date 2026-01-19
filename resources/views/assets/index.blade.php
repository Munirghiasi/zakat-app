@extends('layouts.bootstrap')

@section('title', 'Assets')

@section('content')
@php
    $currencyService = app(\App\Services\CurrencyService::class);
    $user = auth()->user();
@endphp

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-wallet2"></i> Assets</h2>
            <a href="{{ route('assets.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Add Asset
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
                            <th>Category</th>
                            <th>Amount</th>
                            <th>Quantity</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assets as $asset)
                            <tr>
                                <td>
                                    <span class="badge {{ $asset->type === 'zakatable' ? 'bg-success' : 'bg-secondary' }}">
                                        {{ ucfirst($asset->type) }}
                                    </span>
                                </td>
                                <td>{{ ucfirst(str_replace('_', ' ', $asset->category)) }}</td>
                                <td>{{ $currencyService->format($asset->amount, $user->currency, $user) }}</td>
                                <td>{{ $asset->quantity ? number_format($asset->quantity, 4) : '-' }}</td>
                                <td>{{ Str::limit($asset->description, 50) }}</td>
                                <td>
                                    <a href="{{ route('assets.edit', $asset) }}" class="btn btn-sm btn-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('assets.destroy', $asset) }}" method="POST" class="d-inline">
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
                                <td colspan="6" class="text-center text-muted">No assets recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

