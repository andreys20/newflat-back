import {Controller} from '@hotwired/stimulus';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    connect() {
        Array.from(this.element.children).forEach((item) => {
            this.paginate(item);
        })
    }

    paginate(item) {
        $(item).on('click', (e) => {
            this.setAttr(
                'page',
                item.dataset.value
            )
            location.reload();
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
        //window.history.pushState("object or string", "Title", base + '?' + res);
        //window.location.href = base + '?' + res;
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
