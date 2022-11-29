import {Controller} from '@hotwired/stimulus';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['table'];

    connect() {
        let items = this.tableTarget.getElementsByClassName("flats-list__item");

        Array.from(items).forEach((el) => {
            let btn = el.querySelector(".btn");

            btn.addEventListener("click", function(){

                let rows = el.querySelectorAll("tr");
                Array.from(rows).forEach((tr) => {
                    tr.style.display = 'table-row'
                });

                btn.style.display = 'none'
            }, false);
        });
    }

}
