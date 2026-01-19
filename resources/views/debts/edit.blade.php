@extends('layouts.bootstrap')

@section('title', 'Edit Debt')

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-pencil"></i> Edit Debt
            </div>
            <div class="card-body">
                <form action="{{ route('debts.update', $debt) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="type" class="form-label">Type</label>
                        <select class="form-select" id="type" name="type" required>
                            <option value="loans_due" {{ $debt->type === 'loans_due' ? 'selected' : '' }}>Loans Due</option>
                            <option value="credit_cards" {{ $debt->type === 'credit_cards' ? 'selected' : '' }}>Credit Card Balance</option>
                            <option value="rent" {{ $debt->type === 'rent' ? 'selected' : '' }}>Rent Due</option>
                            <option value="bills" {{ $debt->type === 'bills' ? 'selected' : '' }}>Bills</option>
                            <option value="salary_owed" {{ $debt->type === 'salary_owed' ? 'selected' : '' }}>Salary Owed</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount</label>
                        <input type="number" step="0.01" class="form-control" id="amount" name="amount" value="{{ $debt->amount }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3">{{ $debt->description }}</textarea>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Update Debt</button>
                        <a href="{{ route('debts.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

