// =====================================================
// APIC - PWA Bootstrap
// =====================================================

// 1. Registrasi Service Worker
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js')
            .then(reg => console.log('[APIC] Service Worker terdaftar:', reg.scope))
            .catch(err => console.error('[APIC] Service Worker gagal:', err));
    });
}

// 2. Tangani prompt install (beforeinstallprompt)
let deferredPrompt = null;

window.addEventListener('beforeinstallprompt', (e) => {
    // Cegah prompt default browser agar bisa kita kontrol
    e.preventDefault();
    deferredPrompt = e;

    // Munculkan & aktifkan semua tombol install di halaman
    document.querySelectorAll('#installBtn, [data-pwa-install]').forEach((btn) => {
        btn.hidden = false;
        btn.disabled = false;
    });

    console.log('[APIC] Aplikasi siap di-install');
});

// 3. Klik tombol install (delegasi, aman untuk elemen yang dirender belakangan)
document.addEventListener('click', async (e) => {
    const btn = e.target.closest('#installBtn, [data-pwa-install]');
    if (!btn || !deferredPrompt) return;

    deferredPrompt.prompt();
    const { outcome } = await deferredPrompt.userChoice;
    console.log('[APIC] User memilih:', outcome);

    if (outcome === 'accepted') {
        deferredPrompt = null;
        btn.hidden = true;
    }
});

// 4. Deteksi ketika aplikasi berhasil di-install
window.addEventListener('appinstalled', () => {
    console.log('[APIC] Aplikasi berhasil di-install 🎉');
    document.querySelectorAll('#installBtn, [data-pwa-install]').forEach((btn) => {
        btn.hidden = true;
    });
});