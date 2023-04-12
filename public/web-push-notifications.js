
var firebaseConfig = {
    apiKey: "AIzaSyA3ImHMW0c0Kot1fhmqLkgSmrch6BGBZnc",
    authDomain: "whagons.firebaseapp.com",
    databaseURL: "https://whagons.firebaseio.com",
    projectId: "whagons",
    storageBucket: "whagons.appspot.com",
    messagingSenderId: "414801249448",
    appId: "1:414801249448:web:27e5d866fdd97f927dae9d"
};

// Initialize Firebase
firebase.initializeApp(firebaseConfig);

// Retrieve Firebase Messaging object.
messaging = firebase.messaging();

//messaging.usePublicVapidKey('BC6pyjMj3p0Oi7ttsiRrbMMMG_2hraTJCjtpERqJgtcA426YlWgiGO1h1umQ86zeOQ4hofpBu2AdCZkDuw6-BkM');

messaging.requestPermission().then(function() {
    console.log('Notification permission granted.');
}).catch(function(err) {
    console.log('Unable to get permission to notify.', err);
});

messaging.getToken({vapidKey: "BC6pyjMj3p0Oi7ttsiRrbMMMG_2hraTJCjtpERqJgtcA426YlWgiGO1h1umQ86zeOQ4hofpBu2AdCZkDuw6-BkM"}).then(function(currentToken) {
    if(currentToken){
        saveToken(currentToken);
    }
    else
    {
        console.log('No Instance ID token available. Request permission to generate one.');
    }
}).catch(function(err) {
    console.log('An error occurred while retrieving token. ', err);
});

  messaging.onMessage(function(payload) {

    console.log("Message received. ", payload);

    if(payload.data.type == "chat") return;

    getNotifications();

    switch(payload.data.type)
    {
      case "task":

        PNotify.success({
          title: payload.notification.title,
          text: payload.notification.body,
          icon: 'fas fa-check-circle',
          hide: false,
          closer: true,
          modules: {
            Confirm: {
              confirm: true,
              buttons: [{
                text: 'Ver',
                primary: true,
                click: function(notice) {
                  let filter = {logic: "and", filters: [{field: "id", value: payload.data.data, operator: "eq"}]};
                  setFilter(filter);
                  notice.close();
                }
              },
              {
                text: 'Cancelar',
                primary: true,
                addClass: "btn-danger",
                click: function(notice) {
                  notice.close();
                }
              }]
            },
            Buttons: {
              closer: true,
              sticker: false
            },
            History: {
              history: false
            }
          }
        });
        
        break;

      case "room":

        PNotify.info({
          title: payload.notification.title,
          text: payload.notification.body,
          icon: 'fas fa-check-circle',
          hide: false,
          closer: true,
          modules: {
            Confirm: {
              confirm: true,
              buttons: [{
                text: 'Ver',
                primary: true,
                click: function(notice) {
                  document.location.href = "dashboard-cleaning";
                  notice.close();
                }
              },
              {
                text: 'Cancelar',
                primary: true,
                addClass: "btn-danger",
                click: function(notice) {
                  notice.close();
                }
              }]
            },
            Buttons: {
              closer: true,
              sticker: false
            },
            History: {
              history: false
            }
          }
        });  

        break;
    }


});

function saveToken(token)
{
  if(!("user" in window)) return;

  let request = callAjax('saveToken', 'POST', {'token': token, 'os': 'WEB'}, false);

  request.done(function(result) {
        console.log(result);
  
  }).fail(function( jqXHR, status ) {
    console.log(jqXHR);
  });
}