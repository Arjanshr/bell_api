@extends('adminlte::master')

@section('adminlte_css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @stack('css')
    @yield('css')
@stop

@section('body_class', 'hold-transition ' . config('adminlte.classes_body') . ' ' . (config('adminlte.sidebar_mini') ?
    'sidebar-mini ' : '') . (config('adminlte.layout_topnav') ? 'layout-top-nav ' : '') . (config('adminlte.layout_boxed') ?
    'layout-boxed ' : '') . (config('adminlte.collapse_sidebar') ? 'sidebar-collapse ' : '') .
    (config('adminlte.right_sidebar') ? 'control-sidebar-slide-open ' : ''))

@section('body')
    <div class="wrapper">

        {{-- Navbar --}}
        @include('adminlte::partials.navbar.navbar')

        {{-- Left Main Sidebar --}}
        @include('adminlte::partials.sidebar.left-sidebar')

        {{-- Content Wrapper. Contains page content --}}
        <div class="content-wrapper">
            {{-- Content Header (Page header) --}}
            @hasSection('content_header')
                <section class="content-header">
                    <div class="{{ config('adminlte.classes_content_header', 'container-fluid') }}">
                        @yield('content_header')
                    </div>
                </section>
            @endif

            {{-- Main content --}}
            <section class="content">
                <div class="{{ config('adminlte.classes_content', 'container-fluid') }}">
                    @yield('content')
                </div>
            </section>
        </div>
        <div id="fb-root"></div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                if (window.FB && FB.XFBML) {
                    FB.XFBML.parse();
                }
            });
        </script>
        {{-- Footer --}}
        @hasSection('footer')
            <footer class="main-footer">
                @yield('footer')
            </footer>
        @endif

        {{-- Right Control Sidebar --}}
        @hasSection('right-sidebar')
            <aside class="control-sidebar control-sidebar-{{ config('adminlte.right_sidebar_theme') }}">
                @yield('right-sidebar')
            </aside>
        @endif
    </div>

    <!-- Load CKEditor from CDN -->
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>

    <!-- Load your CKEditor initializer -->
    <script src="{{ asset('js/ckeditor-init.js') }}"></script>

    <!-- Run it on all .ckeditor elements -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            initCkeditor();
        });

        // Optional: for Livewire or AJAX content updates
        document.addEventListener('ckeditor:refresh', function() {
            initCkeditor();
        });
    </script>
@stop

@section('adminlte_js')
    @stack('js')
    @yield('js')
@stop
