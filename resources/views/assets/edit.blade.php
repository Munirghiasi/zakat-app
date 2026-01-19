@extends('layouts.bootstrap')

@section('title', 'Edit Asset')

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-pencil"></i> Edit Asset
            </div>
            <div class="card-body">
                <form action="{{ route('assets.update', $asset) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="type" class="form-label">Type</label>
                        <select class="form-select" id="type" name="type" required>
                            <option value="zakatable" {{ $asset->type === 'zakatable' ? 'selected' : '' }}>Zakatable</option>
                            <option value="non_zakatable" {{ $asset->type === 'non_zakatable' ? 'selected' : '' }}>Non-Zakatable</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="category" class="form-label">Category</label>
                        <select class="form-select" id="category" name="category" required>
                            <option value="cash" {{ $asset->category === 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="bank" {{ $asset->category === 'bank' ? 'selected' : '' }}>Bank Balance</option>
                            <option value="gold" {{ $asset->category === 'gold' ? 'selected' : '' }}>Gold</option>
                            <option value="silver" {{ $asset->category === 'silver' ? 'selected' : '' }}>Silver</option>
                            <option value="business_inventory" {{ $asset->category === 'business_inventory' ? 'selected' : '' }}>Business Inventory</option>
                            <option value="money_owed" {{ $asset->category === 'money_owed' ? 'selected' : '' }}>Money Owed</option>
                            <option value="crypto" {{ $asset->category === 'crypto' ? 'selected' : '' }}>Crypto</option>
                            <option value="investments" {{ $asset->category === 'investments' ? 'selected' : '' }}>Investments</option>
                            <option value="house" {{ $asset->category === 'house' ? 'selected' : '' }}>House</option>
                            <option value="car" {{ $asset->category === 'car' ? 'selected' : '' }}>Car</option>
                            <option value="furniture" {{ $asset->category === 'furniture' ? 'selected' : '' }}>Furniture</option>
                            <option value="clothes" {{ $asset->category === 'clothes' ? 'selected' : '' }}>Clothes</option>
                            <option value="phone" {{ $asset->category === 'phone' ? 'selected' : '' }}>Phone</option>
                            <option value="laptop" {{ $asset->category === 'laptop' ? 'selected' : '' }}>Laptop</option>
                            <option value="work_tools" {{ $asset->category === 'work_tools' ? 'selected' : '' }}>Work Tools</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount</label>
                        <input type="number" step="0.01" class="form-control" id="amount" name="amount" value="{{ $asset->amount }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity (grams)</label>
                        <input type="number" step="0.0001" class="form-control" id="quantity" name="quantity" value="{{ $asset->quantity }}">
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3">{{ $asset->description }}</textarea>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Update Asset</button>
                        <a href="{{ route('assets.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

