import {Controller} from '@hotwired/stimulus';

import '../../vendor/quill/quill.snow.css'
// import '../../vendor/quill/quill.bubble.css'
import Quill from "quill";

/* stimulusFetch: 'lazy' */
export default class extends Controller {
  static values = {
    target: String,
  }
  static targets = [
    'spinner',
  ]

  connect() {
    let targetId = this.targetValue;
    let textArea = document.getElementById(targetId);

    const quill = new Quill('#quill-' + targetId, {
      theme: 'snow',
      // placeholder: 'Hello, World!',
    });
    this.spinnerTarget.remove();

    quill.setText(textArea.value);
    quill.on('text-change', () => {
      textArea.value = quill.getText();
    });
  }
}
