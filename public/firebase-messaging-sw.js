importScripts('https://www.gstatic.com/firebasejs/8.0.1/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.0.1/firebase-messaging.js');

var config = {
  apiKey: "AIzaSyA3ImHMW0c0Kot1fhmqLkgSmrch6BGBZnc",
  authDomain: "whagons.firebaseapp.com",
  databaseURL: "https://whagons.firebaseio.com",
  projectId: "whagons",
  storageBucket: "whagons.appspot.com",
  messagingSenderId: "414801249448",
  appId: "1:414801249448:web:27e5d866fdd97f927dae9d"
};

// Initialize the Firebase app in the service worker by passing in the
// messagingSenderId.
firebase.initializeApp(config);

// Retrieve an instance of Firebase Messaging so that it can handle background
// messages.
const messaging = firebase.messaging();

/*
messaging.onBackgroundMessage(function(payload) {
    console.log('[firebase-messaging-sw.js] Received background message ', payload);
    // Customize notification here
    const notificationTitle = 'Background Message Title';
    const notificationOptions = {
      body: 'Background Message body.',
      icon: 'assets/whagons/logo/icon_white.svg',
      data: { url: payload.data.subdomain }
    };

    return self.registration.showNotification(notificationTitle, notificationOptions);
});


self.addEventListener('notificationclick', function(event) {

  console.log(event);

  clients.openWindow(event.notification.data.url);
}
, false);
*/