/***v.5***/

function pb_form_click_trigger(that) {
	var $that = $(that);

	$that.local_data = {
		send_data : {
			pb_send_mode : 'pb_ajax_get_form',
			pb_form_id : $('.pb_form_id',$that.parent()).val()
		}
	};

	$.ajax({
		url: window.location.href,
		type: 'POST',
		data: $that.local_data.send_data,

		beforeSend: function(){

		},

		success: function(data){

			var $pop_window_class_name = 'pop_wrapper_' + $that.local_data.send_data.pb_form_id,
				$backcall_wrapper = $('<div></div>').appendTo('body').addClass('pop_backcall_wrapper_v5');

			$backcall_wrapper.height($(document).height());
			$(data).appendTo('body').addClass($pop_window_class_name);

			var $pop_window = $('.' + $pop_window_class_name),
//				window_height = parseInt($(window).height()),
				window_height = parseInt(Math.max(document.documentElement.clientWidth, window.innerWidth || 0)),
				offset_x = Math.round((window_height/2 - parseInt($pop_window.height()))/2),
				$text_inputs = $('input,textarea',$pop_window),
				$pb_form_rules = $('.pb_form_rules',$pop_window),
				$form_v5_trigger = $('.form_v5_trigger',$pop_window);

console.log($(window).height());

			$pop_window
				.appendTo($backcall_wrapper)
				.css('top',offset_x)
				.animate({
					opacity:1
				}, 200)
				.click(function(e){
					e.stopPropagation();
				});

			$backcall_wrapper.click(function () {
				$backcall_wrapper.remove();
			});

			$pop_window.pb_pop_close = function () {
				$backcall_wrapper.remove();
			};

			$pop_window.pb_send_form_init = function () {

				var $select_list = $('.select_list',$pop_window).hide(),
					$select_value = $('.select_value',$pop_window),
					current_value = $('.select_list_value:first',$select_list).html(),
					current_email = $('.select_list_value:first',$select_list).attr('rel'),
					$select_list_values = $('.select_list_value',$select_list),
					$email_to_iblock = $('.email_to_iblock',$pop_window);

				$select_value
					.html(current_value)
					.click(function(){
						if($select_list.css('display') !== 'none'){
							$select_list.hide();
						}else{
							$select_list.show();
						}
					});

				$email_to_iblock.val(current_email);

				$select_list_values.click(function () {
					var $current_value = $(this);

					current_value = $current_value.html();
					current_email = $current_value.attr('rel');

					$select_value.html(current_value);
					$email_to_iblock.val(current_email);
					$select_list.hide();
				});

				$form_v5_trigger
					.click(function(){
						$pop_window.send_msg_ajax();
						return false;
					});

				$('.pop_close_btn',$pop_window)
					.click(function(){
						$pop_window.pb_pop_close();
						return false;
					});

			};

			$pop_window.send_msg_ajax = function () {

				var form_msg_class = 'form_msg';

				$pop_window.local_data = {
					sessid : $('#pb_bform_sessid').val(),
					pb_send_mode : 'pb_ajax_backcall'
				};

				$text_inputs.each(function(){
					var $that = $(this);

					if($that.attr('type') == 'hidden') return true;

					$that.placeholder = $that.attr('placeholder');

					if(typeof $that.placeholder === 'undefined') return true;

					$that.currentvalue = $that.val();

					if($that.currentvalue == $that.placeholder){
						$that.val('');
					}
				});

				$text_inputs.each(function(){
					var $that = $(this);

					if($that.hasClass('pb_form_rules')) return true;

					$pop_window.local_data[$that.attr('class')] = $that.val();
				});

				if(typeof $pb_form_rules.attr('class') !== 'undefined'){
					$pop_window.local_data['pb_form_rules'] = $pb_form_rules.attr('checked') || '';
				}

				$.each($pop_window.local_data,function(k,v){
					$pop_window.local_data[k] = encodeURIComponent(v);
				});

				$.ajax({
					url: window.location.href,
					type: 'POST',
					dataType: 'json',
					data: $pop_window.local_data,

					beforeSend: function(){
						$('.form_msg:first',$pop_window).remove();
						$('input, textarea, .input_holder',$pop_window).removeClass('required');
						$form_v5_trigger.removeClass('required').addClass('locked');
					},

					success: function(data){

						if(typeof(data) !== 'undefined'){

							if(data.status == 1){
								form_msg_class += ' form_msg_error';
								$.each(data.fields,function(k,v){
									$('.' + v,$pop_window).parents('.input_holder').addClass('required');
								});
							}

							$($pop_window).prepend('<div class="' + form_msg_class + '" style="display:none;"><div class="form_msg_wrapper"><a class="close_btn" href="#">&times;</a>' + data.msg + '</div></div>');
							$('.form_msg',$pop_window).fadeIn(400).delay(3500).fadeOut(400);
							$('.form_msg .close_btn',$pop_window).click(function(){
								$('.form_msg',$pop_window).css('display','none');
								return false;
							});

							if(data.CAPTCHA != undefined){
								$('.captcha_sid',$pop_window).val(data.CAPTCHA);
								var c_img_path = $('.c_img',$pop_window).attr('src');
								c_img_path = c_img_path.split('=');
								$('.c_img',$pop_window).attr('src',c_img_path[0] + '=' + data.CAPTCHA);
								$('.captcha_word',$pop_window).val('');
							}

							$text_inputs.each(function(){
								var $that = $(this);

								if($that.attr('type') == 'hidden') return true;

								$that.placeholder = $that.attr('placeholder');
								$that.currentvalue = $that.val();

								if($that.currentvalue == ''){
									$that.val($that.placeholder);
								}
							});

							if(data.status == 0){
								window.setTimeout(function(){$pop_window.pb_pop_close()},1500);
							}

						}else{
							$($pop_window).prepend('<div class="form_msg error">Error</div>');
						}

						$form_v5_trigger.removeClass('locked');

					},

					error: function(){
						$form_v5_trigger.removeClass('locked');
						$($pop_window).prepend('<div class="form_msg error">Error</div>');
					}
				});

			};

			$text_inputs
				.focus(function(){
					$(this).parents('.input_holder').addClass('active');
				})
				.blur(function(){
					$(this).parents('.input_holder').removeClass('active');
				})
				.each(function(){
					var $that = $(this);

					if($that.attr('type') == 'hidden') return true;

					$that.placeholder = $that.attr('placeholder');

					if(typeof $that.placeholder === 'undefined') return true;

					$that.currentvalue = $that.val();

					if(typeof $that.placeholder !== 'undefined'){
						if($that.currentvalue != $that.placeholder){
							$that.val($that.placeholder);
						}
					}else{
						$that.attr('placeholder', $that.currentvalue);
					}

					$that
						.focus(function(){
							var $that = $(this);
							$that.placeholder = $that.attr('placeholder');
							$that.currentvalue = $that.val();

							if($that.currentvalue == $that.placeholder){
								$that.val('');
							}
						})
						.blur(function(){
							var $that = $(this);
							$that.placeholder = $that.attr('placeholder');
							$that.currentvalue = $that.val();

							if($that.currentvalue == ''){
								$that.val($that.placeholder);
							}
						});
				});

			$pop_window.pb_send_form_init();
		},

		error: function(){
			// $(oBxPbMsgTrigger).removeClass('locked');
		}
	});
	return false;
}
