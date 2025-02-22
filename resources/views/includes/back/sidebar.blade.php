@php
    $currentUri = Route::current()->uri;
@endphp

<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <a href="#">
                <img alt="image" src="{{ asset('logo.jpg') }}" class="rounded-circle mr-1" width="60">
                {{ config('app.name', 'COMCAM') }}
            </a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <a href="#">Mb</a>
        </div>
        <ul class="sidebar-menu">
            <li class="@if (Str::contains($currentUri, 'dashboard')) active @endif">
                <a class="nav-link" href="#"><i class="fas fa-home"></i>
                    <span>@lang('Dashboard')</span></a>
            </li>
            <li class="@if (Str::contains($currentUri, 'users')) active @endif">
                <a class="nav-link" href="#"><i class="fas fa-users"></i>
                    <span>@lang('Users')</span></a>
            </li>

            <li class="@if (Str::contains($currentUri, 'transaction')) active @endif">
                <a class="nav-link" href="{{ route('admin.transaction.create') }}"><i class="fas fa-th"></i>
                    <span>@lang('Transactions')</span></a>
            </li>

            <li class="@if (Str::contains($currentUri, 'titre')) active @endif">
                <a class="nav-link" href="{{ route('admin.titre.index') }}"><i class="fas fa-layer-group"></i>
                    <span>@lang('Titres')</span></a>
            </li>
            <li class="@if (Str::contains($currentUri, 'societe')) active @endif">
                <a class="nav-link" href="{{ route('admin.societe.index') }}"><i class="fas fa-tags"></i>
                    <span>@lang('Societes')</span></a>
            </li>


        </ul>
    </aside>
</div>
