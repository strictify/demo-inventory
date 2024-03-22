import {Controller} from '@hotwired/stimulus';

export default class extends Controller {
  connect() {
  }

  select(el) {
    let previousClassList = document.querySelector('#sidebar-nav .active')?.classList;
    previousClassList?.remove('active');
    previousClassList?.add('collapsed');
    let parentElement = el.srcElement.closest('.nav-link');

    parentElement?.classList.remove('collapsed');
    parentElement?.classList.add('active');
  }
}
