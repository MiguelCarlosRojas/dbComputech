@props([
    'title' => null,
    'icon' => null,
    'badge' => null,
    'glow' => false,
])

<div {{ $attributes->merge(['class' => 'bg-slate-900/85 border border-slate-800 rounded-2xl overflow-hidden shadow-xl flex flex-col ' . ($glow ? 'shadow-cyan-500/5 border-slate-700/60' : '')]) }}>
    @if($title || isset($header))
        <div class="px-5 py-3.5 bg-slate-900/95 border-b border-slate-800/80 flex items-center justify-between">
            <div class="flex items-center gap-2">
                @if($icon)
                    <span class="text-base shrink-0">{!! $icon !!}</span>
                @endif
                <h3 class="text-sm font-bold text-white tracking-wide">{{ $title }}</h3>
                @if($badge)
                    <span class="text-xs font-mono-code px-2 py-0.5 rounded bg-slate-800 text-slate-400 border border-slate-700">{!! $badge !!}</span>
                @endif
            </div>
            @if(isset($headerActions))
                <div class="flex items-center gap-2">
                    {{ $headerActions }}
                </div>
            @endif
        </div>
    @endif

    <div class="p-4 sm:p-5 flex-1 flex flex-col">
        {{ $slot }}
    </div>

    @if(isset($footer))
        <div class="px-5 py-2.5 bg-slate-950/90 border-t border-slate-800/80 text-xs text-slate-400 flex items-center justify-between">
            {{ $footer }}
        </div>
    @endif
</div>
