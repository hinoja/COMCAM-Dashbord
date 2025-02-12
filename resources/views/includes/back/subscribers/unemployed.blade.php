<h4 class="mb-4">@lang('Other Information')</h4>

<p>@lang('Diploma') : <b>{{ $user->userable->talentable->diploma }}</b></p>
<p>@lang('Current Job') : <b>{{ $user->userable->talentable->current_job ?? __('Nothing') }}</b></p>
<p>@lang('Aptitudes') : <b>{{ $user->userable->talentable->aptitudes ?? __('Nothing') }}</b></p>
<p>@lang('Qualifications') : <b>{{ $user->userable->talentable->qualifications ?? __('Nothing') }}</b></p>
