import {Controller} from '@hotwired/stimulus';
import {getConfig} from '../plugins/table/table';
import '../plugins/fancybox';
import '../plugins/unserialize';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static values = {
        url: String,
        columns: Object,
        searchTitle: String,
        type: String
    }

    connect() {
        window.oTable = $(this.element).find('table').DataTable(
            $.extend({},
                getConfig(
                    this.urlValue,
                    this.searchTitleValue,
                    this.columnsValue,
                    (data) => {
                        const filters = $(this.element).closest('.table-with-filter').find('form.filters');
                        let form = filters.unserialize();
                        for(let k in form){
                            if(form.hasOwnProperty(k) && form[k]){
                                data[k] = form[k];
                            }
                        }
                        const searchParams = new URLSearchParams(window.location.search.substring(1));

                        for (let p of searchParams.keys()) {
                            let vs, ps;
                            if (p.indexOf('[]') >= 0) {
                                vs = [];
                                for (let v of searchParams.getAll(p)) {
                                    vs.push(v);
                                }
                                ps = p.substring(0, p.length-2)
                            } else {
                                vs = searchParams.get(p);
                                ps = p;
                            }
                            data[ps] = vs;
                        }
                    }
                ),
                {}
            ));

        // realtime.setListener('update_table', (data) => {
        //     if (data.type === this.typeValue) {
        //         this.reloadTable();
        //     }
        // })
    }

    reloadTable() {
        oTable.ajax.reload();
    }
}