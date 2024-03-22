import {Controller} from '@hotwired/stimulus';

import EditorJS from '@editorjs/editorjs';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
  static values = {
    target: String,
  }

  connect() {
    let targetId = this.targetValue;
    const editor = new EditorJS({
      holder: targetId
    });
  }
}
