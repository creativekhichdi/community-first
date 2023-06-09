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
// OK
function SetupNewApiURL(newApiURL, imgWidth, imageElement) {
    if (imgWidth > 0 && !imageElement.classList.contains('wpc-excluded-adaptive')) {
        newApiURL = newApiURL.replace(/w:(\d{1,5})/g, 'w:' + imgWidth);
    }

    if ((window.devicePixelRatio >= 2 && wpc_vars.retina_enabled == 'true') || wpc_vars.force_retina == 'true') {
        newApiURL = newApiURL.replace(/r:0/g, 'r:1');

        if (jsDebug) {
            console.log('Retina set to True');
            console.log('DevicePixelRation ' + window.devicePixelRatio);
        }

    } else {
        newApiURL = newApiURL.replace(/r:1/g, 'r:0');

        if (jsDebug) {
            console.log('Retina set to False');
            console.log('DevicePixelRation ' + window.devicePixelRatio);
        }
    }

    if (wpc_vars.webp_enabled == 'true' && isSafari == false) {
        if (!imageElement.classList.contains('wpc-excluded-webp')) {
            newApiURL = newApiURL.replace(/wp:0/g, 'wp:1');
        }

        if (jsDebug) {
            console.log('WebP set to True');
        }

    } else {
        newApiURL = newApiURL.replace(/wp:1/g, 'wp:0');

        if (jsDebug) {
            console.log('WebP set to False');
        }

    }

    if (wpc_vars.exif_enabled == 'true') {
        newApiURL = newApiURL.replace(/e:0/g, 'e:1');
    } else {
        newApiURL = newApiURL.replace(/\/e:1/g, '');
        newApiURL = newApiURL.replace(/\/e:0/g, '');
    }

    if (isMobile) {
        newApiURL = getSrcset(newApiURL.split(","), mobileWidth, imageElement);
    }

    return newApiURL;
}
// OK
function srcSetUpdateWidth(srcSetUrl, imageWidth, imageElement) {

    if (imageElement.classList.contains('wpc-excluded-adaptive')) {
        imageWidth = 1;
    }

    var srcSetWidth = srcSetUrl.split(' ').pop();
    if (srcSetWidth.endsWith('w')) {
        // Remove w from width string
        var Width = srcSetWidth.slice(0, -1);
        if (parseInt(Width) <= 5) {
            Width = 1;
        }
        srcSetUrl = srcSetUrl.replace(/w:(\d{1,5})/g, 'w:' + imageWidth);
    } else if (srcSetWidth.endsWith('x')) {
        var Width = srcSetWidth.slice(0, -1);
        if (parseInt(Width) <= 3) {
            Width = 1;
        }
        srcSetUrl = srcSetUrl.replace(/w:(\d{1,5})/g, 'w:' + imageWidth);
    }
    return srcSetUrl;
}
// OK
function getSrcset(sourceArray, imageWidth, imageElement) {
    var changedSrcset = '';

    sourceArray.forEach(function (imageSource) {

        if (jsDebug) {
            console.log('Image src part from array');
            console.log(imageSource);
        }

        newApiURL = srcSetUpdateWidth(imageSource.trimStart(), imageWidth, imageElement);
        changedSrcset += newApiURL + ",";
    });

    return changedSrcset.slice(0, -1); // Remove last comma
}
// OK
function listHas(list, keyword) {
    var found = false;
    list.forEach(function (className) {
        if (className.includes(keyword)) {
            found = true;
        }
    });


    if (found) {
        return true;
    } else {
        return false;
    }

}
// OK
function removeElementorInvisible() {
    var elementorInvisible = document.querySelectorAll(".elementor-invisible");

    for (i = 0; i < elementorInvisible.length; ++i) {
        elementorSection = elementorInvisible[i];
        if ((elementorSection.getBoundingClientRect().top <= window.innerHeight && elementorSection.getBoundingClientRect().bottom >= 0) && getComputedStyle(elementorSection).display !== "none") {
            elementorSection.classList.remove('elementor-invisible');
        }
    }
}
function runLazy() {
    var lazyImages = [].slice.call(document.querySelectorAll("img[data-wpc-loaded='true']"));
    var LazyIFrames = [].slice.call(document.querySelectorAll("iframe.wpc-iframe-delay"));

    if ("IntersectionObserver" in window) {
        var lazyIframesObserver = new IntersectionObserver(function (entries, observer) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    var lazyIframe = entry.target;

                    var src = lazyIframe.dataset.src
                    lazyIframe.src = src;

                    lazyIframesObserver.unobserve(lazyIframe);
                }
            });
        });

        var lazyImageObserver = new IntersectionObserver(function (entries, observer) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    var lazyImage = entry.target;

                    // Integrations
                    masonry = lazyImage.closest(".masonry");
                    owlSlider = lazyImage.closest(".owl-carousel");
                    SlickSlider = lazyImage.closest(".slick-slider");
                    SlickList = lazyImage.closest(".slick-list");
                    slides = lazyImage.closest(".slides");

                    if (jsDebug) {
                        console.log(masonry);
                        console.log(owlSlider);
                        console.log(SlickSlider);
                        console.log(SlickList);
                        console.log(slides);
                    }

                    /**
                     * Is SlickSlider/List?
                     */
                    if (SlickSlider || SlickList || slides || owlSlider || masonry) {
                        if (typeof lazyImage.dataset.src !== 'undefined' && lazyImage.dataset.src != '') {
                            newApiURL = lazyImage.dataset.src;
                        } else {
                            newApiURL = lazyImage.src;
                        }

                        newApiURL = newApiURL.replace(/w:(\d{1,5})/g, 'w:1');
                        lazyImage.src = newApiURL;
                        lazyImage.classList.add("ic-fade-in");
                        lazyImage.classList.add("wpc-remove-lazy");
                        lazyImage.classList.remove("wps-ic-lazy-image");
                        return;
                    }


                    if (wpc_vars.adaptive_enabled == 'false' || lazyImage.classList.toString().includes('logo')) {
                        imgWidth = 1;
                    } else {
                        imageStyle = window.getComputedStyle(lazyImage);

                        imgWidth = Math.round(parseInt(imageStyle.width));

                        if (typeof imgWidth == 'undefined' || !imgWidth || imgWidth == 0 || isNaN(imgWidth)) {
                            imgWidth = 1;
                        }

                        if (listHas(lazyImage.classList, 'slide')) {
                            imgWidth = 1;
                        }
                    }

                    /**
                     * Setup Image SRC only if srcset is empty
                     */
                    if ((typeof lazyImage.dataset.src !== 'undefined' && lazyImage.dataset.src != '')) {
                        newApiURL = lazyImage.dataset.src;

                        newApiURL = SetupNewApiURL(newApiURL, imgWidth, lazyImage);

                        lazyImage.src = newApiURL;
                        if (typeof lazyImage.dataset.srcset !== 'undefined' && lazyImage.dataset.src != '') {
                            lazyImage.srcset = lazyImage.dataset.srcset;
                        }
                    } else if (typeof lazyImage.src !== 'undefined' && lazyImage.src != '') {
                        newApiURL = lazyImage.src;

                        newApiURL = SetupNewApiURL(newApiURL, imgWidth, lazyImage);

                        lazyImage.src = newApiURL;
                        if (typeof lazyImage.dataset.srcset !== 'undefined' && lazyImage.dataset.src != '') {
                            lazyImage.srcset = lazyImage.dataset.srcset;
                        }
                    }

                    lazyImage.classList.add("ic-fade-in");
                    lazyImage.classList.remove("wps-ic-lazy-image");

                    //lazyImage.removeAttribute('data-src'); => Had issues with Woo Zoom
                    lazyImage.removeAttribute('data-srcset');

                    srcSetAPI = '';
                    if (typeof lazyImage.srcset !== 'undefined' && lazyImage.srcset != '') {
                        srcSetAPI = newApiURL = lazyImage.srcset;

                        if (jsDebug) {
                            console.log('Image has srcset');
                            console.log(lazyImage.srcset);
                            console.log(newApiURL);
                        }

                        newApiURL = SetupNewApiURL(newApiURL, imgWidth, lazyImage);

                        lazyImage.srcset = newApiURL;
                    } else if (typeof lazyImage.dataset.srcset !== 'undefined' && lazyImage.dataset.srcset != '') {
                        srcSetAPI = newApiURL = lazyImage.dataset.srcset;
                        if (jsDebug) {
                            console.log('Image does not have srcset');
                            console.log(newApiURL);
                        }

                        newApiURL = SetupNewApiURL(newApiURL, imgWidth, lazyImage);

                        lazyImage.srcset = newApiURL;
                    }

                    //lazyImage.classList.remove("lazy");
                    lazyImageObserver.unobserve(lazyImage);
                }
            });
        });

        lazyImages.forEach(function (lazyImage) {
            lazyImageObserver.observe(lazyImage);
        });

        LazyIFrames.forEach(function (lazyIframes) {
            lazyIframesObserver.observe(lazyIframes);
        });
    } else {
        // Possibly fall back to event handlers here
    }
}

document.addEventListener("DOMContentLoaded", function () {
    removeElementorInvisible();
    runLazy();
});

window.addEventListener("resize", removeElementorInvisible);
window.addEventListener("orientationchange", removeElementorInvisible);
document.addEventListener("scroll", removeElementorInvisible);

if ('undefined' !== typeof jQuery) {
    jQuery(document).on('elementor/popup/show', function () {
        runLazy();
    });
}