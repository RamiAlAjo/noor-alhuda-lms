// Service Worker for Push Notifications
self.addEventListener('install', function(event) {
    console.log('Service Worker installing.');
    self.skipWaiting();
});

self.addEventListener('activate', function(event) {
    console.log('Service Worker activating.');
});

self.addEventListener('push', function(event) {
    console.log('Push message received.', event);

    if (event.data) {
        const data = event.data.json();
        const options = {
            body: data.body || 'You have a new notification',
            icon: data.icon || '/favicon.ico',
            badge: '/favicon.ico',
            vibrate: [200, 100, 200],
            data: {
                url: data.url || '/',
                notification_id: data.notification_id
            },
            requireInteraction: data.requireInteraction || false,
            silent: data.silent || false,
            tag: data.tag || 'noor-notification'
        };

        event.waitUntil(
            self.registration.showNotification(data.title || 'Noor LMS', options)
        );
    }
});

self.addEventListener('notificationclick', function(event) {
    console.log('Notification click received.', event);

    event.notification.close();

    event.waitUntil(
        clients.openWindow(event.notification.data.url || '/')
    );
});

self.addEventListener('notificationclose', function(event) {
    console.log('Notification closed.', event);
});