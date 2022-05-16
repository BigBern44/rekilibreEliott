require('summernote/dist/summernote.css');
require('summernote');
require('summernote/dist/lang/summernote-fr-FR.min.js');
require('../css/home.css');

$(document).ready(function () {

    for (var i = 0; i < 8; i++) {
        $('#summernote' + i).summernote({
            lang: 'fr-FR',
            height: 600
        });

        $('#btn-edit' + i).click(function () {
            $('#layout-summmernote' + $(this).attr("tag")).show();
            $('#layout-article' + $(this).attr("tag")).hide();
        });

        $('#btn-cancel' + i).click(function () {
            $('#layout-summmernote' + $(this).attr("tag")).hide();
            $('#layout-article' + $(this).attr("tag")).show();
        });

        $('#vignette' + i).click(function () {
            for (var j = 0; j < 8; j++) {
                if($(this).attr("tag")==j){
                    $('#layout-article-editor' + j).show();
                }else{
                    $('#layout-article-editor' + j).hide();
                }
            }
        });
    }
});