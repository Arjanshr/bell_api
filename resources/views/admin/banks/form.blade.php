@extends('adminlte::page')

@section('title', isset($bank) ? 'Edit Bank' : 'Create Bank')

@section('content_header')
    <h1>{{ isset($bank) ? 'Edit Bank' : 'Create Bank' }}</h1>
@stop

@section('content')
    <div class="card-body">
        <section class="content">
            <div class="container-fluid">
                <div class="row">

                    <div class="col-md-6">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">{{ isset($bank) ? 'Edit Bank' : 'Create New Bank' }}</h3>
                            </div>
                            <form method="POST"
                                action="{{ isset($bank) ? route('banks.update', $bank->id) : route('banks.insert') }}">
                                @csrf
                                @if (isset($bank))
                                    @method('patch')
                                @endif
                                <div class="card-body">
                                    <!-- Bank Name -->
                                    <div class="form-group">
                                        <label for="name">Bank Name*</label>
                                        <input type="text" class="form-control" id="name" name="name"
                                            placeholder="Enter bank name"
                                            value="{{ old('name', isset($bank) ? $bank->name : '') }}" required>
                                        @error('name')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Minimum EMI Price -->
                                    <div class="form-group">
                                        <label for="min_emi_price">Minimum EMI Price</label>
                                        <input type="number" step="0.01" class="form-control" id="min_emi_price"
                                            name="min_emi_price" placeholder="Enter minimum EMI price"
                                            value="{{ old('min_emi_price', isset($bank) ? $bank->min_emi_price : '') }}">
                                        @error('min_emi_price')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <input type="submit" value="{{ isset($bank) ? 'Update' : 'Create' }}"
                                            class="btn btn-primary" />
                                    </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </section>
    </div>
@stop

@section('css')
@stop

@section('js')
@stop
