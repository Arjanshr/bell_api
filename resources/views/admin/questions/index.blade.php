@extends('adminlte::page')

@section('title', 'Questions')

@section('content_header')
    <h1>Questions</h1>
@stop
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card mb-4">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <form method="GET" action="" class="mb-0 w-100">
                                <div class="d-flex flex-wrap align-items-end" style="gap: 1rem;">
                                    <div style="flex:1 1 220px; min-width:220px;">
                                        <label for="filter" class="small mb-1">Search</label>
                                        <input type="text" name="filter" id="filter" value="{{ request('filter') }}" class="form-control" placeholder="Search question...">
                                    </div>
                                    <div style="flex:1 1 180px; min-width:180px;">
                                        <label for="status" class="small mb-1">Status</label>
                                        <select name="status" id="status" class="form-control">
                                            <option value="">All Status</option>
                                            <option value="unanswered" @if(request('status')=='unanswered') selected @endif>Unanswered</option>
                                            <option value="answered" @if(request('status')=='answered') selected @endif>Answered</option>
                                        </select>
                                    </div>
                                    <div style="flex:1 1 180px; min-width:180px;">
                                        <label for="category" class="small mb-1">Category</label>
                                        <select name="category" id="category" class="form-control">
                                            <option value="">All Categories</option>
                                            @foreach($categories as $cat)
                                                <option value="{{ $cat->id }}" @if(request('category')==$cat->id) selected @endif>{{ $cat->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div style="flex:0 0 120px; min-width:120px;">
                                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>User</th>
                                            <th>Product</th>
                                            <th>Question</th>
                                            <th>Answer</th>
                                            <th>Status</th>
                                            <th>Created At</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($questions as $q)
                                            <tr>
                                                <td>{{ $q->user ? $q->user->name : 'Guest' }}</td>
                                                <td>{{ $q->product ? $q->product->name : 'N/A' }}</td>
                                                <td>{{ $q->question }}</td>
                                                <td>{{ $q->answer }}</td>
                                                <td>
                                                    @if($q->status === 'answered')
                                                        <span class="badge badge-success">Answered</span>
                                                    @elseif($q->status === 'unanswered')
                                                        <span class="badge badge-warning">Unanswered</span>
                                                    @else
                                                        <span class="badge badge-secondary">{{ ucfirst($q->status) }}</span>
                                                    @endif
                                                </td>
                                                <td>{{ $q->created_at }}</td>
                                                <td class="d-flex align-items-center" style="gap:0.5rem;">
                                                    @if($q->status === 'unanswered')
                                                        <a href="{{ route('questions.answer', $q->id) }}" class="btn btn-sm btn-primary">Answer</a>
                                                    @else
                                                        <span class="text-success">Answered</span>
                                                    @endif
                                                    <form method="POST" action="{{ route('questions.delete', $q->id) }}" style="display:inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="btn btn-sm btn-danger btn-delete-question">Delete</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6">No questions found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop

@section('css')
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.btn-delete-question').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const form = btn.closest('form');
                    Swal.fire({
                        title: 'Are you sure?',
                        text: 'This will permanently delete the question.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
@stop
