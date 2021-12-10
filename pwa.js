// version and contentToCache are supplied by IDEController.py during deploy
const cacheName = 'Project1-2021-12-10 13:47:58.794710'; // unique value, generated each deploy
const contentToCache = [
  'code.js',
  'index.html',
  'nsb/images/192.png',
  'nsb/images/512.png',
  'nsb/images/72.png',
  'nsb/images/ajax-loader.gif',
  'nsb/library/appstudioFunctions.js',
  'nsb/library/jquery.modal.min.css',
  'nsb/library/jquery.modal.min.js',
  'nsb/library/jquery3.js',
  'toolbox/as/dist/asStyle.css',
];


const trace = false;
if (trace) console.log('[pwa.js] Startup', cacheName);

self.addEventListener('activate', ((e) => {
  'use strict';

  if (trace) console.log('[pwa.js] Clear old caches');
  e.waitUntil(
    caches.keys().then((keyList) => {
      if (trace) console.log('keylist', keyList);
      return Promise.all(keyList.map((key) => {
        if (trace) console.log('  Key:', key);
        if (cacheName.indexOf(key) === -1  && key.substr(0, 'Project1'.length) === 'Project1') {
          if (trace) console.log('  Delete:', key);
          return caches.delete(key);
        }
      }));
    }),
  );
}));

self.addEventListener('install', (e) => {
  'use strict';

  if (trace) console.log('[pwa.js] Install');
  e.waitUntil(
    caches.open(cacheName).then((cache) => {
      if (trace) console.log('[pwa.js] Caching all: app shell and content');
      return cache.addAll(contentToCache);
    }),
  );
});

self.addEventListener('fetch', (e) => {
  'use strict';

  if (trace) console.log('[pwa.js] fetch', e.request.url)
  // override Chromium bug: https://stackoverflow.com/questions/48463483/what-causes-a-failed-to-execute-fetch-on-serviceworkerglobalscope-only-if
  if (e.request.cache === 'only-if-cached' && e.request.mode !== 'same-origin') return;
  e.respondWith(
    caches.open(cacheName)
      .then(cache => cache.match(e.request, { ignoreSearch: true }))
      .then(response => response || fetch(e.request)),
  );
});

if (typeof self.skipWaiting === 'function') {
  if (trace) console.log('[pwa.js] self.skipWaiting() is supported.');
  self.addEventListener('install', function(e) {
    // See https://slightlyoff.github.io/ServiceWorker/spec/service_worker/index.html#service-worker-global-scope-skipwaiting
    e.waitUntil(self.skipWaiting());
  });
} else {
  if (trace) console.log('[pwa.js] self.skipWaiting() is not supported.');
}

if (self.clients && (typeof self.clients.claim === 'function')) {
  if (trace) console.log('[pwa.js] self.clients.claim() is supported.');
  self.addEventListener('activate', function(e) {
    // See https://slightlyoff.github.io/ServiceWorker/spec/service_worker/index.html#clients-claim-method
    e.waitUntil(self.clients.claim());
  });
} else {
  if (trace) console.log('[pwa.js] self.clients.claim() is not supported.');
}

/* Add to home screen prompt:
https://developers.google.com/web/fundamentals/app-install-banners/#criteria
- needs an Install button to show

- Should PWA stuff happen for PhoneGap? no, but we can't check for cordova.js
- Any reason to have a switch for PWA?
- What triggers the update? Simple refresh doesn't do it.
https://developers.google.com/web/fundamentals/primers/service-workers/#update-a-service-worker

- Clear cache happens on activate. When does that happen?
https://github.com/deanhume/pwa-update-available

- remove AddToHomeScreen on Chrome
https://developers.google.com/web/updates/2019/05/mini-infobar-update
*/

/*
Get list of current caches:
caches.keys().then((keyList) => {console.log(keyList)})
*/

