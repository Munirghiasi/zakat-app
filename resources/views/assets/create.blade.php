@extends('layouts.bootstrap')

@section('title', 'Add Asset')

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-plus-circle"></i> Add Asset
            </div>
            <div class="card-body">
                <form action="{{ route('assets.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="type" class="form-label">Type</label>
                        <select class="form-select" id="type" name="type" required>
                            <option value="">Select Type</option>
                            <option value="zakatable">Zakatable</option>
                            <option value="non_zakatable">Non-Zakatable</option>
                        </select>
                        @error('type')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="category" class="form-label">Category</label>
                        <select class="form-select" id="category" name="category" required>
                            <option value="">Select Category</option>
                            <optgroup label="Zakatable Assets">
                                <option value="cash">Cash</option>
                                <option value="bank">Bank Balance</option>
                                <option value="gold">Gold (grams)</option>
                                <option value="silver">Silver (grams)</option>
                                <option value="business_inventory">Business Inventory</option>
                                <option value="money_owed">Money Owed to You</option>
                                <option value="crypto">Crypto</option>
                                <option value="investments">Investments</option>
                            </optgroup>
                            <optgroup label="Non-Zakatable Assets">
                                <option value="house">House</option>
                                <option value="car">Car</option>
                                <option value="furniture">Furniture</option>
                                <option value="clothes">Clothes</option>
                                <option value="phone">Phone</option>
                                <option value="laptop">Laptop</option>
                                <option value="work_tools">Work Tools</option>
                            </optgroup>
                        </select>
                        @error('category')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount (in currency)</label>
                        <input type="number" step="0.01" class="form-control" id="amount" name="amount" value="{{ old('amount') }}" required>
                        @error('amount')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3" id="quantity-group" style="display: none;">
                        <label for="quantity" class="form-label">Quantity (grams for gold/silver)</label>
                        <input type="number" step="0.0001" class="form-control" id="quantity" name="quantity" value="{{ old('quantity') }}">
                        @error('quantity')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Add Asset</button>
                        <a href="{{ route('assets.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('category').addEventListener('change', function() {
        const category = this.value;
        const quantityGroup = document.getElementById('quantity-group');
        if (category === 'gold' || category === 'silver') {
            quantityGroup.style.display = 'block';
        } else {
            quantityGroup.style.display = 'none';
        }
    });
</script>
@endpush
@endsection

