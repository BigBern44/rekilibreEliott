require('summernote/dist/summernote.css');
require('summernote');
require('summernote/dist/lang/summernote-fr-FR.min.js');
require('../css/news.css');

$(document).ready(function () {
    $('#summernote').summernote({
        lang: 'fr-FR',
        tabsize: 2,
        height: 500
    });

    $('.summernote-modify').summernote({
        lang: 'fr-FR',
        tabsize: 2,
        height: 300
    });

    $('#btn-add-news').click(function () {
        $('#layout-form-news').show();
    });
    
    $('#btn-close-form-news').click(function () {
        $('#layout-form-news').hide();
    });

    $('.btn-edit').click(function () {
        $('#layout-summmernote'+this.getAttribute('idNews')).show();
        $('#layout-description'+this.getAttribute('idNews')).hide();
    });

    $('.btn-cancel').click(function () {
        $('#layout-summmernote'+this.getAttribute('idNews')).hide();
        $('#layout-description'+this.getAttribute('idNews')).show();
    });
});