<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>QR {{ $alat->kode_alat }}</title>
<style>
    body { font-family: Arial, sans-serif; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; background: #fff; }
    .card { text-align: center; border: 2px dashed #0d9488; padding: 24px 32px; border-radius: 12px; }
    img { width: 220px; height: 220px; }
    h1 { font-size: 16px; margin: 12px 0 2px; color: #0f172a; }
    p { font-size: 12px; color: #64748b; margin: 0; }
</style>
</head>
<body>
<div class="card">
    <img src="{{ route('qr.alat', $alat) }}" alt="QR {{ $alat->kode_alat }}">
    <h1>{{ $alat->nama_alat }}</h1>
    <p>{{ $alat->kode_alat }} · APIC — SMK Muhammadiyah 1 Bantul</p>
</div>
<script>window.onload = () => setTimeout(() => window.print(), 300);</script>
</body>
</html>