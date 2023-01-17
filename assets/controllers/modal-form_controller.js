import {Controller} from '@hotwired/stimulus';
import { Modal } from 'bootstrap';
import {startSpin} from "../plugins/spin";
import {closeModal} from "../plugins/bootstrapModal";
import initSelect2 from "../plugins/select2/initSelect2";

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['modal', 'modalBody', 'modalTitle'];
    static values = {
        formUrl: String,
        title: String,
        closes: Boolean,
        modalClass: String
    }

    modalForm = '<div class="modal fade ' + this.modalClassValue + '" tabindex="-1" aria-hidden="true" id="mainModal">' +
        '<div class="modal-dialog modal-dialog-centered">' +
        '<div class="modal-content border-0">' +
        '<div class="modal-header my-3 mx-4 px-3 p-1 border-0 align-items-start">' +
        '<h5 class="modal-title text-start"></h5>' +
        '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>' +
        '</div>' +
        '<div class="modal-body m-0 mx-4 mb-3 px-3 p-1 text-center"></div>' +
        '</div>' +
        '</div>' +
        '</div>';

    modalOptions = {
        backdrop: true,
        keyboard: true,
        focus: true
    };

    mainModal = '';

    async openModal() {
        closeModal();

        let fragment = document.createElement("div");
        fragment.innerHTML = this.modalForm;
        document.body.appendChild(fragment);

        this.mainModal = document.getElementById('mainModal');
        const body = this.mainModal.querySelector('.modal-body');
        const title = this.mainModal.querySelector('.modal-title');
        body.innerHTML = '<div data-controller="loading" data-loading-options-value=\'{"color":"#DD3C43","top":"50%","scale":"1"}\'></div>';
        title.innerHTML = this.titleValue;
        let options = {};
        if (this.closesValue) {
            options = {backdrop: 'static', keyboard: false}
            this.mainModal.querySelector('.btn-close').style.display = 'none';
        }
        const modal = new Modal(this.mainModal, $.extend(this.modalOptions, options));
        modal.show();
        document.activeElement.blur();
        body.innerHTML = await $.ajax(this.formUrlValue);
        setTimeout(() => {initSelect2()},100);
        this.mainModal.addEventListener('hidden.bs.modal', (event) => {
            event.target.parentNode.remove();
        })
        await this.addListenerSubmit();
    }

    async submitForm(form) {
        const $form = $(form);
        const $loading = $form.find('.loading');
        if ($loading) {
            $loading.css({'color':'transparent'});
            const options = $loading.data('loadingOptions');
            startSpin($loading, options ? options : {});
        }
        $form.closest('.modal-body').find('.alert.alert-danger').remove();
        let xhr = new XMLHttpRequest();
        const response = await $.ajax({
            url: $form.attr('action'),
            method: $form.attr('method'),
            data: new FormData($form[0]),
            processData: false,
            contentType: false,
            xhr: function() {
                return xhr;
            }
        })
        if (xhr.responseText.indexOf('DOCTYPE') >= 0) {
            window.location = xhr.responseURL;
        } else if (typeof response.draw !== 'undefined') {
            window.location = xhr.responseURL;
        } else {
            const modalBody = this.mainModal.querySelector('.modal-body');
            modalBody.innerHTML = response;
            let tmpTitle = modalBody.querySelector('.title');
            let tmpClosesEl = modalBody.querySelector('.closes');
            let tmpClosesValue = false;
            if (tmpClosesEl) {
                tmpClosesValue = tmpClosesEl.textContent === 'true';
                tmpClosesEl.remove();
            }
            if (tmpTitle) {
                const modalTitle = this.mainModal.querySelector('.modal-title');
                modalTitle.innerHTML = tmpTitle.innerHTML;
                tmpTitle.remove();
            }
            const modalEl = document.getElementById('mainModal');
            const modal = Modal.getInstance(modalEl);
            if (tmpClosesValue) {
                this.mainModal.querySelector('.btn-close').style.display = 'none';
                modal._config.keyboard = false;
                modal._config.backdrop = 'static';
            } else {
                this.mainModal.querySelector('.btn-close').style.display = 'block';
                modal._config.keyboard = true;
                modal._config.backdrop = true;
            }
            setTimeout(() => {initSelect2()},100);
            await this.addListenerSubmit();
        }
    }

    async addListenerSubmit() {
        const form = this.mainModal.getElementsByTagName('form');
        if (form.length > 0) {
            form[0].addEventListener('submit', (event) => {
                event.preventDefault();
                this.submitForm(event.target);
            })
        }
    }

    getFormData($form) {
        let array = $form.serializeArray();
        let indexed_array = {};

        $.map(array, function(n){
            indexed_array[n['name']] = n['value'];
        });

        return indexed_array;
    }
}
