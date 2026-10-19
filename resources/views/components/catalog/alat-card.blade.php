@props(['alat', 'detailUrl' => null])

<article class="group flex h-full flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition duration-300 hover:-translate-y-0.5 hover:border-teal-300 hover:shadow-lg">
    <div class="relative aspect-[4/3] overflow-hidden bg-white">
        @if($alat->gambar)
            <img
                src="{{ asset('storage/' . $alat->gambar) }}"
                alt="{{ $alat->nama_alat }}"
                loading="lazy"
                class="h-full w-full object-contain p-4 transition duration-500 group-hover:scale-105"
            >
        @else
            <div class="flex h-full w-full items-center justify-center bg-slate-50 text-slate-300">
                <svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
        @endif

        <span class="absolute right-3 top-3 rounded-md px-3 py-1.5 text-[11px] font-bold uppercase tracking-wide shadow-sm
            {{ $alat->stok_tersedia > 0 ? 'bg-emerald-500 text-white' : 'bg-slate-700 text-white' }}">
            {{ $alat->stok_tersedia > 0 ? 'Tersedia' : 'Stok Habis' }}
        </span>
    </div>

    <div class="flex flex-1 flex-col p-4 sm:p-5">
        <div class="mb-2 flex items-center justify-between gap-3">
            <span class="truncate text-[11px] font-bold uppercase tracking-wide text-teal-600">{{ $alat->kategori->nama_kategori ?? 'Umum' }}</span>
            <span class="shrink-0 font-mono text-xs text-slate-400">{{ $alat->kode_alat }}</span>
        </div>

        <h2 class="min-h-[3.5rem] text-lg font-bold leading-snug text-slate-900 transition group-hover:text-teal-700 line-clamp-2">
            {{ $alat->nama_alat }}
        </h2>

        <div class="mt-3 border-t border-slate-100 pt-3">
            <div class="flex items-center justify-between gap-2">
                <span class="text-sm text-slate-500">
                    Stok:
                    <strong class="font-semibold text-slate-800">{{ $alat->stok_tersedia }}</strong>
                </span>
                @if($alat->harga_sewa_per_hari > 0)
                    <span class="text-right text-sm font-extrabold text-slate-900">
                        Rp {{ number_format($alat->harga_sewa_per_hari, 0, ',', '.') }}
                        <span class="font-normal text-slate-400">/hari</span>
                    </span>
                @else
                    <span class="text-sm font-extrabold text-emerald-600">Gratis</span>
                @endif
            </div>

            <div class="mt-2 flex min-h-7 items-center justify-between gap-2">
                <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700">
                    {{ ucfirst(str_replace('_', ' ', $alat->kondisi)) }}
                </span>
                @if($detailUrl)
                    <a href="{{ $detailUrl }}" class="inline-flex items-center gap-1 text-sm font-bold text-teal-600 transition hover:text-teal-800">
                        Detail
                        <span aria-hidden="true">&rarr;</span>
                    </a>
                @endif
            </div>
        </div>
    </div>
</article>
