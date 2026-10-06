<!-- resources/views/partials/scripts.blade.php -->

<!-- Alpine.js -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>

@vite(['resources/js/app.js'])

<script>
    // Auto dismiss alerts
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(() => {
            document.querySelectorAll('.alert-dismissible').forEach(alert => {
                setTimeout(() => {
                    alert.style.transition = 'opacity 0.5s';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 500);
                }, 3000);
            });
        }, 100);
    });

    // Search functionality
    function setupSearch() {
        const searchInput = document.getElementById('searchInput');
        const searchResults = document.getElementById('searchResults');

        if (!searchInput) return;

        let searchTimeout;

        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();

            if (query.length < 2) {
                searchResults?.classList.add('hidden');
                return;
            }

            searchTimeout = setTimeout(() => {
                fetch(`/search?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (!searchResults) return;
                        
                        if (data.length === 0) {
                            searchResults.innerHTML = `
                                <div class="p-4 text-center text-gray-500 text-sm">
                                    Tidak ada alat yang ditemukan
                                </div>
                            `;
                        } else {
                            searchResults.innerHTML = data.map(alat => `
                                <a href="/user/alat/${alat.id}" 
                                   class="flex items-center gap-3 p-3 hover:bg-gray-50 border-b border-gray-100 last:border-0 transition-colors duration-200">
                                    <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center text-primary-600 text-sm font-bold">
                                        ${alat.nama_alat.charAt(0)}
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-800">${alat.nama_alat}</p>
                                        <p class="text-xs text-gray-500">${alat.kode_alat} · Rp ${new Intl.NumberFormat('id-ID').format(alat.harga_sewa_per_hari)}/hari</p>
                                    </div>
                                </a>
                            `).join('');
                        }
                        searchResults.classList.remove('hidden');
                    })
                    .catch(() => {
                        if (searchResults) {
                            searchResults.innerHTML = `
                                <div class="p-4 text-center text-red-500 text-sm">
                                    Terjadi kesalahan
                                </div>
                            `;
                            searchResults.classList.remove('hidden');
                        }
                    });
            }, 300);
        });

        document.addEventListener('click', function(e) {
            if (searchResults && !searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                searchResults.classList.add('hidden');
            }
        });
    }

    // Smooth scroll untuk anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href === '#') return;
            
            e.preventDefault();
            const target = document.querySelector(href);
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Navbar scroll effect
    let lastScroll = 0;
    window.addEventListener('scroll', function() {
        const nav = document.querySelector('nav.navbar-main');
        if (!nav) return;
        
        const currentScroll = window.pageYOffset;
        
        if (currentScroll > 100) {
            nav.classList.add('shadow-md');
            nav.style.backgroundColor = 'rgba(255, 255, 255, 0.95)';
            nav.style.backdropFilter = 'blur(8px)';
        } else {
            nav.classList.remove('shadow-md');
            nav.style.backgroundColor = '';
            nav.style.backdropFilter = '';
        }
        lastScroll = currentScroll;
    });
</script>

@stack('scripts')