require('summernote/dist/summernote.css');
require('summernote');
require('summernote/dist/lang/summernote-fr-FR.min.js');
require('../css/home.css');

$(document).ready(function () {
    
    $('#summernote').summernote({
        lang: 'fr-FR',
        height: 400
    });

    $('#btn-edit').click(function () {
        $('#layout-summmernote').show();
        $('#layout-description').hide();
    });

    $('#btn-cancel').click(function () {
        $('#layout-summmernote').hide();
        $('#layout-description').show();
    });
});