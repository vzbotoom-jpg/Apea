@extends('layouts.user')
@section('title', 'Notifikasi')
@section('page-title', 'Notifikasi')
@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Notifikasi</h1>
            <p class="text-sm text-slate-500 mt-0.5">
                @if($unread > 0) {{ $unread }} notifikasi belum dibaca @else Semua notifikasi sudah dibaca @endif
            </p>
        </div>
        @if($unread > 0)
            <form method="POST" action="{{ route('user.notifikasi.readAll') }}">
                @csrf
                <button class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-lg transition">✓ Tandai Semua Dibaca</button>
            </form>
        @endif
    </div>

    {{-- List --}}
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        @forelse($notifications as $n)
            <a href="{{ route('user.notifikasi.show', $n) }}"
               class="flex gap-3 px-5 py-4 border-b border-slate-100 last:border-0 hover:bg-slate-50 transition {{ !$n->is_read ? 'bg-teal-50/40' : '' }}">
                <span class="w-9 h-9 rounded-full flex items-center justify-center shrink-0
                    {{ match($n->type) {
                        'success' => 'bg-emerald-100 text-emerald-600',
                        'warning' => 'bg-amber-100 text-amber-600',
                        'danger'  => 'bg-red-100 text-red-600',
                        'payment' => 'bg-teal-100 text-teal-600',
                        default   => 'bg-slate-100 text-slate-500',
                    } }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        @if($n->type === 'success')<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        @elseif($n->type === 'warning')<path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        @elseif($n->type === 'danger')<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M12 3l9 18H3l9-18z"/>
                        @elseif($n->type === 'payment')<path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        @else<path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        @endif
                    </svg>
                </span>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-sm font-bold {{ !$n->is_read ? 'text-slate-900' : 'text-slate-600' }}">{{ $n->title }}</p>
                        <span class="text-[10px] text-slate-400 shrink-0">{{ $n->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">{{ $n->message }}</p>
                </div>
                @if(!$n->is_read)<span class="w-2 h-2 rounded-full bg-teal-500 mt-1.5 shrink-0"></span>@endif
            </a>
        @empty
            <div class="p-12 text-center">
                <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-400">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900">Tidak ada notifikasi</h3>
                <p class="text-slate-500 text-sm mt-1">Kami akan memberi tahu Anda saat ada pembaruan peminjaman.</p>
            </div>
        @endforelse
    </div>

    {{ $notifications->links() }}
</div>
@endsection