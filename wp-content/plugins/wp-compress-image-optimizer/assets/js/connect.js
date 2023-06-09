jQuery(document).ready(function ($) {

    var swalFunc = function open_connect_popup() {
        Swal.fire({
            title: '',
            showClass: {
                popup: 'in'
            },
            html: jQuery('.wps-ic-connect-form').html(),
            width: 700,
            position: 'center',
            customClass: {
                container: 'in',
                popup: 'wps-ic-connect-popup'
            }, //customClass:'wps-ic-connect-popup',
            showCloseButton: false,
            showCancelButton: false,
            showConfirmButton: false,
            allowOutsideClick: false,
            onOpen: function () {

                $('.wps-ic-connect-retry').on('click', function (e) {
                    e.preventDefault();
                    swalFunc();
                    return false;
                });

                var swal_container = $('.swal2-container');
                var form = $('#wps-ic-connect-form', swal_container);
                $('#wps-ic-connect-form', swal_container).on('submit', function (e) {
                    e.preventDefault();

                    var form_container = $('.wps-ic-form-container', swal_container);
                    var success_message = $('.wps-ic-success-message-container', swal_container);
                    var error_message_container = $('.wps-ic-error-message-container', swal_container);
                    var error_message_text = $('.wps-ic-error-message-container-text', swal_container);
                    var already_connected = $('.wps-ic-error-already-connected', swal_container);
                    var success_message_text = $('.wps-ic-success-message-container-text', swal_container);
                    var success_message_choice_text = $('.wps-ic-success-message-choice-container-text', swal_container);
                    var success_message_buttons = $('.wps-ic-success-message-choice-container-text a', swal_container);
                    var finishing = $('.wps-ic-finishing-container', swal_container);

                    var loader = $('.wps-ic-loading-container', swal_container);
                    var tests = $('.wps-ic-tests-container', swal_container);
                    var init = $('.wps-ic-init-container', swal_container);

                    $(already_connected).hide();
                    $(error_message_text).hide();
                    $(success_message_text).hide();
                    $(error_message_container).hide();
                    $(init, swal_container).hide();
                    $(form_container).hide();
                    $(loader).show();
                    $(tests).hide();

                    var apikey = $('input[name="apikey"]', form_container).val();

                    $.post(ajaxurl, {
                        action: 'wps_ic_live_connect',
                        apikey: apikey
                    }, function (response) {
                        if (response.success) {
                            // Connect
                            $('.wps-ic-connect-inner').addClass('padded');
                            $(success_message).show();
                            $(success_message_choice_text).show();

                            /**
                             * Figure out what optimization mode is enabled
                             */
                            if (response.data.liveMode == '0') {
                                var liveBtn = $('.wpc-live-btn', '.wpc-select-mode-containers');
                                var liveBtnText = $('.wpc-live-btn-text', liveBtn);
                                $(liveBtn).addClass('wpc-disabled-option');
                                $(liveBtnText).addClass('wpc-disabled-button');
                                $(liveBtnText).html('Disabled');
                            }

                            if (response.data.localMode == '0') {
                                var localBtn = $('.wpc-local-btn', '.wpc-select-mode-containers');
                                var localBtnText = $('.wpc-local-btn-text', localBtn);
                                $(localBtn).addClass('wpc-disabled-option');
                                $(localBtnText).addClass('wpc-disabled-button');
                                $(localBtnText).html('Disabled');
                            }

                            $('.wpc-live-btn', success_message_choice_text).on('click', function (e) {
                                e.preventDefault();

                                var btn = $(this);
                                if ($(btn).hasClass('wpc-disabled-option')) {
                                    return false;
                                }

                                $.post(ajaxurl, {
                                    action: 'wpc_ic_set_mode',
                                    value: 'recommended'
                                }, function (response) {
                                    $(loader).hide();
                                    $(tests).hide();
                                    setTimeout(function (){
                                        window.location.reload();
                                    },1000);
                                });
                            });

                            $('.wpc-local-btn', success_message_choice_text).on('click', function (e) {
                                e.preventDefault();

                                var btn = $(this);
                                if ($(btn).hasClass('wpc-disabled-option')) {
                                    return false;
                                }

                                $.post(ajaxurl, {
                                    action: 'wpc_ic_set_mode',
                                    value: 'safe'
                                }, function (response) {
                                    $(loader).hide();
                                    $(tests).hide();
                                    setTimeout(function (){
                                        window.location.reload();
                                    },1000);
                                });
                            });


                            $(success_message_buttons).on('click', function (e) {
                                e.preventDefault();

                                var btn = $(this);
                                if ($(btn).hasClass('wpc-disabled-option')) {
                                    return false;
                                }

                                $(finishing).show();
                                $(success_message).hide();
                                $(success_message_choice_text).hide();

                                setTimeout(function () {
                                    $(loader).hide();
                                    $(tests).hide();
                                }, 2000);
                            });

                            $(loader).hide();
                            $(tests).hide();
                        } else {
                            // Not OK
                            // msg = 'Your api key does not match our records.';
                            //                 title = 'API Key Validation';

                            if (response.data.msg == 'site-already-connected') {
                                $(already_connected).show();
                                $(error_message_container).show();
                                $(error_message_text).hide();
                                $(success_message_choice_text).hide();
                                $(success_message_text).hide();
                                $(success_message).hide();
                                $(loader).hide();
                                $(tests).hide();
                            } else {
                                $(error_message_text).show();
                                $(error_message_container).show();
                                $(success_message_text).hide();
                                $(success_message_choice_text).hide();
                                $(success_message).hide();
                                $(loader).hide();
                                $(tests).hide();
                            }

                            // $('.wps-ic-connect-retry', swal_container).bind('click');

                        }
                    });

                    return false;
                })

            }
        });
    }

    swalFunc();


});