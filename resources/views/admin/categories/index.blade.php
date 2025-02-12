@extends('layouts.back')

@section('subtitle', __('Categories list'))

@push('css')
    @livewireStyles()
@endpush

@section('content')
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <x-livewire-alert::scripts />
    @livewire('admin.categories-manage')
@endsection


@push('js')
    <script type="text/javascript">
        // Add/Update category modal
        window.livewire.on('closeModal', () => {
            $('#AddCategory').modal('hide');
            $('#deleteCategory').modal('hide');
            $('#EditCategory').modal('hide');
        });
        //Edit Category Modal
        window.livewire.on('openEditModal', () => {
            $('#EditCategory').modal('show');
        });
         //add Category Modal
         window.livewire.on('openModal', () => {
            $('#AddCategory').modal('show');
        });
        // Delete category modal
        window.livewire.on('openDeleteModal', () => {
            $('#deleteCategory').modal('show');
        });
    </script>

    @livewireScripts()
@endpush
