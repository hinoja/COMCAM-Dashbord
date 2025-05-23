@php
    $currentUri = Route::current()->uri;
@endphp

<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <a href="#">
               <span> <img alt="image" src="{{ asset('logo.jpg') }}" class="rounded-circle mr-1" width="60">
                {{ config('app.name', 'COMCAM') }}</span>
            </a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <a href="#">E&F CC</a>
        </div>
        <ul class="sidebar-menu"> 
            <li class="@if ($currentUri === 'dashboard') active @endif">
                <a class="nav-link" href="{{ route('admin.dashboard') }}"><i class="fas fa-tachometer-alt"></i>
                    <span>@lang('Tableau de bord')</span></a>
            </li>

            @auth
                {{-- @if (Auth::user()->role_id < 2) --}}
                    <li class="@if (Str::contains($currentUri, 'users')) active @endif">
                        <a class="nav-link" href="{{ route('admin.users.index') }}"><i class="fas fa-users"></i>
                            <span>@lang('Users')</span></a>
                    </li>
                {{-- @endif --}}
            @endauth

            <li class="@if (Str::contains($currentUri, 'transaction')) active @endif">
                <a class="nav-link" href="{{ route('admin.transaction.index') }}"><i class="fas fa-th"></i>
                    <span>@lang('Transactions')</span></a>
            </li>

            <li class="@if (Str::contains($currentUri, 'titre')) active @endif">
                <a class="nav-link" href="{{ route('admin.titre.index') }}"><i class="fas fa-layer-group"></i>
                    <span>@lang('Titres')</span></a>
            </li>
            <li class="@if (Str::contains($currentUri, 'essence')) active @endif">
                <a class="nav-link" href="{{ route('admin.essence.index') }}"><i class="fas fa-tree"></i>
                    <span>@lang('Essences')</span></a>
                    </li>




            <li class="@if (Str::contains($currentUri, 'societe')) active @endif">
                <a class="nav-link" href="{{ route('admin.societe.index') }}"><i class="fas fa-tags"></i>
                    <span>@lang('Societes')</span></a>
            </li>
            <li class="@if (Str::contains($currentUri, 'profile')) active @endif">
                <a class="nav-link" href="{{ route('profile.edit') }}"><i class="fas fa-user"></i>
                    <span>@lang('Profile')</span></a>
            </li>


        </ul>
    </aside>
</div>
