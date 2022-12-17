import {Controller} from '@hotwired/stimulus';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['inputTitle', 'inputPriceFrom', 'inputPriceTo', 'selectCalendar'];

    connect() {
        let params = this.getAttr();
        this.initFormSearch(params);
    }

    initFormSearch(params) {

        this.inputTitleTarget.value = params[this.inputTitleTarget.getAttribute('name')] ?? '';
        this.inputPriceFromTarget.value = params[this.inputPriceFromTarget.getAttribute('name')] ?? '';
        this.inputPriceToTarget.value = params[this.inputPriceToTarget.getAttribute('name')] ?? '';

        Array.from(this.selectCalendarTarget.options).forEach((option) => {
            let valueOption = option.value;

            if (params[this.selectCalendarTarget.getAttribute('name')] === valueOption) {
                $(option).prop('selected', true);
            }
        });
    }

    getAttr()
    {
        return window
            .location
            .search
            .replace('?','')
            .split('&')
            .reduce(
                function(p,e){
                    var a = e.split('=');
                    p[ decodeURIComponent(a[0])] = decodeURIComponent(a[1]);
                    return p;
                },
                {}
            );
    }
}
