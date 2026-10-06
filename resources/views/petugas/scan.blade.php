<!-- resources/views/petugas/scan.blade.php -->
@extends('layouts.petugas')
@section('title', 'Scan QR Alat')
@section('page-title', 'Scan QR Alat')
@section('content')
<div class="max-w-md mx-auto space-y-6">

    {{-- Scanner Kamera --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 text-center">
        <div class="w-12 h-12 mx-auto bg-teal-50 text-teal-600 rounded-lg flex items-center justify-center mb-3">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 7V5a2 2 0 012-2h2m10 0h2a2 2 0 012 2v2m0 10v2a2 2 0 01-2 2h-2M7 21H5a2 2 0 01-2-2v-2m0-8h18M7 10h.01M12 10h.01M17 10h.01M7 14h.01M12 14h.01M17 14h.01"/>
            </svg>
        </div>
        <h2 class="text-lg font-bold text-slate-900 mb-1">Scan QR Alat</h2>
        <p class="text-sm text-slate-600 mb-4">Klik <strong>"Mulai Scan QR"</strong> lalu arahkan kamera ke QR Code alat.</p>

        {{-- Placeholder sebelum kamera aktif --}}
        <div id="scan-placeholder" class="rounded-lg border-2 border-dashed border-slate-300 bg-slate-50 py-10 flex flex-col items-center justify-center gap-3">
            <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
            </svg>
            <p class="text-sm text-slate-500">Kamera belum aktif</p>
        </div>

        {{-- Area kamera (tersembunyi sampai tombol ditekan) --}}
        <div id="qr-reader" class="rounded-lg overflow-hidden border border-slate-200 bg-slate-900 hidden"></div>

        <p id="scan-status" class="text-xs text-slate-500 mt-3">Kamera hemat baterai & privasi — hanya aktif saat Anda memulai scan.</p>

        {{-- Tombol Kontrol --}}
        <div class="flex gap-2 mt-4">
            <button id="btn-start" onclick="startScan()"
                    class="flex-1 px-4 py-2.5 bg-teal-600 text-white text-sm font-bold rounded-lg hover:bg-teal-700 transition flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 7V5a2 2 0 012-2h2m10 0h2a2 2 0 012 2v2m0 10v2a2 2 0 01-2 2h-2M7 21H5a2 2 0 01-2-2v-2m0-8h18"/>
                </svg>
                Mulai Scan QR
            </button>
            <button id="btn-stop" onclick="stopScan()"
                    class="hidden flex-1 px-4 py-2.5 bg-white border border-red-200 text-red-600 text-sm font-bold rounded-lg hover:bg-red-50 transition flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 10h6v4H9z"/>
                </svg>
                Stop Kamera
            </button>
        </div>
    </div>

    {{-- Fallback Manual --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <h3 class="text-sm font-bold text-slate-900 mb-3">Kamera tidak tersedia? Input manual:</h3>
        <form method="GET" action="{{ route('petugas.alat.index') }}" class="flex gap-2">
            <input type="text" name="search" placeholder="Contoh: ALT-0001"
                   class="flex-1 px-4 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none">
            <button class="px-4 py-2 bg-teal-600 text-white text-sm font-bold rounded-lg hover:bg-teal-700 transition">Cari</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    const appUrl = @json(url('/'));
    let html5QrCode = null;
    let scanning = false;
    let done = false;

    const elReader      = document.getElementById('qr-reader');
    const elPlaceholder = document.getElementById('scan-placeholder');
    const btnStart      = document.getElementById('btn-start');
    const btnStop       = document.getElementById('btn-stop');

    function setStatus(text) {
        document.getElementById('scan-status').textContent = text;
    }

    function toggleUI(isScanning) {
        elReader.classList.toggle('hidden', !isScanning);
        elPlaceholder.classList.toggle('hidden', isScanning);
        btnStart.classList.toggle('hidden', isScanning);
        btnStop.classList.toggle('hidden', !isScanning);
    }

    // ========== MULAI SCAN (hanya saat tombol diklik) ==========
    function startScan() {
        if (scanning) return;
        scanning = true;
        done = false;
        toggleUI(true);
        setStatus('Menyalakan kamera…');

        if (!html5QrCode) {
            html5QrCode = new Html5Qrcode("qr-reader");
        }

        html5QrCode.start(
            { facingMode: "environment" },
            { fps: 10, qrbox: { width: 220, height: 220 } },
            onScanSuccess,
            () => {} // abaikan frame yang gagal dibaca
        ).catch(err => {
            scanning = false;
            toggleUI(false);
            setStatus('⚠️ Kamera tidak dapat diakses (' + err + '). Gunakan input manual di bawah.');
        });
    }

    // ========== STOP KAMERA ==========
    function stopScan() {
        stopCamera();
        toggleUI(false);
        setStatus('Kamera dimatikan. Klik "Mulai Scan QR" untuk memindai lagi.');
    }

    function stopCamera() {
        if (html5QrCode && scanning) {
            html5QrCode.stop()
                .then(() => html5QrCode.clear())
                .catch(() => {})
                .finally(() => { scanning = false; });
        } else {
            scanning = false;
        }
    }

    // ========== HASIL SCAN ==========
    function onScanSuccess(decodedText) {
        if (done) return;
        done = true;
        setStatus('✅ QR terdeteksi! Mengalihkan…');
        stopCamera();

        let target = null;

        // Format QR: APIC|KODE|URL (alat) atau APIC-PMJ|KODE|URL (peminjaman)
        if (decodedText.startsWith('APIC|') || decodedText.startsWith('APIC-PMJ|')) {
            const parts = decodedText.split('|');
            target = parts[2] || null;
        }
        // URL polos dari aplikasi ini
        else if (decodedText.startsWith(appUrl)) {
            target = decodedText;
        }
        // Selain itu → anggap kode alat, cari manual
        else {
            target = '{{ route('petugas.alat.index') }}?search=' + encodeURIComponent(decodedText);
        }

        // Keamanan: hanya izinkan redirect ke domain sendiri
        if (target && target.startsWith(appUrl)) {
            setTimeout(() => window.location.href = target, 300);
        } else {
            done = false;
            toggleUI(false);
            setStatus('⚠️ QR bukan milik APIC. Klik "Mulai Scan QR" untuk mencoba lagi.');
        }
    }

    // Pastikan kamera dilepas saat meninggalkan halaman
    window.addEventListener('beforeunload', () => {
        if (scanning && html5QrCode) {
            try { html5QrCode.stop(); } catch (e) {}
        }
    });
</script>
@endpush