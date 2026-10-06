<!-- resources/views/admin/logs/index.blade.php -->
@extends('layouts.admin')

@section('title', 'Log Aktivitas')
@section('page-title', 'Log Aktivitas')

@section('content')
<div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
    
    {{-- Header --}}
    <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-slate-50/50">
        <div>
            <h2 class="text-lg font-bold text-slate-900">Log Aktivitas</h2>
            <p class="text-sm text-slate-500 mt-0.5">Total: {{ $logs->total() }} aktivitas</p>
        </div>
        <form action="{{ route('admin.logs.clear') }}" method="POST" class="inline-block">
            @csrf
            <button type="submit" onclick="return confirm('Hapus semua log aktivitas? Tindakan ini tidak dapat dibatalkan.')" 
                    class="flex items-center gap-2 px-4 py-2 bg-red-50 text-red-600 text-sm font-semibold rounded-lg hover:bg-red-100 transition border border-red-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Bersihkan Log
            </button>
        </form>
    </div>

    {{-- Filter --}}
    <div class="p-5 border-b border-slate-100">
        <form method="GET" class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1 relative">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Cari aktivitas atau user..." 
                       class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition">
                <svg class="absolute left-3 top-3 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <div class="flex gap-3">
                <input type="date" name="date" value="{{ request('date') }}" 
                       class="px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition">
                <button type="submit" class="px-5 py-2.5 bg-slate-900 text-white text-sm font-semibold rounded-lg hover:bg-slate-800 transition">Filter</button>
                @if(request()->hasAny(['search', 'date']))
                    <a href="{{ route('admin.logs.index') }}" class="px-4 py-2.5 text-slate-500 hover:text-slate-700 text-sm font-medium transition flex items-center">Reset</a>
                @endif
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
        @if($logs->isEmpty())
            <div class="p-12 text-center text-slate-500">
                <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <p>Tidak ada log aktivitas</p>
            </div>
        @else
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                        <th class="px-6 py-4">Waktu</th>
                        <th class="px-6 py-4">User</th>
                        <th class="px-6 py-4">Aktivitas</th>
                        <th class="px-6 py-4">Deskripsi</th>
                        <th class="px-6 py-4">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @foreach($logs as $log)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-6 py-4 text-slate-500 whitespace-nowrap font-mono text-xs">
                                {{ $log->created_at->format('d/m/Y H:i:s') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center text-xs font-bold shrink-0">
                                        {{ $log->user ? strtoupper(substr($log->user->name, 0, 1)) : '?' }}
                                    </div>
                                    <span class="font-medium text-slate-900">{{ $log->user->name ?? 'System' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold
                                    {{ str_contains($log->activity, 'menambah') ? 'bg-emerald-100 text-emerald-700' : 
                                       (str_contains($log->activity, 'memperbarui') ? 'bg-blue-100 text-blue-700' : 
                                       (str_contains($log->activity, 'menghapus') ? 'bg-red-100 text-red-700' : 
                                       (str_contains($log->activity, 'login') ? 'bg-purple-100 text-purple-700' : 
                                       'bg-slate-100 text-slate-600'))) }}">
                                    {{ $log->activity }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-600 max-w-xs truncate" title="{{ $log->description }}">
                                {{ $log->description ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-slate-400 font-mono text-xs">{{ $log->ip_address ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
    
    @if($logs->hasPages())
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
            {{ $logs->links() }}
        </div>
    @endif
</div>
@endsection