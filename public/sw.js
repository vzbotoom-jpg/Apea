const CACHE_NAME = "apic-cache-v20260811";
const OFFLINE_URL = "/offline.html";
const FILES_TO_CACHE = [
    "/",
    OFFLINE_URL,
    "/manifest.json"
];

// Pre-cache critical resources
self.addEventListener("install", (event) => {
    console.log('[APIC] Service Worker installing...');
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => cache.addAll(FILES_TO_CACHE))
    );
});

// Remove old caches
self.addEventListener("activate", (event) => {
    console.log('[APIC] Service Worker activated.');
    event.waitUntil(
        caches.keys().then(keys =>
            Promise.all(
                keys.map(key => {
                    if (key !== CACHE_NAME) {
                        return caches.delete(key);
                    }
                })
            )
        )
    );
    self.clients.claim();
});

// Listen for skip waiting message
self.addEventListener('message', (event) => {
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
});

// Fetch strategy
self.addEventListener("fetch", (event) => {
    const request = event.request;

    // Never cache non-GET requests
    if (request.method !== 'GET') {
        event.respondWith(fetch(request));
        return;
    }

    // Handle page navigation (offline fallback)
    if (request.mode === "navigate") {
        event.respondWith(
            fetch(request)
                .catch(() => caches.match(OFFLINE_URL))
        );
        return;
    }

    // Cache-first for static assets
    if (
        request.destination === "style" ||
        request.destination === "script" ||
        request.destination === "image" ||
        request.destination === "font"
    ) {
        event.respondWith(
            caches.match(request)
                .then(cached => {
                    return cached || fetch(request).then(response => {
                        return caches.open(CACHE_NAME).then(cache => {
                            cache.put(request, response.clone());
                            return response;
                        });
                    });
                })
        );
        return;
    }

    // Default: network-first with cache fallback
    event.respondWith(
        fetch(request)
            .then(response => {
                return caches.open(CACHE_NAME).then(cache => {
                    cache.put(request, response.clone());
                    return response;
                });
            })
            .catch(() => caches.match(request))
    );
});