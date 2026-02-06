@extends('adminlte::page')

@section('title', 'Contact Message')

@section('content_header')
    <h1>Contact Message</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <a href="{{ route('contact-messages') }}" class="btn btn-secondary mb-3">Back to list</a>

            <table class="table table-bordered">
                <tr>
                    <th>First Name</th>
                    <td>{{ $message->first_name }}</td>
                </tr>
                <tr>
                    <th>Last Name</th>
                    <td>{{ $message->last_name }}</td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td>{{ $message->email }}</td>
                </tr>
                <tr>
                    <th>Contact Number</th>
                    <td>{{ $message->contact_number }}</td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>
                        <span class="badge badge-{{ $message->status->color() }}">{{ $message->status->label() }}</span>
                    </td>
                </tr>
                <tr>
                    <th>Received At</th>
                    <td>{{ $message->created_at->format('Y-m-d H:i') }}</td>
                </tr>
                <tr>
                    <th>Message</th>
                    <td style="white-space:pre-wrap">{{ $message->message }}</td>
                </tr>
            </table>

            <form action="{{ route('contact-message.delete', $message->id) }}" method="POST" onsubmit="return confirm('Delete this message?');" class="d-inline">
                @csrf
                @method('DELETE')
                <button class="btn btn-danger">Delete</button>
            </form>

            @if ($message->status->value !== 'answered')
                <form action="{{ route('contact-message.mark-contacted', $message->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button class="btn btn-success">Mark as Contacted</button>
                </form>
            @endif
        </div>
    </div>
@stop
