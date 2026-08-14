self.addEventListener('install', () => self.skipWaiting());
self.addEventListener('activate', () => self.clients.claim());
self.addEventListener('fetch', (event) => {
    event.respondWith(fetch(event.request));
});

self.addEventListener('push', (event) => {
    const dados = event.data ? event.data.json() : {};

    event.waitUntil(
        self.registration.showNotification(dados.title || 'Gerenciador de Finanças', {
            body: dados.body || '',
            icon: dados.icon || '/icons/favicon-180.png',
            data: dados.data || {},
        })
    );
});

self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    const url = (event.notification.data && event.notification.data.url) || '/';

    event.waitUntil(
        clients.matchAll({ type: 'window' }).then((janelas) => {
            for (const janela of janelas) {
                if (janela.url.includes(url) && 'focus' in janela) {
                    return janela.focus();
                }
            }
            return clients.openWindow(url);
        })
    );
});
