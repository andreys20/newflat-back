import {Controller} from '@hotwired/stimulus';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['sortSelect', 'titleCatalog'];

    connect() {
        let params = this.getAttr();
        this.initSelect(params);

    }

    change() {
        this.setAttr(
            'order',
            this.sortSelectTarget.options[this.sortSelectTarget.selectedIndex].value
        )
        this.setAttr(
            'sort',
            this.sortSelectTarget.options[this.sortSelectTarget.selectedIndex].getAttribute('name')
        )
        location.reload();
    }

    initSelect(params)
    {
        Array.from(this.sortSelectTarget.options).forEach((option) => {
            let nameOption = option.getAttribute('name');
            let valueOption = option.value;

            if (params['sort'] === nameOption && params['order'] === valueOption) {
                $(option).prop('selected', true);
            }
        });
    }

    setAttr(prmName,val)
    {
        var res = '';
        var d = location.href.split("#")[0].split("?");
        var base = d[0];
        var query = d[1];
        if(query) {
            var params = query.split("&");
            for(var i = 0; i < params.length; i++) {
                var keyval = params[i].split("=");
                if(keyval[0] !== prmName) {
                    res += params[i] + '&';
                }
            }
        }
        res += prmName + '=' + val;

        history.pushState(null, null, base + '?' + res);
        return false;
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
