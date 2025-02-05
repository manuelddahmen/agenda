/*
 * Copyright (c) 2024. Manuel Daniel Dahmen
 *
 *
 *    Copyright 2012-2023 Manuel Daniel Dahmen
 *
 *    Licensed under the Apache License, Version 2.0 (the "License");
 *    you may not use this file except in compliance with the License.
 *    You may obtain a copy of the License at
 *
 *        http://www.apache.org/licenses/LICENSE-2.0
 *
 *    Unless required by applicable law or agreed to in writing, software
 *    distributed under the License is distributed on an "AS IS" BASIS,
 *    WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *    See the License for the specific language governing permissions and
 *    limitations under the License.
 */


import firebase from "firebase/compat/app";
import auth from "firebase/compat/auth";
// Replace with your Firebase project configuration
// TODO: Replace the following with your app's Firebase project configuration
// See: https://firebase.google.com/docs/web/learn-more#config-object
const config = {
    apiKey: "1053386986412-q05vuknkmq57aid34r52fitjq5ku1nuk.apps.googleusercontent.com",
    authDomain: "empty3.app",
};
const app = firebase.initializeApp(firebaseConfig);

// Sign in with email/password
function signIn() {
    const email = document.getElementById("email").value;
    const password = document.getElementById("password").value;
    firebase.auth().signInWithEmailAndPassword(email, password)
        .then((userCredential) => {
            // Signed in
            const user = userCredential.user;
            alert("Logged in user" + user.email);
            console.log("User signed in:", user);
            document.location.href = 'https://empty3.app/agenda/src/?page=login&username=' + userCredential.user.email + '&password=' + userCredential;
        })
        .catch((error) => {
            // Handle errors
            const errorCode = error.code;
            const errorMessage = error.message;
            alert("Not logged ERROR");
            console.error("Error signing in:", errorCode, errorMessage);
        });
}


// Initialize Firebase Authentication and get a reference to the service
const auth1 = firebase.auth();


auth1.signInWithEmailAndPassword(email, password)
    .then((userCredential) => {
        // Signed in
        var user = userCredential.user;
        alert("Looged as email on empty3.app/agenda");
    })
    .catch((error) => {
        var errorCode = error.code;
        var errorMessage = error.message;
    });

auth1.onAuthStateChanged((user) => {
    if (user) {
        // User is signed in, see docs for a list of available properties
        // https://firebase.google.com/docs/reference/js/v8/firebase.User
        var uid = user.uid;
        // ...
    } else {
        // User is signed out
        // ...
    }
});
