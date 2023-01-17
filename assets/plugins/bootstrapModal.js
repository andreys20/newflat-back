import {Modal} from "bootstrap";

function closeModal() {
    if (document.body.classList.contains('modal-open')) {
        const modalEl = document.getElementById('mainModal');
        const modal = Modal.getInstance(modalEl);
        modalEl.classList.remove('fade');
        modal._backdrop._config.isAnimated = false;
        modal.hide();
        modal.dispose();
        modalEl.parentNode.remove();
    }
}

export {closeModal};