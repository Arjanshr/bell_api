@extends('adminlte::page')

@section('title', 'Processing Fee Rules')

@section('content_header')
    <h1>Processing Fee Rules</h1>
@stop

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <a href="{{ route('processing-fee-rules.create') }}" class="btn btn-success">Add Processing Fee Rule</a>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Bank</th>
                            <th>Type</th>
                            <th>Value</th>
                            <th>Min Fee</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rules as $rule)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $rule->bank->name }}</td>
                                <td>{{ ucfirst($rule->type) }}</td>
                                <td>{{ $rule->value }}</td>
                                <td>{{ $rule->min_fee ?? '-' }}</td>
                                <td>
                                    <a href="{{ route('processing-fee-rules.edit', $rule->id) }}" class="btn btn-sm btn-primary"><i class="fa fa-edit"></i></a>
                                    <form method="POST" action="{{ route('processing-fee-rules.destroy', $rule->id) }}" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger delete">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $rules->links() }}
            </div>
        </div>
    </div>
</section>
@stop

@section('js')
<script>
    $(document).on('click', '.delete', function(e) {
        e.preventDefault();
        const form = $(this).closest('form');
        Swal.fire({
            title: 'Delete this rule?',
            text: "This action cannot be undone.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
</script>
@stop
