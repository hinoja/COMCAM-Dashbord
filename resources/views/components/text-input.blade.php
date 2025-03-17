@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'tabindex="1" required autofocus form-control ']) }}>
