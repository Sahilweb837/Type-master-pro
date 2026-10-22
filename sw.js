/**
 * Custom Type Master Pro - Service Worker
 * Enables PWA installability and desktop shortcut features
 */

const CACHE_NAME = 'typemaster-cache-v1';

self.addEventListener('install', (event) => {
  self.skipWaiting();
});

self.addEventListener('activate', (event) => {
  event.waitUntil(self.clients.claim());
});

self.addEventListener('fetch', (event) => {
  // Let browser handle network requests naturally
  event.respondWith(
    fetch(event.request).catch(() => caches.match(event.request))
  );
});
