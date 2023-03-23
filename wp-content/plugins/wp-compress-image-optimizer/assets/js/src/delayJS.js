// Delay JS Script
jQuery(document).ready(function ($) {

    var all_iframe = $('iframe.wpc-iframe-delay');
    var all_scripts = $('[type="wpc-delay-script"]');
    var all_styles = $('[rel="wpc-stylesheet"]');
    var mobile_styles = $('[rel="wpc-mobile-stylesheet"]');

    var mobileStyles = [];
    var styles = [];
    var iframes = [];

    $(mobile_styles).each(function (index, element) {
        mobileStyles.push(element);
    });

    $(all_styles).each(function (index, element) {
        styles.push(element);
    });

    $(all_iframe).each(function (index, element) {
        iframes.push(element);
    });


    var needs_preload = 0;
    $(all_scripts).each(function (index, element) {
        if (element.src.length > 0) {
            needs_preload++;
            element.inline = false;
        } else {
            element.inline = true;
        }
    });

    $(document).on('keydown mousedown mousemove touchmove touchstart touchend wheel visibilitychange load', preload);
    var scrollTop = $(window).scrollTop();
    if (scrollTop > 40) {
        preload();
    }

    function preload() {

        $(document).off('keydown mousedown mousemove touchmove touchstart touchend wheel visibilitychange load');

        var i = 0;
        $(all_scripts).each(function (index, element) {
            if (element.inline === false) {
                var preload_link = document.createElement('link');
                $(preload_link).attr('rel', 'preload');
                $(preload_link).attr('as', 'script');
                $(preload_link).attr('href', $(element).attr('src'));

                $(preload_link).on('load error', function () {
                    i++;
                    if (i == needs_preload) {
                        //everything has been loaded
                        run();
                    }
                });

                document.head.appendChild(preload_link);
            }
        });

        $(styles).each(function (index, element) {
            $(element).attr('rel', 'stylesheet');
            $(element).attr('type', 'text/css');
        });

        styles = [];

        $(mobileStyles).each(function (index, element) {
            $(element).attr('rel', 'stylesheet');
            $(element).attr('type', 'text/css');
        });

        mobileStyles = [];

        // $(iframes).each(function (index, element) {
        //     var src = $(element).attr('data-src');
        //     $(element).attr('src', src);
        // });
        //
        // iframes = [];

    }

    function run() {
        $(all_scripts).each(function (index, element) {
            if (element.inline === false) {
                var new_element = document.createElement('script');
                $(new_element).attr('src', $(element).attr('src'));
                document.head.appendChild(new_element);
            } else {
                try {
                    $(element).attr('type', 'text/javascript');
                    $.globalEval(element.text);
                } catch(element) {
                    console.log('Delay Error:' + element)
                }
            }
        });

    }

});