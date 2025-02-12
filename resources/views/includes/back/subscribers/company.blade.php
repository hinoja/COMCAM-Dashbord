<div class="profile-widget-description">
    <h4 class="mb-4">@lang('Other Information')</h4>
    <p class="mr-2">@lang('Research Area') : <b>{{ $user->userable->category->name }}</b></p>
    <p class="mr-2">@lang('Localisation') : <b>{{ $user->userable->location ?? __('Nothing') }}</b></p>
    <p class="mr-2">@lang('Website') : <a href="{{ $user->userable->url ?? __('Nothing') }}" target="_blank"> {{ $user->userable->url }}</a></p>
    <p>@lang('Description') : <b>{{ $user->userable->description }}</b></p>
</div>
