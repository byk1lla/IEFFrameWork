// Dummy Service Worker to satisfy browser requests
self.addEventListener('install', (event) => {
    // console.log('SW installed');
});

self.addEventListener('fetch', (event) => {
    // Just pass through
});
