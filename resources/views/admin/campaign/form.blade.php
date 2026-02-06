@extends('adminlte::page')

@section('title', 'Campaigns')

@section('content_header')
    <h1>Campaigns</h1>
@stop

@section('content')
    <div class="card-body">
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <!-- Removed Sortable Campaign List -->
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">{{ isset($campaign) ? 'Edit Campaign' : 'Create New Campaign' }}</h3>
                            </div>

                            <form method="POST"
                                action="{{ isset($campaign) ? route('campaigns.update', $campaign->id) : route('campaigns.insert') }}"
                                enctype="multipart/form-data">
                                @csrf
                                @if (isset($campaign))
                                    @method('patch')
                                @endif

                                <!-- INNER CARD BODY STARTS HERE -->
                                <div class="card-body">
                                    <div class="row">
                                        <!-- Name -->
                                        <div class="form-group col-md-12">
                                            <label for="name">Name*</label>
                                            <input type="text" class="form-control" id="name" name="name"
                                                placeholder="Name"
                                                value="{{ isset($campaign) ? $campaign->name : old('name') }}" required>
                                            @error('name')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row">
                                        <!-- Start Date -->
                                        <div class="form-group col-md-6">
                                            <label for="start_date">Start Date*</label>
                                            <input type="date" class="form-control" id="start_date" name="start_date"
                                                value="{{ isset($campaign) ? $campaign->start_date : old('start_date') }}"
                                                required>
                                            @error('start_date')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- End Date -->
                                        <div class="form-group col-md-6">
                                            <label for="end_date">End Date*</label>
                                            <input type="date" class="form-control" id="end_date" name="end_date"
                                                value="{{ isset($campaign) ? $campaign->end_date : old('end_date') }}"
                                                required>
                                            @error('end_date')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row">
                                        <!-- Has Active Period -->
                                        <div class="form-group col-md-4">
                                            <label for="has_active_period">
                                                <input type="checkbox" id="has_active_period" name="has_active_period"
                                                    {{ (isset($campaign) && $campaign->has_active_period) || old('has_active_period') ? 'checked' : '' }}>
                                                Has Active Period (Daily Flash Sale)?
                                            </label>
                                            @error('has_active_period')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Start Time -->
                                        <div class="form-group col-md-4 flash-time" style="display: none;">
                                            <label for="start_time">Start Time</label>
                                            @php
                                                $start_time = old(
                                                    'start_time',
                                                    isset($campaign) && $campaign->start_time
                                                        ? \Illuminate\Support\Carbon::parse(
                                                            $campaign->start_time,
                                                        )->format('H:i')
                                                        : '',
                                                );
                                            @endphp
                                            <input type="time" name="start_time" value="{{ $start_time }}"
                                                class="form-control" />
                                            @error('start_time')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- End Time -->
                                        <div class="form-group col-md-4 flash-time" style="display: none;">
                                            <label for="end_time">End Time</label>
                                            @php
                                                $end_time = old(
                                                    'end_time',
                                                    isset($campaign) && $campaign->end_time
                                                        ? \Illuminate\Support\Carbon::parse(
                                                            $campaign->end_time,
                                                        )->format('H:i')
                                                        : '',
                                                );
                                            @endphp
                                            <input type="time" name="end_time" value="{{ $end_time }}"
                                                class="form-control" />
                                            @error('end_time')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row">
                                        <!-- Status -->
                                        <div class="form-group col-md-6">
                                            <label for="status">Status*</label>
                                            <select id="status" name="status" class="form-control" required>
                                                <option value="">Select a status</option>
                                                <option value="active"
                                                    {{ (isset($campaign) && $campaign->status == 'active') || old('status') == 'active' ? 'selected' : '' }}>
                                                    Active
                                                </option>
                                                <option value="inactive"
                                                    {{ (isset($campaign) && $campaign->status == 'inactive') || old('status') == 'inactive' ? 'selected' : '' }}>
                                                    Inactive
                                                </option>
                                            </select>
                                            @error('status')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Color Theme -->
                                        <div class="form-group col-md-6">
                                            <label for="color_theme">Color Theme</label>
                                            <input type="color" class="form-control" id="color_theme" name="color_theme"
                                                value="{{ isset($campaign) ? $campaign->color_theme : old('color_theme', '#ffffff') }}">
                                            @error('color_theme')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row">
                                        <!-- Campaign Type -->
                                        <div class="form-group col-md-6">
                                            <label for="type">Campaign Type*</label>
                                            <select id="type" name="type" class="form-control" required>
                                                <option value="">Select type</option>
                                                <option value="discount"
                                                    {{ old('type', $campaign->type ?? '') === 'discount' ? 'selected' : '' }}>
                                                    Discount</option>
                                                <option value="free_delivery"
                                                    {{ old('type', $campaign->type ?? '') === 'free_delivery' ? 'selected' : '' }}>
                                                    Free Delivery</option>
                                                <option value="banner"
                                                    {{ old('type', $campaign->type ?? '') === 'banner' ? 'selected' : '' }}>
                                                    Banner</option>
                                                <option value="offers"
                                                    {{ old('type', $campaign->type ?? '') === 'offers' ? 'selected' : '' }}>
                                                    Offers</option>
                                            </select>
                                            @error('type')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row" id="min-cart-value-row" style="display: none;">
                                        <div class="form-group col-md-6">
                                            <label for="min_cart_value">Minimum Cart Value (Rs.)</label>
                                            <input type="number" step="0.01" class="form-control" id="min_cart_value"
                                                name="min_cart_value"
                                                value="{{ old('min_cart_value', $campaign->min_cart_value ?? '') }}">
                                            @error('min_cart_value')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row">
                                        <!-- Background Image -->
                                        <div class="form-group col-md-4">
                                            <label for="background_image">Background Image</label>
                                            @if (isset($campaign) && $campaign->background_image)
                                                <div class="mt-2">
                                                    <img src="{{ asset('storage/' . $campaign->background_image) }}"
                                                        alt="Background Image" width="150">
                                                </div>
                                                <div class="form-check">
                                                    <input type="checkbox" class="form-check-input"
                                                        id="remove_background_image" name="remove_background_image"
                                                        value="1">
                                                    <label class="form-check-label" for="remove_background_image">Remove
                                                        current image</label>
                                                </div>
                                            @else
                                                <input type="file" class="form-control" id="background_image"
                                                    name="background_image">
                                                @error('background_image')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            @endif
                                        </div>

                                        <!-- Campaign Banner -->
                                        <div class="form-group col-md-4">
                                            <label for="campaign_banner">Campaign Banner</label>
                                            @if (isset($campaign) && $campaign->campaign_banner)
                                                <div class="mt-2">
                                                    <img src="{{ asset('storage/' . $campaign->campaign_banner) }}"
                                                        alt="Campaign Banner" width="150">
                                                </div>
                                                <div class="form-check">
                                                    <input type="checkbox" class="form-check-input"
                                                        id="remove_campaign_banner" name="remove_campaign_banner"
                                                        value="1">
                                                    <label class="form-check-label" for="remove_campaign_banner">Remove
                                                        current banner</label>
                                                </div>
                                            @else
                                                <input type="file" class="form-control" id="campaign_banner"
                                                    name="campaign_banner">
                                                @error('campaign_banner')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            @endif
                                        </div>

                                        <!-- Banner URL -->
                                        <div class="form-group col-md-4">
                                            <label for="banner_url">Banner Link (URL - optional)</label>
                                            <input type="url" id="banner_url" name="banner_url" class="form-control"
                                                placeholder="https://example.com/category/smartphones"
                                                value="{{ old('banner_url', $campaign->banner_url ?? '') }}">
                                            @error('banner_url')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row">
                                        <!-- Submit Button -->
                                        <div class="form-group col-md-12 text-center">
                                            <input id="submit" type="submit"
                                                value="{{ isset($campaign) ? 'Edit' : 'Create' }}"
                                                class="btn btn-primary" />
                                        </div>
                                    </div>
                                </div>
                                <!-- INNER CARD BODY ENDS HERE -->

                            </form>
                        </div> <!-- /.card -->
                    </div> <!-- /.col-md-12 -->
                </div> <!-- /.row -->
            </div> <!-- /.container-fluid -->
        </section>
    </div> <!-- /.card-body -->
@stop



@section('css')
@stop

@section('js')
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script>
        $(function() {
            $("#sortable-campaigns").sortable({
                update: function(event, ui) {
                    let order = $(this).sortable('toArray', {
                        attribute: 'data-id'
                    });
                    $.ajax({
                        url: "{{ route('campaigns.updateOrder') }}",
                        method: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            order: order
                        },
                        success: function(response) {
                            alert('Order updated successfully!');
                        },
                        error: function() {
                            alert('Failed to update order.');
                        }
                    });
                }
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            function toggleFlashTimeFields() {
                if ($('#has_active_period').is(':checked')) {
                    $('.flash-time').show();
                } else {
                    $('.flash-time').hide();
                }
            }

            // On page load
            toggleFlashTimeFields();

            // On checkbox change
            $('#has_active_period').change(function() {
                toggleFlashTimeFields();
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkbox = document.querySelector('input[name="has_active_period"]');
            const startTimeInput = document.querySelector('input[name="start_time"]');
            const endTimeInput = document.querySelector('input[name="end_time"]');

            checkbox.addEventListener('change', function() {
                if (!this.checked) {
                    startTimeInput.value = '';
                    endTimeInput.value = '';
                }
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            function toggleMinCartValueField() {
                if ($('#type').val() === 'free_delivery') {
                    $('#min-cart-value-row').show();
                } else {
                    $('#min-cart-value-row').hide();
                    $('#min_cart_value').val('');
                }
            }

            // On page load
            toggleMinCartValueField();

            // On type change
            $('#type').change(function() {
                toggleMinCartValueField();
            });
        });
    </script>

@stop
