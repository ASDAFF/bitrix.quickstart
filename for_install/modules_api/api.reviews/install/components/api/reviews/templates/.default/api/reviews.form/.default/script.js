/*!
 * $.fn.apiReviewsForm
 */
(function ($) {

	"use strict"; // Hide scope, no $ conflict

	var defaults = {};

	var methods = {

		init: function (params) {

			var options = $.extend(true, {}, defaults, params);

			if (!this.data('apiReviewsForm')) {

				this.data('apiReviewsForm', options);

				var modalId       = '#' + options.id;
				var review_form   = $('.api-reviews-form .api_form');
				var review_submit = $('.api-reviews-form .api-form-submit');
				var rating_label  = $('.api-reviews-form .api-star-rating-label');


				//Autoresize textarea
				review_form.find('[data-autoresize]').each(function () {
					var offset         = this.offsetHeight - this.clientHeight;
					var resizeTextarea = function (el) {
						$(el).css('height', 'auto').css('height', el.scrollHeight + offset);
					};
					$(this).on('keyup input', function () { resizeTextarea(this); }).removeAttr('data-autoresize');
					resizeTextarea(this);
				});

				//Stars
				review_form
					 .find('.api-star-rating i')
					 .mouseenter(function () {
						 var rating = $(this).parents('.api-star-rating');
						 var elems  = rating.find('i');
						 var index  = elems.index(this);
						 var label  = $(this).data('label');

						 $(this).addClass('active');
						 elems.filter(':lt(' + index + ')').addClass('active');
						 elems.filter(':gt(' + index + ')').removeClass('active');

						 if (label.length)
							 rating_label.html(label);

					 })
					 .click(function () {
						 var rating = $(this).parents('.api-star-rating');
						 var index  = rating.find('i').index(this); // index [0-4]
						 rating.attr('data-star', index);
						 rating.find('input').val(index + 1);// rating [1-5]
						 return false;
					 });

				review_form
					 .find('.api-star-rating')
					 .mouseleave(function () {
						 var rating = $(this);
						 if (rating.attr('data-star')) {
							 rating.find('i').filter(':eq(' + rating.attr('data-star') + ')').addClass('active');
							 rating.find('i').filter(':lt(' + rating.attr('data-star') + ')').addClass('active');
							 rating.find('i').filter(':gt(' + rating.attr('data-star') + ')').removeClass('active');

							 var label = rating.find('i').filter(':eq(' + rating.attr('data-star') + ')').data('label');

							 if (label.length)
								 rating_label.html(label);

						 } else {
							 rating.find('i').removeClass('active');
						 }
					 });

				if (options.USE_EULA) {
					review_form.find('input[name=EULA_ACCEPTED]').on('change',function(){
						if($(this).is(':checked')){
							review_form.find('.api-eula-error').slideUp(200);
						}
					});
				}
				if (options.USE_PRIVACY) {
					review_form.find('input[name=PRIVACY_ACCEPTED]').on('change',function(){
						if($(this).is(':checked')){
							review_form.find('.api-privacy-error').slideUp(200);
						}
					});
				}


				///////////////////////////////////////////////////////////////////////
				//  Video upload
				///////////////////////////////////////////////////////////////////////
				review_form.on('change paste', '.api_video_upload input',function(e){
					var self = this;
					var url = '';

					var clipboardData = e.originalEvent.clipboardData || e.clipboardData || w.clipboardData || null;
					if (clipboardData){
						url = clipboardData.getData("text");
					}
					else {
						url = $(this).val();
					}
					if(url.length){
						$(self).parent().addClass('api_button_busy');
						$.ajax({
							type: 'POST',
							cache: false,
							data: {
								'sessid': BX.bitrix_sessid(),
								'API_REVIEWS_FORM_ACTION': 'VIDEO_UPLOAD',
								'VIDEO_URL': url
							},
							success: function (response) {
								$(self).val('');

								if(response.result === 'ok'){
									var video = response.video || {};
									var image = response.image || {};

									var html = '';
									html += '<div class="api_video_item">';
									html += '<div class="api_video_remove" data-id="'+ video.id +'"></div>';
									html += '<a href="'+ video.url +'" target="_blank">'+ video.title +'</a>';
									html += '</div>';
									review_form.find('.api_video_list').append(html);
								}
								else {
									$.fn.apiAlert(response.alert);
								}

								$(self).parent().removeClass('api_button_busy');
							}
						});
					}
				});

				review_form.on('click','.api_video_remove',function(){
					var videBtn = $(this);
					var videId = $(this).data('id') || '';
					if(videId.length){
						$.ajax({
							type: 'POST',
							cache: false,
							data: {
								'sessid': BX.bitrix_sessid(),
								'API_REVIEWS_FORM_ACTION': 'VIDEO_DELETE',
								'VIDEO_ID': videId
							},
							success: function () {
								$(videBtn).closest('.api_video_item').remove();
							}
						});
					}
					else{
						$(videBtn).closest('.api_video_item').remove();
					}
				});

				//Form submit
				review_submit.on('click', function (e) {

					var bError = false;

					if (options.USE_EULA) {
						if (!review_form.find('input[name=EULA_ACCEPTED]').prop('checked')) {
							review_form.find('.api-eula-error').slideDown(200);
							bError = true;
						}
						else {
							review_form.find('.api-eula-error').slideUp(200);
						}
					}
					if (options.USE_PRIVACY) {
						if (!review_form.find('input[name=PRIVACY_ACCEPTED]').prop('checked')) {
							review_form.find('.api-privacy-error').slideDown(200);
							bError = true;
						}
						else {
							review_form.find('.api-privacy-error').slideUp(200);
						}
					}

					if(bError)
						return false;


					//block fields before ajax
					review_submit.prop('disabled', true).find('.api-button-text').html(options.message.submit_text_ajax);
					//review_form.find('.api-field, .dropdown-field').attr('readonly', true);
					$(modalId).find('.api_modal_loader').fadeIn(200);

					var postData = {
						sessid: BX.bitrix_sessid(),
						API_REVIEWS_FORM_AJAX: 'Y'
					};
					review_form.find('.api-field, .dropdown-field').each(function () {
						var name       = $(this).attr('name');
						postData[name] = $(this).val();
					});

					$.ajax({
						type: 'POST',
						data: postData,
						dataType: 'json',
						error: function (jqXHR, textStatus, errorThrown) {
							console.error('textStatus: ' + textStatus);
							console.error('errorThrown: ' + errorThrown);
							alert(textStatus);
						},
						success: function (response) {

							$(modalId).find('.api_modal_loader').fadeOut(200);

							//console.log(response);

							//unblock fields after ajax
							review_submit.prop('disabled', false).find('.api-button-text').html(options.message.submit_text_default);
							review_form.find('.api-field, .dropdown-field').attr('readonly', false);

							if (response.STATUS === 'ERROR') {

								for (var key in postData) {
									if(key === 'FILES[]'){
										key = 'FILES';
									}
									else if(key === 'VIDEOS[]'){
										key = 'VIDEOS';
									}

									if (response.FIELDS[key]) {
										review_form
											 .find('[name*=' + key + ']')
											 .addClass('api_field_error')
											 .closest('.api_row')
											 .addClass('api_row_error');
									}
									else{
										review_form
											 .find('[name*=' + key + ']')
											 .removeClass('api_field_error')
											 .closest('.api_row')
											 .removeClass('api_row_error');
									}
								}

								review_form.find('.api_field_error').each(function () {
									$(this).on('keyup change', function () {
										if ($(this).val().length)
											$(this)
												 .removeClass('api_field_error')
												 .closest('.api_row')
												 .removeClass('api_row_error');
									});
								});
							}
							else if (response.STATUS === 'OK') {
								//$.fn.apiReviewsForm('alert', modalId, response)

								review_form.find('.api-field:not([name=RATING])').val('');

								$.fn.apiModal('hide', {id:modalId});
								$.fn.apiReviewsList('refresh');

								$.fn.apiAlert({
									class: 'success',
									showIcon: true,
									title: response.MESSAGE,
								});
							}
						}
					});

					e.preventDefault();
				});
			}

			return this;
		},
		alert: function (modalId, data) {
			/*
			 $.fn.apiModal('alert',{
			 type: 'success',
			 autoHide: true, //2000
			 modalId: modalId,
			 message: data.MESSAGE
			 });
			 */

			/*var dialogStyle = $(modalId + ' .api_modal_dialog').attr('style') + ';display: block;';

			var content = '' +
				 '<div class="api_modal_dialog api_alert" style="'+dialogStyle+'">' +
				 '<div class="api_modal_close"></div>' +
				 '<div class="api_alert_success">' +
				 '<span></span>' +
				 '<div class="api_alert_title">'+data.MESSAGE+'</div>' +
				 '</div>' +
				 '</div>';

			$(modalId).html(content);
			$.fn.apiModal('resize',{id:modalId});*/

			/*window.setTimeout(function(){
				$.fn.apiModal('hide', {id:modalId});
				$.fn.apiReviewsList('refresh');
			},2000);*/
		}

	};

	$.fn.apiReviewsForm = function (method) {
		if (methods[method]) {
			return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
		} else if (typeof method === 'object' || !method) {
			return methods.init.apply(this, arguments);
		} else {
			$.error('Error! Method "' + method + '" not found in plugin $.fn.apiReviewsForm');
		}
	};

})(jQuery);