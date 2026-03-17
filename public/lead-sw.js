const CACHE_NAME = 'lead-portal-v2';
const OFFLINE_PORTAL_FALLBACK_KEY = '/__offline_lead_portal';
const PRECACHE_URLS = [
  '/lead/login',
  '/lead/portal',
  '/lead-sw.js',
];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => cache.addAll(PRECACHE_URLS)).catch(() => {})
  );
  self.skipWaiting();
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) =>
      Promise.all(
        keys
          .filter((k) => k !== CACHE_NAME)
          .map((k) => caches.delete(k))
      )
    )
  );
  self.clients.claim();
});

self.addEventListener('fetch', (event) => {
  const req = event.request;
  if (req.method !== 'GET') return;

  // Network first for lead pages; fallback cache for offline use
  if (req.mode === 'navigate' || req.url.includes('/lead/')) {
    event.respondWith(
      fetch(req)
        .then((res) => {
          const copy = res.clone();
          caches.open(CACHE_NAME).then((cache) => {
            cache.put(req, copy).catch(() => {});

            // Persist a dedicated offline copy of authenticated lead portal HTML.
            if (
              req.mode === 'navigate' &&
              req.url.includes('/lead/portal') &&
              res.ok &&
              (res.headers.get('content-type') || '').includes('text/html')
            ) {
              cache.put(OFFLINE_PORTAL_FALLBACK_KEY, res.clone()).catch(() => {});
            }
          }).catch(() => {});
          return res;
        })
        .catch(async () => {
          const cache = await caches.open(CACHE_NAME);
          const exact = await cache.match(req);
          if (exact) return exact;

          if (req.url.includes('/lead/portal')) {
            const offlinePortal = await cache.match(OFFLINE_PORTAL_FALLBACK_KEY);
            if (offlinePortal) return offlinePortal;
          }

          const loginFallback = await cache.match('/lead/login');
          if (loginFallback) return loginFallback;

          return caches.match(req);
        })
    );
    return;
  }

  // Stale-while-revalidate for other GET assets
  event.respondWith(
    caches.match(req).then((cached) => {
      const fetchPromise = fetch(req)
        .then((res) => {
          const copy = res.clone();
          caches.open(CACHE_NAME).then((cache) => cache.put(req, copy)).catch(() => {});
          return res;
        })
        .catch(() => cached);
      return cached || fetchPromise;
    })
  );
});

