@extends('adminlte::page')

@section('title', 'Bank Tenures')

@section('content_header')
    <h1>Bank Tenures</h1>
@stop

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    @can('add-bank-tenures')
                    <div class="card-header">
                        <a href="{{ route('banks-tenures.create') }}" class="btn btn-success">Add Tenure</a>
                    </div>
                    @endcan

                    <div class="card-body">
                        <table id="tenures" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Actions</th>
                                    <th>Bank</th>
                                    <th>Tenure (Months)</th>
                                    <th>Service Charge (%)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tenures as $tenure)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        @can('edit-bank-tenures')
                                        <a href="{{ route('banks-tenures.edit', $tenure->id) }}" class="btn btn-sm btn-success" title="Edit">
                                            <i class="fa fa-pen"></i>
                                        </a>
                                        @endcan
                                        @can('delete-bank-tenures')
                                        <form method="post" action="{{ route('banks-tenures.delete', $tenure->id) }}" style="display:inline;">
                                            @csrf
                                            @method('delete')
                                            <button class="delete btn btn-danger btn-sm" type="submit" title="Delete">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                        @endcan
                                    </td>
                                    <td>{{ $tenure->bank->name }}</td>
                                    <td>{{ $tenure->months }}</td>
                                    <td>
                                        {{ $tenure->service_charge_percent }}%
                                        @if($tenure->min_service_charge_amount > 0)
                                            <br>
                                            <small>Min: {{ number_format($tenure->min_service_charge_amount, 2) }}</small>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>#</th>
                                    <th>Actions</th>
                                    <th>Bank</th>
                                    <th>Tenure (Months)</th>
                                    <th>Service Charge (%)</th>
                                </tr>
                            </tfoot>
                        </table>
                        {{ $tenures->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
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
        $('#tenures').DataTable();
        $('.dataTables_length').addClass('bs-select');
    });
</script>
@stop
