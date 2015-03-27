/*global jQuery, document, window, smartview_vars*/
/*jslint newcap: true*/
jQuery(document).ready(function ($) {
    'use strict';

    var newModal, formURL;
    
    if (isMobile.any) {
        $('.smartview').click(function (e) {
            e.preventDefault();

            window.location.href = $(this).attr('href');
        });

        $('.smartview-back').click(function () {
            parent.history.back();
            return false;
        });
    } else {
        $('.smartview').colorbox({
            iframe: true,
            width: '80%',
            height: '80%',
            fixed: true,
            transition: smartview_vars.modal_effect,
            speed: Number(smartview_vars.modal_speed)
        });
    }
});
