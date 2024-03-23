import {Controller} from '@hotwired/stimulus';

export default class extends Controller {
  static targets = ["collectionContainer"]

  static values = {
    index: Number,
    minimum: Number,
    prototype: String,
  }

  connect() {
    let min = this.minimumValue;
    let indexValue = this.indexValue;
    if (min > indexValue) {
      this.addCollectionElement();
    }

    console.log(this.prototypeValue);
  }

  addCollectionElement() {
    const item = document.createElement('tr');
    item.innerHTML = this.prototypeValue.replace(/__name__/g, this.indexValue);
    this.collectionContainerTarget.appendChild(item);
    this.indexValue++;
  }

  remove(event) {
    let closest = event.target.closest(`tr`);
    closest.parentNode.removeChild(closest);
  }
}
