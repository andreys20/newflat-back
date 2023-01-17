global.$ = global.jQuery = $;

import { Fancybox } from "@fancyapps/ui";
import "@fancyapps/ui/dist/fancybox.css";
import '../styles/fancybox-custom.css';

$(function(){
    fancybox();
});

function fancybox(){
    Fancybox.bind("[data-fancybox]", {
        infinite: false,
    });
}

export default fancybox;
