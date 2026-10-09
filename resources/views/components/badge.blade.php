@props([
    'color' => '#3b82f6',
    'dot' => true,
])

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg border text-xs font-semibold shadow-sm select-none']) }}
      style="background-color: {{ $color }}15; border-color: {{ $color }}44; color: {{ $color }};">
    @if($dot)
        <span class="w-2 h-2 rounded-full shrink-0 shadow-sm" style="background-color: {{ $color }};"></span>
    @endif
    {{ $slot }}
</span>
