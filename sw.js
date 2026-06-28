const CACHE_NAME = 'student-link-v1';
const ASSETS = [
  '/',
  'src/View/login.php',
  'public/assets/css/style.css' // Rajouter les fichiers css principaux
];

// Installation du Service Worker et mise en cache des fichiers de base
self.addEventListener('install', (e) => {
  e.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      return cache.addAll(ASSETS);
    })
  );
});

// Intercepter les requêtes pour servir le contenu depuis le cache si on est hors-ligne
self.addEventListener('fetch', (e) => {
  e.respondWith(
    caches.match(e.request).then((response) => {
      return response || fetch(e.request);
    })
  );
});