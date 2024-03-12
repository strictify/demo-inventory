import {Controller} from '@hotwired/stimulus';

export default class extends Controller {
  connect() {
  }

  select(el) {
    document.querySelector('#sidebar-nav a.active')?.classList.remove('active');
    let parentElement = el.srcElement.parentElement;

    parentElement?.classList.add('active');

    console.log(parentElement);
  }
}
