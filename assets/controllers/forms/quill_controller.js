import {Controller} from '@hotwired/stimulus';

import 'quill/dist/quill.snow.css'
import Quill from "quill";

/* x-stimulusFetch: 'lazy' */
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
    let placeholder = textArea.getAttribute('placeholder');

    const quill = new Quill('#quill-' + targetId, {
      theme: 'snow',
      placeholder: placeholder,
    });
    this.spinnerTarget.remove();

    quill.on('text-change', () => {
      textArea.value = quill.getSemanticHTML();
    });
  }
}
