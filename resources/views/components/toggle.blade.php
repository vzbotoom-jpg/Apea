@props(['bound', 'disabled' => false])

<button type="button"
        role="switch"
        :aria-checked="String({{ $bound }})"
        @unless($disabled) @click="{{ $bound }} = !{{ $bound }}" @endunless
        :class="{{ $bound }} ? 'bg-teal-600' : 'bg-slate-300'"
        class="relative inline-flex h-6 w-11 shrink-0 items-center rounded-full transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500 {{ $disabled ? 'opacity-60 cursor-not-allowed' : '' }}">
    <span :class="{{ $bound }} ? 'translate-x-6' : 'translate-x-1'"
          class="inline-block h-4 w-4 rounded-full bg-white shadow transform transition-transform"></span>
</button>