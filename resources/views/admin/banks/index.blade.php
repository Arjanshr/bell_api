@extends('adminlte::page')

@section('title', 'Banks')

@section('content_header')
    <h1>Banks</h1>
@stop

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        @can('add-banks')
                            <div class="card-header">
                                <a href="{{ route('banks.create') }}" class="btn btn-success">Add Bank</a>
                            </div>
                        @endcan
                        <div class="card-body">
                            <div id="banks_wrapper" class="dataTables_wrapper dt-bootstrap4">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <table id="banks" class="table table-bordered table-hover dataTable dtr-inline"
                                            aria-describedby="banks_info">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Actions</th>
                                                    <th>Bank Name</th>
                                                    <th>Minimum EMI Price</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($banks as $bank)
                                                    <tr>
                                                        <td width="20px">{{ $loop->iteration }}</td>
                                                        <td>
                                                            @can('edit-banks')
                                                                <a href="{{ route('banks.edit', $bank->id) }}"
                                                                    class="btn btn-sm btn-success" title="Edit">
                                                                    <i class="fa fa-pen"></i>
                                                                </a>
                                                            @endcan
                                                            @can('delete-banks')
                                                                <form method="post"
                                                                    action="{{ route('banks.delete', $bank->id) }}"
                                                                    style="display: inline;">
                                                                    @csrf
                                                                    @method('delete')
                                                                    <button class="delete btn btn-danger btn-sm" type="submit"
                                                                        title="Delete">
                                                                        <i class="fas fa-trash-alt"></i>
                                                                    </button>
                                                                </form>
                                                            @endcan
                                                        </td>
                                                        <td>{{ $bank->name }}</td>
                                                        <td>
                                                            {{ $bank->min_emi_price !== null ? number_format($bank->min_emi_price, 2) : '-' }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Actions</th>
                                                    <th>Bank Name</th>
                                                    <th>Minimum EMI Price</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                                {{ $banks->links() }}
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
    <script>
        $(document.body).on('click', '.delete', function(event) {
            event.preventDefault();
            var form = $(this).closest("form");
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
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

        $(document).ready(function() {
            $('#banks').DataTable();
            $('.dataTables_length').addClass('bs-select');
        });
    </script>
@stop
