@extends('layouts.admin')

@section('content')
<div class="container">
    <h2>Edit Hosting Plan</h2>

    <form method="POST" action="{{ route('admin.hosting_plans.update', $plan->id) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" value="{{ $plan->name }}">
        </div>

        <div class="mb-3">
            <label>Description</label>
            <textarea name="description" class="form-control" rows="4">{{ $plan->description }}</textarea>
        </div>

        <button type="submit" class="btn btn-success">Update</button>
        <a href="{{ route('admin.hosting_plans.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
