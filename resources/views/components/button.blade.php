@props([
    'variant' => 'secondary',
    'size' => 'md',
    'type' => 'button',
    'icon' => null,
    'iconOnly' => false,
])

@php
    $hasSlot = !empty(trim((string) $slot));
    $isIconOnly = $iconOnly || ($icon && !$hasSlot);

    $baseClasses = 'group relative inline-flex items-center justify-center font-semibold tracking-wide rounded-xl transition-all duration-200 ease-out active:scale-[0.97] hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-slate-950 disabled:opacity-50 disabled:pointer-events-none select-none cursor-pointer overflow-hidden shadow-sm';

    if ($isIconOnly) {
        $sizeClasses = [
            'xs' => 'w-7 h-7 p-0',
            'sm' => 'w-8 h-8 p-0',
            'md' => 'w-9 h-9 p-0',
            'lg' => 'w-10 h-10 p-0',
        ][$size] ?? 'w-8 h-8 p-0';
    } else {
        $sizeClasses = [
            'xs' => 'px-3 py-1.5 text-xs gap-1.5',
            'sm' => 'px-3.5 py-2 text-xs sm:text-xs gap-2',
            'md' => 'px-5 py-2.5 text-xs sm:text-sm gap-2',
            'lg' => 'px-6 py-3 text-sm sm:text-base font-bold gap-2.5',
        ][$size] ?? 'px-4 py-2 text-xs sm:text-sm gap-2';
    }

    $variantClasses = [
        'primary' => 'bg-gradient-to-r from-cyan-400 via-teal-400 to-emerald-400 hover:from-cyan-300 hover:to-emerald-300 text-slate-950 font-bold shadow-lg shadow-cyan-500/25 hover:shadow-cyan-500/40 border border-cyan-300/60 focus:ring-cyan-400',
        'secondary' => 'bg-slate-800/80 hover:bg-slate-700/90 text-slate-200 hover:text-white border border-slate-700/80 hover:border-cyan-500/40 shadow-md shadow-slate-950/50 hover:shadow-cyan-500/10 backdrop-blur-md focus:ring-slate-500',
        'cyan' => 'bg-gradient-to-r from-cyan-600 via-sky-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 text-white font-bold shadow-lg shadow-cyan-600/30 hover:shadow-cyan-500/50 border border-cyan-400/50 hover:border-cyan-300/80 focus:ring-cyan-400',
        'emerald' => 'bg-gradient-to-r from-emerald-600 via-teal-600 to-green-600 hover:from-emerald-500 hover:to-green-500 text-white font-bold shadow-lg shadow-emerald-600/30 hover:shadow-emerald-500/50 border border-emerald-400/50 hover:border-emerald-300/80 focus:ring-emerald-400',
        'danger' => 'bg-gradient-to-r from-rose-600 via-red-600 to-rose-700 hover:from-rose-500 hover:to-red-500 text-white font-bold shadow-lg shadow-rose-600/30 hover:shadow-rose-500/50 border border-rose-400/50 hover:border-rose-300/80 focus:ring-rose-500',
        'danger-subtle' => 'bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 hover:text-rose-200 border border-rose-500/30 hover:border-rose-400/60 shadow-sm shadow-rose-950/30 focus:ring-rose-500 backdrop-blur-sm',
        'warning' => 'bg-gradient-to-r from-amber-400 via-amber-500 to-yellow-500 hover:from-amber-300 hover:to-yellow-400 text-slate-950 font-bold shadow-lg shadow-amber-500/30 hover:shadow-amber-500/50 border border-amber-300/60 focus:ring-amber-500',
        'ghost' => 'hover:bg-slate-800/80 text-slate-400 hover:text-cyan-300 border border-transparent hover:border-slate-700/60 focus:ring-slate-700',
    ][$variant] ?? 'bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700';

    $classes = "{$baseClasses} {$sizeClasses} {$variantClasses}";
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
    <!-- Subtle glossy reflection overlay -->
    <span class="absolute inset-0 bg-gradient-to-b from-white/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none"></span>

    @if($icon)
        <span class="relative inline-flex items-center justify-center shrink-0 w-4 h-4 [&>svg]:w-4 [&>svg]:h-4 [&>svg]:stroke-[2] transition-transform duration-200 group-hover:scale-110 leading-none">{!! $icon !!}</span>
    @endif
    @if($hasSlot)
        <span class="relative inline-flex items-center justify-center gap-1.5 leading-none">{{ $slot }}</span>
    @endif
</button>
