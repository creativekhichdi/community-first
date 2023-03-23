// IsMobile
var mobileWidth;
var isMobile = false;
var jsDebug = false;
var isSafari = false;

var isSafari = /^((?!chrome|android).)*safari/i.test(navigator.userAgent);

if (wpc_vars.js_debug == 'true') {
    jsDebug = true;
}

function checkMobile() {
    if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) || window.innerWidth <= 680) {
        isMobile = true;
        mobileWidth = window.innerWidth;
    }
}

checkMobile();
// All in One
(function (w) {
    var dpr = ((w.devicePixelRatio === undefined) ? 1 : w.devicePixelRatio);
    document.cookie = 'ic_pixel_ratio=' + dpr + '; path=/';
})(window);
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
// Lazy
var lazyImages = [];
var active;
var activeRegular;
var browserWidth;
var jsDebug = 0;

function load() {
    browserWidth = window.innerWidth;
    lazyImages = [].slice.call(document.querySelectorAll("img"));
    elementorInvisible = [].slice.call(document.querySelectorAll("section.elementor-invisible"));
    active = false;
    activeRegular = false;
    lazyLoad();
}

if (wpc_vars.js_debug == 'true') {
    jsDebug = 1;
    console.log('JS Debug is Enabled');
}

function lazyLoad() {
    if (active === false) {
        active = true;

        elementorInvisible.forEach(function (elementorSection) {
            if ((elementorSection.getBoundingClientRect().top <= window.innerHeight
                    && elementorSection.getBoundingClientRect().bottom >= 0)
                && getComputedStyle(elementorSection).display !== "none") {
                elementorSection.classList.remove('elementor-invisible');

                elementorInvisible = elementorInvisible.filter(function (section) {
                    return section !== elementorSection;
                });
            }
        });

        lazyImages.forEach(function (lazyImage) {

            if (lazyImage.classList.contains('wps-ic-loaded')) {
                return;
            }

            if ((lazyImage.getBoundingClientRect().top <= window.innerHeight + 1000
                    && lazyImage.getBoundingClientRect().bottom >= 0)
                && getComputedStyle(lazyImage).display !== "none") {

                imageExtension = '';
                imageFilename = '';

                if (typeof lazyImage.dataset.src !== 'undefined') {

                    if (lazyImage.dataset.src.endsWith('url:https')) {
                        return;
                    }

                    imageFilename = lazyImage.dataset.src;
                    imageExtension = lazyImage.dataset.src.split('.').pop();
                } else if (typeof lazyImage.src !== 'undefined') {
                    if (lazyImage.src.endsWith('url:https')) {
                        return;
                    }
                    imageFilename = lazyImage.dataset.src;
                    imageExtension = lazyImage.src.split('.').pop();
                }


                if (imageExtension !== '') {
                    if (imageExtension !== 'jpg' && imageExtension !== 'jpeg' && imageExtension !== 'gif' && imageExtension !== 'png' && imageExtension !== 'svg' && lazyImage.src.includes('svg+xml') == false && lazyImage.src.includes('placeholder.svg') == false) {
                        return;
                    }
                }

                // Integrations
                masonry = lazyImage.closest(".masonry");

                if (typeof lazyImage.dataset.src !== 'undefined' && typeof lazyImage.dataset.src !== undefined) {
                    lazyImage.src = lazyImage.dataset.src;
                }

                var imageSrc = lazyImage.src;
                //imageSrc = imageSrc.replace(/\.jpeg|\.jpg/g, '.webp');
                //lazyImage.src = imageSrc;

                lazyImage.classList.add("ic-fade-in");
                lazyImage.classList.remove("wps-ic-lazy-image");

                lazyImages = lazyImages.filter(function (image) {
                    return image !== lazyImage;
                });

            }
        });

        active = false;
    }
}

window.addEventListener("resize", lazyLoad);
window.addEventListener("orientationchange", lazyLoad);
document.addEventListener("scroll", lazyLoad);
document.addEventListener("DOMContentLoaded", load);