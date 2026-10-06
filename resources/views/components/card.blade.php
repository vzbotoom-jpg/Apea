@props(['title' => null, 'subtitle' => null, 'headerClass' => '', 'bodyClass' => '', 'footer' => null])

<div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
    @if($title || $subtitle)
        <div class="px-6 py-4 border-b border-slate-100 {{ $headerClass }}">
            @if($title)
                <h3 class="text-base font-bold text-slate-900">{{ $title }}</h3>
            @endif
            @if($subtitle)
                <p class="text-sm text-slate-500 mt-0.5">{{ $subtitle }}</p>
            @endif
        </div>
    @endif

    <div class="p-6 {{ $bodyClass }}">
        {{ $slot }}
    </div>

    @if($footer)
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
            {{ $footer }}
        </div>
    @endif
</div>