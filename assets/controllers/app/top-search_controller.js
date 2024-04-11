import {Controller} from '@hotwired/stimulus';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    change({detail}) {
        let url = detail.value;
        Turbo.visit(url, {action: 'advance', frame: 'main'});
        let searchField = document.getElementById('top-search-query');
        searchField.value = ''
    }
}
