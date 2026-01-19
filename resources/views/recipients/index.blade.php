@extends('layouts.bootstrap')

@section('title', 'Recipients')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-people"></i> Recipients</h2>
            <a href="{{ route('recipients.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Add Recipient
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
                            <th>Name</th>
                            <th>Category</th>
                            <th>Notes</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recipients as $recipient)
                            <tr>
                                <td>{{ $recipient->name }}</td>
                                <td>
                                    <span class="badge bg-success">{{ \App\Models\Recipient::getValidCategories()[$recipient->category] ?? $recipient->category }}</span>
                                </td>
                                <td>{{ Str::limit($recipient->notes, 50) }}</td>
                                <td>
                                    <a href="{{ route('recipients.edit', $recipient) }}" class="btn btn-sm btn-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('recipients.destroy', $recipient) }}" method="POST" class="d-inline">
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
                                <td colspan="4" class="text-center text-muted">No recipients added yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

