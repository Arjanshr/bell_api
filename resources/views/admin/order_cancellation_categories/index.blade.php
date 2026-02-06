@extends('adminlte::page')

@section('title', 'Cancellation Categories')

@section('content_header')
    <h1>Cancellation Categories</h1>
@stop

@push('js')
    <script>
        (function(){
            function loadSwal(callback){
                if (window.Swal || window.sweetAlert || window.swal) return callback();
                var s = document.createElement('script');
                s.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11';
                s.onload = callback;
                document.head.appendChild(s);
            }

            function confirmAndSubmit(form){
                var name = form.dataset.name || '';
                loadSwal(function(){
                    var sw = window.Swal || window.sweetAlert || window.swal;
                    if (!sw) {
                        if (confirm('Delete "' + name + '"?')) form.submit();
                        return;
                    }
                    sw.fire({
                        title: 'Are you sure?',
                        text: name ? ('Delete "' + name + '"? This action cannot be undone.') : 'Delete this item? This action cannot be undone.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete it!'
                    }).then(function(result){
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            }

            document.addEventListener('click', function(e){
                var btn = e.target.closest && e.target.closest('button[type="submit"]');
                if (!btn) return;
                var form = btn.form;
                if (!form || !form.classList.contains('swal-delete-form')) return;
                e.preventDefault();
                confirmAndSubmit(form);
            });
        })();
    </script>
@endpush

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Cancellation Categories</h3>
        <div class="card-tools">
            <a href="{{ route('order-cancellation-categories.create') }}" class="btn btn-sm btn-success">Create</a>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categories as $cat)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $cat->name }}</td>
                        <td>{{ $cat->status ? 'Active' : 'Inactive' }}</td>
                        <td>
                            <a href="{{ route('order-cancellation-categories.edit', $cat->id) }}" class="btn btn-sm btn-primary">Edit</a>
                            <form action="{{ route('order-cancellation-categories.delete', $cat->id) }}" method="post" class="swal-delete-form" style="display:inline-block" data-name="{{ $cat->name }}">
                                @csrf
                                @method('delete')
                                <button class="btn btn-sm btn-danger" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@stop
