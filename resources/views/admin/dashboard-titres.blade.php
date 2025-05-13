@extends('layouts.back')

@section('subtitle', __('Dashboard Titres'))

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="section-title text-primary">Dashboard des Titres</h2>
    </div>
    <div class="section-body mt-4">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-12 col-md-12 col-12">
                    <div class="card shadow-lg">
                        @livewire('dashboard-titres')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <style>
        .card {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 25px 0 rgba(0, 0, 0, 0.1);
        }
        
        canvas {
            max-height: 300px;
        }
    </style>
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush
