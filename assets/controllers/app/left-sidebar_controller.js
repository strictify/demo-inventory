import {Controller} from '@hotwired/stimulus';

export default class extends Controller {
    connect() {
        window.addEventListener('turbo:visit', this._onVisit);
    }

    /**
     * And this is why JS is not a programming language
     */
    select(el) {
        let previousClassList = document.querySelector('#sidebar-nav .active')?.classList;
        previousClassList?.remove('active');
        previousClassList?.add('collapsed');
        let parentElement = el.target.closest('.nav-link');

        parentElement?.classList.remove('collapsed');
        parentElement?.classList.add('active');
    }

    _onVisit(event) {
        console.log(event);
        let url = event.detail.url;
        let relativeUrl = '/' + url.replace(/^(?:\/\/|[^/]+)*\//, '');

        let ul = document.getElementById('sidebar-nav');
        let allAElements = ul.querySelectorAll('a');
        allAElements.forEach(el => {
            let href = el.getAttribute('href');
            if (relativeUrl.startsWith(href)) {
                let previousClassList = document.querySelector('#sidebar-nav .active')?.classList;
                previousClassList?.remove('active');
                previousClassList?.add('collapsed');
                let parentElement = el.closest('.nav-link');

                parentElement?.classList.remove('collapsed');
                parentElement?.classList.add('active');
            }
        })
    }
}
