<h4 class="mb-4">@lang('Academic information')</h4>
<p class="mr-2">@lang('University of ') : <b>{{ $user->userable->talentable->university }}</b></p>

<p class="mr-2">@lang('Training school') : <b>{{ $user->userable->talentable->training_school }}</b></p>
{{-- <span class="mr-2">@lang('Field ') :</span>
 @if ($user->userable->talentable->field)
   <b>  {{ $user->userable->talentable->field }}</b>
 @else
     @lang('Any')
 @endif <span class="mr-2 ml-4">@lang('Level') :</span>
 @if ($user->userable->talentable->level)
    <b> {{ $user->userable->talentable->level }}</b>
 @else
     @lang('Any')
 @endif
 <br><br> --}}
