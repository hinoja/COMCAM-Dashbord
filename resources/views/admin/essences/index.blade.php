@extends('layouts.back')

@section('subtitle', __('Liste des Essences'))

@section('content')
    <div class="section-body mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="section-title text-primary">Liste des Essences</h2>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.essence.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Ajouter une Essence
                </a>
                <a href="{{ route('admin.essence.export') }}" class="btn btn-success">
                    <i class="fas fa-file-excel me-2"></i>Exporter Excel
                </a>
            </div>
        </div>

        // ... existing code ...
    </div>
@endsection
