jQuery(document).on('click', '.bt-download', function () {

    var file = jQuery(this).data('file');
    var path = jQuery('#iframe_down').data('path');
    jQuery('#iframe_down').empty();

    var iframe = jQuery("<iframe/>").attr({
        src: path + '/plugins/content/fichiers/download.php?arquivo=' + file,
        style: "visibility:hidden;display:none"
    }).appendTo('#iframe_down');


});


jQuery(document).on('click', '.bt-preview', function () {
    var file = jQuery(this).data('file');
    jQuery.fancybox.open(file);
});
jQuery(document).on('click', '.bt-preview-pdf', function () {
    var file = jQuery(this).data('file');
    jQuery.fancybox.open({
        href: file,
        type: 'iframe',
        width: 800,
        height: 600,
        padding: 5
    });
});
