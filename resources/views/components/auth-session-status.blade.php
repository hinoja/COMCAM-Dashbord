@props(['status'])

@if ($status)
     
    <div {{ $attributes->merge(['class' => ' alert-dismissible font-medium text-sm text-green-600 fade show mt-1 shadow-sm rounded-lg']) }}>
        {{ $status }}
    </div>
@endif
