@extends('layouts.back')

@section('subtitle', __('Tags list'))

@push('css')
    @livewireStyles()
@endpush

@section('content')
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <x-livewire-alert::scripts />
    @livewire('admin.tags-manage')
@endsection


@push('js')
    <script type="text/javascript">
        // Add/Update category modal
        window.livewire.on('closeModal', () => {
            $('#AddTag').modal('hide');
            $('#deleteTag').modal('hide');
        });
        //Edit Tag Modal
        window.livewire.on('openEditModal', () => {
            $('#AddTag').modal('show');
        });
        //add Category Modal
        window.livewire.on('openModal', () => {
            $('#AddTag').modal('show');
        });
        // Delete category modal
        window.livewire.on('openDeleteModal', () => {
            $('#deleteTag').modal('show');
        });
    </script>
    @livewireScripts()
@endpush
