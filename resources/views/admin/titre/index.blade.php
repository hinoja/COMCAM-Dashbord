@extends('layouts.back')

@section('subtitle', __('Liste des Titres'))

@section('content')
    <div class="section-body mt-4">
        <div class="container">
            <h2 class="section-title text-primary">Liste des Titres</h2>
            <hr class="my-4">

            <div class="row justify-content-center">
                <div class="col-lg-8 col-md-10 col-12">
                    <div class="card shadow">
                        


                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/css/tom-select.bootstrap4.min.css" rel="stylesheet">

@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/js/tom-select.complete.min.js"></script>

@endpush
