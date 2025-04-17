@extends('layouts.back')

@section('subtitle', __('Essences'))

@section('content')
    <div class="section-body mt-4">
        <div class="container">
            <!-- Header with Title and Total Count -->
            <div class="d-flex justify-content-between align-items-center mb-4 header-gradient rounded-lg shadow-lg p-3">
                <h2 class="section-title m-0 text-white font-weight-bold">
                    <i class="fas fa-tree mr-2 text-success"></i>Gestion des Essences
                </h2>
                <span class="badge badge-success px-3 py-2 shadow-sm">
                    Total: {{ \App\Models\Essence::count() }} essences
                </span>
            </div>

            <div class="row">
                <!-- Liste des essences -->
                <div class="col-12">
                    @livewire('admin.essence-list')
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script>
    $(document).ready(function() {
        $('.custom-file-input').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').html(fileName || 'Choisir un fichier');
        });
    });
</script>
@endpush
