import {Controller} from '@hotwired/stimulus';
import Swal from "sweetalert2";
import window from "inputmask/lib/global/window";

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static values = {
        title: String,
        confirmTitle: String,
        text: String,
        permissionDelete: Boolean
    }

    async submit(event) {
        event.preventDefault();
        let deleteConfig = {};

        if (!this.permissionDeleteValue){
            deleteConfig = {
                showConfirmButton: false,
            }
        }

        let defaultConfig = {
            title: this.titleValue,
            text: this.textValue,
            icon: 'warning',
            customClass: {
                confirmButton: 'btn btn-danger px-4 mx-2',
                cancelButton: 'btn btn-outline-danger px-4 mx-2'
            },
            buttonsStyling: false,
            confirmButtonText: 'Удалить',
            cancelButtonText: 'Отменить',
            showCancelButton: true,
            focusConfirm: false,
            focusCancel: true,
            showLoaderOnConfirm: true,
            showCloseButton: true,
            backdrop: true,
            allowOutsideClick: () => !Swal.isLoading(),
            allowEscapeKey: () => !Swal.isLoading(),
            didOpen: () => {
                Swal.getActions().getElementsByClassName('swal2-cancel')[0].focus();
            },
            preConfirm: () => {
                return fetch($(this.element).attr('action'), {
                    method: $(this.element).attr('method'),
                    body: new FormData(this.element),
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(response.statusText)
                        }
                        return response
                    })
                    .catch(error => {
                        Swal.showValidationMessage(
                            `Request failed: ${error}`
                        )
                    })
            },
        }
        Swal.fire($.extend(true, {}, defaultConfig, deleteConfig)).then((result) => {
            if (result.isConfirmed) {
                Swal.fire(
                    'Удалено',
                    this.confirmTitleValue,
                    'success'
                )
                setTimeout(()=>{window.location.reload();}, 2000);
            }
        })
    }
}