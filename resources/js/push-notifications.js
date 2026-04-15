// resources/js/push-notifications.js
import { initializeApp } from "firebase/app";
import { getMessaging, getToken, onMessage } from "firebase/messaging";

const firebaseConfig = {
    apiKey: import.meta.env.VITE_FIREBASE_API_KEY,
    authDomain: import.meta.env.VITE_FIREBASE_AUTH_DOMAIN,
    projectId: import.meta.env.VITE_FIREBASE_PROJECT_ID,
    storageBucket: import.meta.env.VITE_FIREBASE_STORAGE_BUCKET,
    messagingSenderId: import.meta.env.VITE_FIREBASE_MESSAGING_SENDER_ID,
    appId: import.meta.env.VITE_FIREBASE_APP_ID
};

const app = initializeApp(firebaseConfig);
const messaging = getMessaging(app);

// Register Service Worker
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/firebase-messaging-sw.js')
        .then((registration) => {
            console.log('Service Worker registered with scope:', registration.scope);
        }).catch((err) => {
            console.error('Service Worker registration failed:', err);
        });
}

export const requestPermissionAndGetToken = async () => {
    try {
        const permission = await Notification.requestPermission();
        if (permission === 'granted') {
            const token = await getToken(messaging, {
                vapidKey: import.meta.env.VITE_FIREBASE_MESSAGING_VAPID_KEY
            });
            
            if (token) {
                console.log('FCM Token:', token);
                await saveTokenToBackend(token);
                return token;
            } else {
                console.warn('No registration token available. Request permission to generate one.');
            }
        }
    } catch (error) {
        console.error('An error occurred while retrieving token: ', error);
    }
    return null;
};

const saveTokenToBackend = async (token) => {
    try {
        const response = await fetch('/api/save-fcm-token', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ token })
        });
        
        if (response.ok) {
            window.dispatchEvent(new CustomEvent('fcm-token-saved'));
        }
    } catch (error) {
        console.error('Failed to save FCM token to backend:', error);
    }
};

// Handle foreground messages
onMessage(messaging, (payload) => {
    console.log('Message received. ', payload);
    // You can use Filament's browser notification or a simple toast here
    if (window.Filament) {
        window.dispatchEvent(new CustomEvent('notification-received', { detail: payload }));
    }
});

// Expose to global scope for the "Enable Notifications" button
window.requestPushPermission = requestPermissionAndGetToken;
