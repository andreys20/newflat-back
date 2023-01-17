global.$ = global.jQuery = $;

import "select2";
import "select2/dist/js/i18n/ru";
import "select2/dist/css/select2.min.css";
import "./select2.less";

$(function(){
    initSelect2();

    $(document).on('focus', '.select2.select2-container', function (e) {
        let isOriginalEvent = e.originalEvent
        let isSingleSelect = $(this).find(".select2-selection--single").length > 0
        if (isOriginalEvent && isSingleSelect) {
            $(this).siblings('select:enabled').select2('open');
        }

    });

});

function initSelect2() {
    $('select').select2({
        language: "ru",
        minimumResultsForSearch: 10,
    });
}

export default initSelect2;
