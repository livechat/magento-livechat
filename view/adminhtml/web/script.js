require(['jquery'], function ($) {
	
	var save_props_url = $('#save-props-url').html();
	var save_license_url = $('#save-license-url').html();
	var reset_license_url = $('#reset-license-url').html();
	var get_props_url = $('#get-props-url').html();

	var logoutLiveChat = function () {
		sendMessage('logout');
	};

	var login_with_livechat = document.getElementById('login-with-livechat');

	$('#lc_block_config_custom_params').on('change', function () {
		var cart_products = $('#lc_block_config_custom_params_cart_products').val()
		var total_cart_value = $('#lc_block_config_custom_params_total_cart_value').val();
		var total_orders_count = $('#lc_block_config_custom_params_total_orders_count').val();
		var last_order_details = $('#lc_block_config_custom_params_last_order_details').val();
		
		var param = {
		cart_products : cart_products,	
		total_cart_value : total_cart_value,	
		total_orders_count : total_orders_count,	
		last_order_details : last_order_details	
		};
		
		$.ajax({
			showLoader: true,
			url: save_props_url,
			data: param,
			type: "POST",
			dataType: 'json'
		});
	});

	$('#reset_settings').on('click', function () {
		
		var param = {
			param : 'param'		
		};
		
		$.ajax({
			showLoader: true,
			type: 'POST',
			dataType: 'json',
			data: param,
			url: reset_license_url
		}).done(function () {
			logoutLiveChat();

			$('#login_panel').show();
			$('#admin_panel').hide();
			$('iframe#login-with-livechat').removeClass('hidden');
			$('.progress-button').addClass('hidden');
		});

	});

	var sendMessage = function (msg) {
		login_with_livechat.contentWindow.postMessage(msg, '*');
	};

	var logoutLiveChat = function () {
		sendMessage('logout');
	};

	function receiveMessage(event) {
		try {
			var livechatMessage = JSON.parse(event.data);
		}
		catch(err) {
			console.log(JSON.stringify(err));
		}
		
		if (livechatMessage.type === 'logged-in' && livechatMessage.eventTrigger === 'click') {

			$('#login_panel').hide();
			$('#admin_panel').show();
			$('iframe#login-with-livechat').addClass('hidden');
			$('.progress-button').removeClass('hidden');
			
			$.ajax({
				showLoader: true,
				type: 'POST',
				dataType: 'json',
				url: save_license_url,
				data: {
					email: livechatMessage.email,
					license: livechatMessage.license
				}
			}).done(function(){
				$.ajax({
				showLoader: true,
				type: 'GET',
				url: get_props_url
				}).done(function(result){
					var license_settings = JSON.parse(result.license_settings);
				
					document.getElementById('livechat_login').innerHTML = 
							license_settings.license_email;
					document.getElementById('lc_block_config_custom_params_cart_products')
							.value = Number(license_settings.cart_products);
					document.getElementById('lc_block_config_custom_params_total_cart_value')
							.value = Number(license_settings.total_cart_value);
					document.getElementById('lc_block_config_custom_params_total_orders_count')
							.value = Number(license_settings.total_orders_count);
					document.getElementById('lc_block_config_custom_params_last_order_details')
							.value = Number(license_settings.last_order_details);
				});
			});
		}
	}

	window.addEventListener("message", receiveMessage, false);

});
