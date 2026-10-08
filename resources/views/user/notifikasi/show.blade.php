@extends('layouts.user')
@section('title', 'Detail Notifikasi')
@section('page-title', 'Detail Notifikasi')
@section('content')
<div class="max-w-xl mx-auto">
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Notifikasi</span>
            <span class="text-[10px] text-slate-400">{{ $notification->created_at->locale('id')->translatedFormat('d M Y, H:i') }} WIB</span>
        </div>
        <div class="p-6 space-y-5">
            <div class="flex items-start gap-3">
                <span class="w-10 h-10 rounded-full bg-teal-100 text-teal-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                </span>
                <div>
                    <h2 class="text-lg font-bold text-slate-900">{{ $notification->title }}</h2>
                    <p class="text-sm text-slate-600 mt-2 leading-relaxed">{{ $notification->message }}</p>
                </div>
            </div>
            <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-2">
                <a href="{{ route('user.notifikasi.index') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-900">← Semua Notifikasi</a>
                @if($notification->link)
                    <a href="{{ $notification->link }}" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-sm font-bold rounded-lg transition">Buka Terkait →</a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection