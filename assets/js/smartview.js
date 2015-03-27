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
            width: smartview_vars.modal_width,
            height: smartview_vars.modal_height,
            fixed: true,
            transition: smartview_vars.modal_effect,
            speed: Number(smartview_vars.modal_speed),
            title: smartview_vars.modal_title,
            opacity: Number(smartview_vars.modal_opacity),
        });

        $('.smartview-error').colorbox({
            html: smartview_vars.modal_error,
            fixed: true,
            transition: smartview_vars.modal_effect,
            speed: Number(smartview_vars.modal_speed),
            opacity: Number(smartview_vars.modal_opacity),
        });
    }
});
