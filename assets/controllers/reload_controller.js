import {Controller} from '@hotwired/stimulus';

/* xstimulusFetch: 'lazy' */
export default class extends Controller {
    static values = {
        id: String,
    };

    connect() {
        let id = this.idValue;
        this._doReload(id);
        this.element.remove();
    }

    _doReload(id) {
        let frame = document.getElementById(id);
        if (!frame) {
            return;
        }
        let reloadSrc = frame.getAttribute('data-reload-src');
        if (reloadSrc) {
            fetch(reloadSrc)
            .then(response => {
                if (response.ok) {
                    return response.text()
                }
                throw response.status
            })
            .then(html => {
                Turbo.renderStreamMessage(html)

            })
            .catch(error => console.warn(error))


            // let a = document.createElement('a');
            // a.style.display = 'none';
            // a.href = reloadSrc;
            // frame.appendChild(a);
            // a.click()
            //
            // return;
        }

        // frame.reload();
    }
}
