<!-- resources/views/user/alat/index.blade.php -->
@extends('layouts.user')

@section('title', 'Katalog Alat')
@section('page-title', 'Katalog Alat')

@section('content')
<div class="space-y-6">
    
    {{-- Filter & Search Bar --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm md:p-5">
        <form method="GET" action="{{ route('user.alat.index') }}" class="flex min-w-0 flex-col gap-3 lg:flex-row">
            <div class="relative min-w-0 flex-1">
                <input type="search" name="search" value="{{ request('search') }}" placeholder="Cari nama alat atau kode..."
                       class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 pl-11 pr-4 text-sm outline-none transition placeholder:text-slate-400 focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20">
                <svg class="absolute left-4 top-3.5 h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            
            <div class="grid min-w-0 grid-cols-1 gap-3 sm:grid-cols-2 lg:flex lg:shrink-0">
                <select name="kategori" class="w-full min-w-0 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 lg:w-auto">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoris as $kategori)
                        <option value="{{ $kategori->id }}" {{ request('kategori') == $kategori->id ? 'selected' : '' }}>{{ $kategori->nama_kategori }}</option>
                    @endforeach
                </select>
                
                <select name="status" class="w-full min-w-0 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 lg:w-auto">
                    <option value="">Semua Status</option>
                    <option value="tersedia" {{ request('status') == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                    <option value="dipinjam" {{ request('status') == 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                </select>

                <button type="submit" class="rounded-xl bg-teal-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2">
                    Filter
                </button>
                
                @if(request()->hasAny(['search', 'kategori', 'status']))
                    <a href="{{ route('user.alat.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-600 transition hover:border-slate-300 hover:bg-slate-50 hover:text-slate-900">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Results Info --}}
    <div class="flex items-center justify-between">
        <p class="text-sm text-slate-500">Menampilkan <span class="font-semibold text-slate-900">{{ $alats->total() }}</span> alat</p>
    </div>

    {{-- Grid Alat --}}
    @if($alats->isEmpty())
        <div class="bg-white border border-slate-200 rounded-xl p-12 text-center">
            <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-400">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h3 class="text-lg font-bold text-slate-900">Tidak ada alat ditemukan</h3>
            <p class="text-slate-500 mt-1">Coba ubah kata kunci pencarian atau filter Anda.</p>
        </div>
    @else
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-4">
            @foreach($alats as $alat)
                <x-catalog.alat-card :alat="$alat" :detail-url="route('user.alat.show', $alat)" />
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-8">
            {{ $alats->links() }}
        </div>
    @endif
</div>
@endsection