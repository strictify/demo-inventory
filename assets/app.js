import './bootstrap.js';
import '@hotwired/turbo';

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
import './vendor/remixicon/remixicon.css';
import './styles/admin.css';
import './styles/extras.css';

import './vendor/bootstrap/js/bootstrap.bundle.js';

document.addEventListener('turbo:before-fetch-response', (event) => {
  let response = event?.detail?.fetchResponse?.response;
  let status = response?.status ?? null;
  let headers = response.headers;
  let location = headers.get('redirect-url');
  let frame = headers.get('frame');
  if (status !== 204) {
    return;
  }
  if (!location) {
    return;
  }
  if (!frame) {
    window.location.replace(location);
    event.preventDefault();

    return false;
  }

  Turbo.visit(location, {action: 'advance', frame: frame});
  event.preventDefault();

  return false;
});

// console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');
