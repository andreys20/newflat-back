import {Controller} from '@hotwired/stimulus';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    changeFilter(event) {
        //event.preventDefault();
        console.log(this.element);
    }

}
