import 'datatables.net-bs5/js/dataTables.bootstrap5';
import 'datatables.net-bs5/css/dataTables.bootstrap5.css';
import './table.less';
import initSelect2 from "../select2/initSelect2";
import {startSpin, stopSpin} from "../spin";

function getConfig(url, searchPlaceholder, columnsValue, dataCallback) {
    if (typeof searchPlaceholder === 'undefined') {
        url = '';
    }
    if (typeof searchPlaceholder === 'undefined') {
        searchPlaceholder = '';
    }
    if (typeof columnsValue === 'undefined') {
        columnsValue = {};
    }

    let columns = [];
    Object.keys(columnsValue).forEach(column => {
        const data = columnsValue[column];
        columns.push({
            data: column,
            sClass: column + '-th' + (data.addClass ? ' ' + data.addClass : ''),
            sortable: data.sortable === undefined || data.sortable === true,
            visible: data.visible === undefined || data.visible === true
        })
    });

    return {
        lengthMenu: [10, 25, 50, 100],
        pageLength: 10,
        dom: "<'container-fluid mt-5 mt-sm-1 mb-3 lh-base px-1 px-lg-4' <'row justify-content-end mb-2' <'col-12 col-sm-5 col-md-4 col-xl-3 text-end' f>><'row' <'col-sm-12' tr>><'row' <'col-12 col-lg-6' <'row align-items-center mb-3 mb-lg-0' <'col-12 col-sm-6 col-lg-12 text-start' l><'col-12 col-sm-6 col-lg-12 text-start' i>>><'col-12 col-lg-6' p>>>",
        searching: true,
        order: [[0, "desc"]],
        ajax: {
            url: url,
            type: "POST",
            data: dataCallback,
            beforeSend: function () {
                startSpin($(".dataTables_processing"), {color:'#003290', top:'50px'});
            },
            complete: function () {
                stopSpin($(".dataTables_processing"));
                initSelect2();
            },
        },
        fixedColumns: {
            heightMatch: 'none'
        },
        processing: true,
        bFilter: false,
        serverSide: true,
        columns: columns,
        autoWidth: false,
        bPaginate: true,
        bLengthChange: true,
        iCookieDuration: 60*60*24*31,
        bSort: true,
        bInfo: true,
        bAutoWidth: false,
        bStateSave: true,
        language: {
            decimal: ",",
            emptyTable: "Данные отсутствуют в таблице",
            info: "Показаны _START_-_END_ (всего _TOTAL_ записей)",
            infoEmpty: "Показаны 0 из 0 (всего 0 записей)",
            infoFiltered: "(отфильтровано из _MAX_ записей)",
            infoPostFix: "",
            thousands: " ",
            lengthMenu: "<span class='me-1'>По </span> _MENU_ <span class='ms-1'> записей.</span>",
            loadingRecords: "",
            processing: '',
            search: "_INPUT_",
            searchPlaceholder: searchPlaceholder,
            zeroRecords: "Совпадающих записей не найдено",
            paginate: {
                first: "Первая",
                last: "Последняя",
                next: "Следующая",
                previous: "Предыдущая"
            },
            aria: {
                paginate: {
                    first: "Первая",
                    previous: "Последняя",
                    next: "Следующая",
                    last: "Предыдущая"
                },
                sortAscending:  ": сортировка по возрастанию",
                sortDescending: ": сортировка по убыванию"
            }
        },
        classes: {
            sSortAsc: "sorting_asc",
            sSortDesc: "sorting_desc",
            sSortable: "sorting",
            sSortableAsc: "sorting_asc_disabled",
            sSortableDesc: "sorting_desc_disabled",
            sSortableNone: "sorting_disabled",
            sSortIcon: "DataTables_sort_icon",
        }
    };
}

export {getConfig}