@extends('adminlte::page')

@section('title', isset($bankTenure) ? 'Edit Bank Tenure' : 'Add Bank Tenure')

@section('content_header')
    <h1>{{ isset($bankTenure) ? 'Edit Bank Tenure' : 'Add Bank Tenure' }}</h1>
@stop

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <div class="card card-primary">
                        <form
                            action="{{ isset($bankTenure) ? route('banks-tenures.update', $bankTenure->id) : route('banks-tenures.insert') }}"
                            method="POST">
                            @csrf
                            @if (isset($bankTenure))
                                @method('PATCH')
                            @endif

                            <div class="card-body">
                                <div class="form-group">
                                    <label for="bank_id">Bank</label>
                                    <select name="bank_id" id="bank_id" class="form-control" required>
                                        <option value="">Select Bank</option>
                                        @foreach ($banks as $bank)
                                            <option value="{{ $bank->id }}"
                                                {{ (old('bank_id') ?? ($bankTenure->bank_id ?? '')) == $bank->id ? 'selected' : '' }}>
                                                {{ $bank->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('bank_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="months">Months</label>
                                    <input type="number" class="form-control" name="months" id="months"
                                        value="{{ old('months', $bankTenure->months ?? '') }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="service_charge_percent">Service Charge (%)</label>
                                    <input type="number" step="0.01" min="0" name="service_charge_percent"
                                        id="service_charge_percent" class="form-control"
                                        value="{{ old('service_charge_percent') ?? ($bankTenure->service_charge_percent ?? '') }}"
                                        required>
                                    @error('service_charge_percent')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="min_service_charge_amount">Min Service Charge Amount</label>
                                    <input type="number" step="0.01" min="0" name="min_service_charge_amount"
                                        id="min_service_charge_amount" class="form-control"
                                        value="{{ old('min_service_charge_amount') ?? ($bankTenure->min_service_charge_amount ?? '') }}">
                                    @error('min_service_charge_amount')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="card-footer">
                                <button type="submit"
                                    class="btn btn-primary">{{ isset($bankTenure) ? 'Update' : 'Add' }}</button>
                                <a href="{{ route('banks-tenures.index') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop
