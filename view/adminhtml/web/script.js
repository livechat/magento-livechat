require(['jquery'], function($){
    function LiveChatCreateNewAccount() {
        var login = $('#lc_new_account_email').val();
        var password = $('#lc_new_account_password').val();
        var isError = false;
        hideError('lc_new_account_email');
        hideError('lc_new_account_password');

        if (!login.length) {
            showError('lc_new_account_email', 'Please enter your email address.');
            isError = true;
        }
        
        if (/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,6}$/i.test(login) === false) {
            showError('lc_new_account_email', 'Please enter a valid email address.');
            isError = true;
        }

        if (password.length < 6) {
            showError('lc_new_account_password', 'Password must be at least 6 characters.');
            isError = true;
        }

        if (!password.length) {
            showError('lc_new_account_password', 'Please choose a password.');
            isError = true;
        }
        
        if (isError) {
            return;
        }
        
        var name = login.split("@")[0].split('+')[0];

        $.ajax({
            url: 'https://api.livechatinc.com/v2/license/',
            data: {
                name: name,
                email: login,
                password: password,
                promo_code: 'magento2',
                timezone: 'utc'
            },
            type: "POST",
            dataType: 'json',
            cache: false,
            showLoader: true,
            beforeSend: function (xhr) {
                $('#lc_create_new_account_button').attr('disabled', 'disabled');
            },
            success: function (data, status, error) {
                if (data.error) {
                    showError('lc_new_account_password', data.error);
                } else {
                    $('#lc_block_config_account_license_id').val(data.license);
                    $('#lc_block_config_account_license_email').val(login);
                    $('#save').click();
                }
            },
            error: function (data, status, error) {
                var response = jQuery.parseJSON(data.responseText);
                if (response.error) {
                    if ('Agent already exists!' === response.error) {
                        showError('lc_new_account_email', 'This email is already taken. Please try another or connect an existing account.');
                    } else {
                        showError('lc_new_account_password', response.error);
                    }
                } else if (response.errors) {
                    showError('lc_new_account_password', response.errors[0]);
                } else {
                    showError('lc_new_account_password', 'Something went wrong. Please try again.');
                }
            },
            complete: function (jqXHR, textStatus ) {
                $('#lc_create_new_account_button').removeAttr('disabled');
            }
        });
    }

    function LiveChatExistingAccountForm() {
        $('tr.lc_new_account').hide();
        $('tr.lc_use_existing_account').show();
        $('span.lc_new_account').hide();
        $('span.lc_use_existing_account').show();
    }
    
    function LiveChatNewAccountForm() {
        $('tr.lc_use_existing_account').hide();
        $('tr.lc_new_account').show();
        $('span.lc_use_existing_account').hide();
        $('span.lc_new_account').show();
    }

    function LiveChatForm() {
        var clickedElement = $('#lc_change_account');
        if ($('#lc_form').hasClass('hidden')) {
            clickedElement.html('Cancel');
            $('#lc_form').removeClass('hidden');
        } else {
            clickedElement.html('Connect a different account');
            $('#lc_form').addClass('hidden');
        }
    }

    function LiveChatConnect() {
        var login = $('#lc_existing_account').val();

        if (!login.length) {
            showError('lc_existing_account', 'Please enter your email address.');
            return;
        }

        $.ajax({
            url: 'https://api.livechatinc.com/licence/operator/' + login + '?callback=?',
            data: {},
            type: "GET",
            dataType: 'jsonp',
            cache: false,
            showLoader: true,
            beforeSend: function (xhr) {
                hideError('lc_existing_account');
                $('#lc_use_existing_account_button').attr('disabled', 'disabled');
            },
            success: function (data, status, error) {
                if (data.error) {
                    showError('lc_existing_account', 'Are you sure you entered a correct email address?');
                } else if (data.number) {
                    $('#lc_block_config_account_license_id').val(data.number);
                    $('#lc_block_config_account_license_email').val(login);
                    $('#save').click();
                }
            },
            error: function (data, status, error) {
                showError('lc_existing_account', 'Something went wrong. Please try again.');
            },
            complete: function (jqXHR, textStatus ) {
                $('#lc_use_existing_account_button').removeAttr('disabled');
            }
        });
    }

    function showError(inputId, message) {
        $('#' + inputId).addClass('mage-error');
        $('#' + inputId + '-error').text(message).removeClass('lc-hidden').addClass('lc-show');
    }
    
    function hideError(inputId) {
        $('#' + inputId).removeClass('mage-error');
        $('#' + inputId + '-error').addClass('lc-hidden').removeClass('lc-show');
    }
    
    function checkAccount(licenseId) {
        var statusContainer = $('#livechat-account-status');
        $.ajax({
            url: 'https://api.livechatinc.com/v2/license/' + licenseId + '?callback=?',
            type: "GET",
            dataType: 'jsonp',
            cache: false,
            beforeSend: function (xhr) {
                
            },
            success: function (data, status, error) {
                if (data.error) {
                    //
                    statusContainer.text('Something went wrong, please reload page.')
                } else {
                    if (true === data.license_active) {
                        statusContainer.text('Active.')
                    } else {
                        statusContainer.text('Inactive. Please login to LiveChat and subscribe')
                    }
                }
            },
            error: function (data, status, error) {
                statusContainer.text('Something went wrong, please reload page.')
            }
        });
    }

    function disconnect() {
        $('#lc_block_config_account_license_id').val(null);
        $('#lc_block_config_account_license_email').val(null);
        $('#save').click();
    }

    window.LiveChatConnect = LiveChatConnect;
    window.LiveChatCreateNewAccount = LiveChatCreateNewAccount;
    window.LiveChatForm = LiveChatForm;
    window.LiveChatExistingAccountForm = LiveChatExistingAccountForm;
    window.LiveChatNewAccountForm = LiveChatNewAccountForm;
    window.LiveChatDisconnect = disconnect;
    
    if ($('#livechat-account-status').length > 0) {
        checkAccount($('#livechat-account-status').attr('data-license-id'));
    }
});
