import {Controller} from '@hotwired/stimulus';

export default class extends Controller {
  connect() {
    this.element.addEventListener('click', () => {
      let classList = this.element.closest('turbo-frame').classList;
      // classList.add('animate__animated');
      // classList.add('animate__fadeOutRightBig');
      this.element.closest('turbo-frame').innerHTML = '';
    })
  }
}
