import {Controller} from '@hotwired/stimulus';
import Inputmask from 'inputmask/dist/inputmask.es6';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static values = {
        mask: String
    }

    connect() {
        if (typeof this.maskValue === 'undefined') {
            this.maskValue = '+9999999999';
        }
        let im = new Inputmask(this.maskValue);
        im.mask($(this.element));
    }
}