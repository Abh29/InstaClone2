import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    connect() {
        $('#post_form_uploadFile').on('change', function () {
            let self = this;
            preview_image(self);
        })
    }
}

function preview_image(el) {
    $('#post-preview-img').attr('src', URL.createObjectURL(el.files[0]));
}