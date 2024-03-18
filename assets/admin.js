import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './vendor/bootstrap/css/bootstrap.min.css';
// import 'bootstrap/dist/css/bootstrap.min.css';
import './vendor/bootstrap-icons/bootstrap-icons.css';
import './vendor/boxicons/css/boxicons.min.css';
import './vendor/quill/quill.snow.css';
import './vendor/quill/quill.bubble.css';
import './vendor/remixicon/remixicon.css';
import './styles/admin.css';

import './vendor/bootstrap/js/bootstrap.bundle.js';

document.addEventListener('turbo:before-fetch-response', (event) => {
  let location = event.detail.fetchResponse.response.headers.get('redirect-url');
  if (location) {
    window.location.replace(location);
    event.preventDefault();
  }

});

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');
