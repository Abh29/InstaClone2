import { Controller } from '@hotwired/stimulus';
import axios from "axios";

export default class extends Controller {
    connect() {
        let self = this;

        $('#profile_edit_form_uploadFile').on('change', function () {
            let self = this;
            preview_image(self);
        });

        $('#subscribe-button').on('click', function () {
           let href = $(this).attr('href');
           let btn = this;

           axios.get(href).then(function (response){
               let html = $(btn).html();
               let count =  $('#followers-count strong').html();
               if (html === 'subscribe'){
                   html = 'unsubscribe';
                   count++;
               }
               else{
                   html = 'subscribe';
                   count--;
               }
               $('#followers-count strong').html(count);
               $(btn).html(html);
           }).catch(function (error){
               console.log(error.message);
           })
        });

    }
}

function preview_image(el) {
    $('#profile-preview-img').attr('src', URL.createObjectURL(el.files[0]));
}