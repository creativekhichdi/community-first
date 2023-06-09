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