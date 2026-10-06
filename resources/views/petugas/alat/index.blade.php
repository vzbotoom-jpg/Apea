<!-- resources/views/petugas/alat/index.blade.php -->
@extends('layouts.petugas')

@section('title', 'Daftar Alat')
@section('page-title', 'Daftar Alat')

@section('content')
<div class="space-y-6">
    
    {{-- Filter Bar --}}
    <div class="bg-white border border-slate-200 rounded-xl p-4 md:p-5">
        <form method="GET" class="flex flex-col lg:flex-row gap-4">
            <div class="flex-1 relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama alat atau kode..." 
                       class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition">
                <svg class="absolute left-3 top-3 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            
            <div class="flex flex-wrap gap-3">
                <select name="kategori" class="px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 outline-none text-slate-700">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoris as $kategori)
                        <option value="{{ $kategori->id }}" {{ request('kategori') == $kategori->id ? 'selected' : '' }}>{{ $kategori->nama_kategori }}</option>
                    @endforeach
                </select>
                
                <select name="status" class="px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 outline-none text-slate-700">
                    <option value="">Semua Status</option>
                    <option value="tersedia" {{ request('status') == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                    <option value="dipinjam" {{ request('status') == 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                    <option value="perbaikan" {{ request('status') == 'perbaikan' ? 'selected' : '' }}>Perbaikan</option>
                    <option value="tidak_tersedia" {{ request('status') == 'tidak_tersedia' ? 'selected' : '' }}>Tidak Tersedia</option>
                </select>

                <select name="kondisi" class="px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 outline-none text-slate-700">
                    <option value="">Semua Kondisi</option>
                    <option value="baik" {{ request('kondisi') == 'baik' ? 'selected' : '' }}>Baik</option>
                    <option value="rusak_ringan" {{ request('kondisi') == 'rusak_ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                    <option value="rusak_berat" {{ request('kondisi') == 'rusak_berat' ? 'selected' : '' }}>Rusak Berat</option>
                </select>

                <button type="submit" class="px-5 py-2.5 bg-slate-900 text-white text-sm font-semibold rounded-lg hover:bg-slate-800 transition">
                    Filter
                </button>
                
                @if(request()->hasAny(['search', 'kategori', 'status', 'kondisi']))
                    <a href="{{ route('petugas.alat.index') }}" class="px-4 py-2.5 text-slate-500 hover:text-slate-700 text-sm font-medium transition flex items-center">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Results Grid --}}
    @if($alats->isEmpty())
        <div class="bg-white border border-slate-200 rounded-xl p-12 text-center">
            <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-400">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h3 class="text-lg font-bold text-slate-900">Tidak ada alat ditemukan</h3>
            <p class="text-slate-500 mt-1">Coba ubah kata kunci pencarian atau filter Anda.</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($alats as $alat)
                <div class="group bg-white border border-slate-200 rounded-xl overflow-hidden hover:shadow-lg hover:border-teal-300 transition duration-300 flex flex-col">
                    <div class="relative h-48 bg-slate-100 overflow-hidden">
                        @if($alat->gambar)
                            <img src="{{ asset('storage/' . $alat->gambar) }}" alt="{{ $alat->nama_alat }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-slate-300">
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                        @endif
                        <div class="absolute top-3 right-3">
                            <span class="px-2.5 py-1 rounded text-[10px] font-bold uppercase tracking-wider shadow-sm backdrop-blur-md
                                {{ $alat->status === 'tersedia' ? 'bg-emerald-500/90 text-white' : 
                                   ($alat->status === 'dipinjam' ? 'bg-teal-500/90 text-white' : 
                                   ($alat->status === 'rusak_berat' || $alat->status === 'tidak_tersedia' ? 'bg-red-500/90 text-white' : 'bg-amber-500/90 text-white')) }}">
                                {{ ucfirst(str_replace('_', ' ', $alat->status)) }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="p-5 flex-1 flex flex-col">
                        <div class="flex justify-between items-start mb-2">
                            <span class="text-[10px] font-bold text-teal-600 uppercase tracking-wider">{{ $alat->kategori->nama_kategori ?? 'Umum' }}</span>
                            <span class="text-xs text-slate-400 font-mono">{{ $alat->kode_alat }}</span>
                        </div>
                        
                        <h3 class="font-bold text-slate-900 text-lg leading-tight mb-3 group-hover:text-teal-700 transition line-clamp-2">{{ $alat->nama_alat }}</h3>
                        
                        <div class="mt-auto pt-4 border-t border-slate-100 flex items-center justify-between">
                            <div class="flex flex-col">
                                <span class="text-xs text-slate-500">Stok: <span class="font-bold text-slate-900">{{ $alat->stok_tersedia }}</span></span>
                                <span class="text-xs px-2 py-0.5 rounded-full inline-block w-fit mt-1
                                    {{ $alat->kondisi === 'baik' ? 'bg-emerald-50 text-emerald-700' : 
                                       ($alat->kondisi === 'rusak_ringan' ? 'bg-amber-50 text-amber-700' : 'bg-red-50 text-red-700') }}">
                                    {{ ucfirst(str_replace('_', ' ', $alat->kondisi)) }}
                                </span>
                            </div>
                            <div class="text-right">
                                <span class="block text-sm font-bold text-slate-900">Rp {{ number_format($alat->harga_sewa_per_hari, 0, ',', '.') }}</span>
                                <a href="{{ route('petugas.alat.show', $alat) }}" class="text-xs font-bold text-teal-600 hover:text-teal-700">Detail &rarr;</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $alats->links() }}
        </div>
    @endif
</div>
@endsection