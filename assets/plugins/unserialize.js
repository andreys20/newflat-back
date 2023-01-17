$.fn.unserialize = function(){
    return $.unserialize($(this).find(':input').serialize());
};
$.unserialize = function(serializedString){
    let str = decodeURI(serializedString.replace(/\+/g," "));
    let pairs = str.split('&');
    let obj = {}, p, idx;
    for (let i=0, n=pairs.length; i < n; i++) {
        p = pairs[i].split('=');
        idx = p[0];

        if (idx.indexOf("[]") === (idx.length - 2)) {
            let ind = idx.substring(0, idx.length-2);
            if (obj[ind] === undefined) {
                obj[ind] = [];
            }
            obj[ind].push(p[1]);
        }
        else {
            obj[idx] = p[1];
        }
    }
    return obj;
};