@extends('adminlte::page')

@section('title', 'Contact Messages')

@section('content_header')
    <h1>Contact Messages</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form method="GET" class="form-inline mb-3">
                <div class="input-group">
                    <input type="text" name="q" class="form-control" placeholder="Search name, email or contact" value="{{ old('q', $q ?? '') }}">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="submit">Search</button>
                    </div>
                </div>
            </form>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Contact Number</th>
                            <th>Status</th>
                            <th>Received</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($messages as $m)
                            <tr>
                                <td>
                                    <a href="{{ route('contact-message.show', $m->id) }}">{{ $m->first_name }} {{ $m->last_name }}</a>
                                </td>
                                <td>{{ $m->email }}</td>
                                <td>{{ $m->contact_number }}</td>
                                <td>
                                    <span class="badge badge-{{ $m->status->color() }}">{{ $m->status->label() }}</span>
                                </td>
                                <td>{{ $m->created_at->format('Y-m-d H:i') }}</td>
                                <td>
                                    <a href="{{ route('contact-message.show', $m->id) }}" class="btn btn-sm btn-primary">View</a>
                                    <form action="{{ route('contact-message.delete', $m->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Delete this message?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">No messages found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $messages->links() }}
            </div>
        </div>
    </div>
@stop
