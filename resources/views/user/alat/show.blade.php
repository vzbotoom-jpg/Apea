<!-- resources/views/user/alat/show.blade.php -->
@extends('layouts.user')

@section('title', $alat->nama_alat)
@section('page-title', 'Detail Alat')

@section('content')
<div class="max-w-5xl mx-auto">
    
    {{-- Breadcrumb --}}
    <nav class="flex text-sm text-slate-500 mb-6">
        <a href="{{ route('user.alat.index') }}" class="hover:text-teal-600">Katalog</a>
        <span class="mx-2">/</span>
        <span class="text-slate-900 font-medium">{{ $alat->nama_alat }}</span>
    </nav>

    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
        <div class="grid grid-cols-1 lg:grid-cols-2">
            
            {{-- Image Section --}}
            <div class="bg-slate-50 border-b lg:border-b-0 lg:border-r border-slate-200 p-8 flex items-center justify-center min-h-[300px] lg:min-h-[500px]">
                @if($alat->gambar)
                    <img src="{{ asset('storage/' . $alat->gambar) }}" alt="{{ $alat->nama_alat }}" class="max-w-full max-h-full object-contain rounded-lg shadow-sm">
                @else
                    <div class="text-slate-300">
                        <svg class="w-32 h-32" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                @endif
            </div>

            {{-- Info Section --}}
            <div class="p-8 lg:p-10 flex flex-col">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <span class="text-xs font-bold text-teal-600 uppercase tracking-wider">{{ $alat->kategori->nama_kategori ?? 'Umum' }}</span>
                        <h1 class="text-3xl font-extrabold text-slate-900 mt-1 leading-tight">{{ $alat->nama_alat }}</h1>
                        <p class="text-sm text-slate-500 font-mono mt-1">{{ $alat->kode_alat }}</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide
                        {{ $alat->stok_tersedia > 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                        {{ $alat->stok_tersedia > 0 ? 'Tersedia' : 'Habis' }}
                    </span>
                </div>

                {{-- Key Stats Grid --}}
                <div class="grid grid-cols-2 gap-4 my-8">
                    <div class="p-4 bg-slate-50 rounded-lg border border-slate-100">
                        <p class="text-xs text-slate-500 font-medium uppercase">Kondisi</p>
                        <p class="text-sm font-bold text-slate-900 mt-1 capitalize">{{ str_replace('_', ' ', $alat->kondisi) }}</p>
                    </div>
                    <div class="p-4 bg-slate-50 rounded-lg border border-slate-100">
                        <p class="text-xs text-slate-500 font-medium uppercase">Stok</p>
                        <p class="text-sm font-bold {{ $alat->stok_tersedia > 0 ? 'text-emerald-600' : 'text-red-600' }} mt-1">
                            {{ $alat->stok_tersedia }} / {{ $alat->stok }} Unit
                        </p>
                    </div>
                    <div class="p-4 bg-slate-50 rounded-lg border border-slate-100 col-span-2">
                        <p class="text-xs text-slate-500 font-medium uppercase">Biaya Sewa</p>
                        <div class="flex items-baseline gap-1 mt-1">
                            @if($alat->harga_sewa_per_hari > 0)
                                <span class="text-2xl font-extrabold text-slate-900">Rp {{ number_format($alat->harga_sewa_per_hari, 0, ',', '.') }}</span>
                                <span class="text-sm text-slate-500">/ hari</span>
                            @else
                                <span class="text-2xl font-extrabold text-emerald-600">Gratis</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Description --}}
                @if($alat->deskripsi)
                    <div class="mb-8">
                        <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-2">Deskripsi</h3>
                        <p class="text-slate-600 text-sm leading-relaxed whitespace-pre-line">{{ $alat->deskripsi }}</p>
                    </div>
                @endif

                {{-- Action Buttons --}}
                <div class="mt-auto pt-6 border-t border-slate-100 flex gap-3">
                    @if($alat->stok_tersedia > 0)
                        <a href="{{ route('user.peminjaman.create', ['alat_id' => $alat->id]) }}" class="flex-1 flex justify-center items-center px-6 py-3 bg-teal-600 text-white font-bold rounded-lg hover:bg-teal-700 transition shadow-sm">
                            Ajukan Peminjaman
                        </a>
                    @else
                        <button disabled class="flex-1 px-6 py-3 bg-slate-100 text-slate-400 font-bold rounded-lg cursor-not-allowed">
                            Stok Habis
                        </button>
                    @endif
                    <a href="{{ route('user.alat.index') }}" class="px-6 py-3 bg-white border border-slate-300 text-slate-700 font-bold rounded-lg hover:bg-slate-50 transition">
                        Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Related Items --}}
    @if(isset($relatedAlat) && $relatedAlat->isNotEmpty())
        <div class="mt-12">
            <h3 class="text-lg font-bold text-slate-900 mb-6">Alat Lainnya dalam Kategori Ini</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($relatedAlat as $related)
                    <a href="{{ route('user.alat.show', $related) }}" class="group block bg-white border border-slate-200 rounded-lg p-4 hover:border-teal-300 hover:shadow-md transition">
                        <div class="aspect-square bg-slate-100 rounded mb-3 overflow-hidden">
                             @if($related->gambar)
                                <img src="{{ asset('storage/' . $related->gambar) }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-slate-300">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                            @endif
                        </div>
                        <h4 class="text-sm font-bold text-slate-900 truncate group-hover:text-teal-600">{{ $related->nama_alat }}</h4>
                        <p class="text-xs text-slate-500 mt-1">
                            @if($related->harga_sewa_per_hari > 0)
                                Rp {{ number_format($related->harga_sewa_per_hari, 0, ',', '.') }}
                            @else
                                <span class="text-emerald-600">Gratis</span>
                            @endif
                        </p>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

</div>
@endsection