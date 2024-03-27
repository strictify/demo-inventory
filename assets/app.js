import './bootstrap.js';
import '@hotwired/turbo';


import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap-icons/font/bootstrap-icons.min.css'
import './styles/admin.css';
import './styles/extras.css';
import 'animate.css/animate.min.css';

import 'bootstrap';

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

// monkeypatch: https://github.com/hotwired/turbo/pull/579#issuecomment-1403990185
document.addEventListener('turbo:before-fetch-request', (event) => {
  const targetTurboFrame = event.target.getAttribute('data-turbo-frame');
  const fetchTurboFrame = event.detail.fetchOptions.headers['Turbo-Frame'];
  if (targetTurboFrame && targetTurboFrame != fetchTurboFrame && document.querySelector(`turbo-frame#${targetTurboFrame}`)) {
    event.detail.fetchOptions.headers['Turbo-Frame'] = targetTurboFrame;
  }
});

// console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');
