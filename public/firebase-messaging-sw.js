importScripts('https://www.gstatic.com/firebasejs/7.9.3/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/7.9.3/firebase-messaging.js');

// Initialize the Firebase app in the service worker by passing in the
// messagingSenderId.
firebase.initializeApp({
    apiKey: "AIzaSyACuRKV9V-tCEUYHUK0jNGn_lJsAomShrs",
    authDomain: "cgl-erp.firebaseapp.com",
    projectId: "cgl-erp",
    storageBucket: "cgl-erp.appspot.com",
    messagingSenderId: "38544840864",
    appId: "1:38544840864:web:81147203cb2c5f92e4d1c5",
    measurementId: "G-2XD5HLJTE3"
});

// Retrieve an instance of Firebase Messaging so that it can handle background
// messages.
const messaging = firebase.messaging();

messaging.setBackgroundMessageHandler(function(payload) {
    console.log('[firebase-messaging-sw.js] Received background message ', payload);
    // Customize notification here
    const notificationTitle = 'Background Message Title';
    const notificationOptions = {
        body: 'Background Message body.',
        icon: 'https://meyerfood.id/public/images/meyer-logo.png'
    };

    return self.registration.showNotification(notificationTitle,
        notificationOptions);
});