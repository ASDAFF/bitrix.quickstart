/*!
 * $.fn.apiReviewsList
 */
(function ($) {

	"use strict"; // Hide scope, no $ conflict

	var location = window.history.location || window.location;
	var defaults = {};
	var options  = {};
	var methods  = {
		init: function (params) {

			options = $.extend(true, {}, defaults, options, params);

			if (!this.data('apiReviewsList')) {
				this.data('apiReviewsList', options);

				var reviews     = '#reviews';
				var reviewsList = '#reviews .api-reviews-list';

				//getLink
				$(reviews).on('click','.js-getLink',function(e){
					var url = $(this).data('url');
					var id = $(this).data('id');
					var txt = options.mess.review_link.replace(/\{id\}/g, id);
					$.fn.apiAlert({
						title: txt,
						input: {
							text: url,
						}
					});
					e.preventDefault();
				});

				//getFileDelete
				$(reviews).on('click','.js-getFileDelete',function(e){
					e.preventDefault();
					methods.fileDelete(this,options.getFileDelete,'fileDelete');
				});

				//getVideoDelete
				$(reviews).on('click','.js-getVideoDelete',function(e){
					e.preventDefault();
					methods.fileDelete(this,options.getVideoDelete,'videoDelete');
				});

				//api-pagination
				$(reviews).on('click','.api-pagination a',function(e){
					var href = $(this).attr('href');
					if(href.length){
						$.fn.apiWait('show');
						$.ajax({
							type: 'POST',
							url: href,
							data: {
								sessid: BX.bitrix_sessid(),
								API_REVIEWS_LIST_AJAX: 'Y'
							},
							error: function (jqXHR, textStatus, errorThrown) {
								console.log('textStatus: ' + textStatus);
								console.log('errorThrown: ' + errorThrown);
							},
							success: function (data) {
								$(reviewsList).replaceWith(data);
								$.fn.apiWait('hide');
								$.fn.apiReviewsList('scroll');
								$.fn.apiReviewsList('refreshGallery',reviewsList);
							}
						});
					}

					/**
					 @param {Object} [data]
					 @param {string} [title]
					 @param {string} [url]
					 @return {void}
					 */
					history.pushState({}, '', href);

					e.preventDefault();
				});

				//Init gallery
				$.fn.apiReviewsList('refreshGallery',reviewsList);
			}
			return this;
		},

		/**
		 * @var object  params {action,id}
		 */
		refresh: function (params) {
			$.fn.apiWait('show');

			var extra     = params || {};
			var reviewsId = '#reviews .api-reviews-list';

			var data = {
				sessid: BX.bitrix_sessid(),
				API_REVIEWS_LIST_AJAX: 'Y'
			};

			$.ajax({
				type: 'POST',
				data: $.extend(true, data, extra),
				error: function (jqXHR, textStatus, errorThrown) {
					console.log('textStatus: ' + textStatus);
					console.log('errorThrown: ' + errorThrown);
				},
				success: function (data) {

					$.fn.apiReviewsList('updateCount');

					if(options.use_stat == 'Y')
						$.fn.apiReviewsStat('update');

					$(reviewsId).replaceWith(data);
					$.fn.apiWait('hide');
					$.fn.apiReviewsList('refreshGallery',reviewsId);
				}
			});

		},
		refreshGallery: function(reviewsId){

			var lightboxElements = {};

			$(reviewsId).find('.api-item').each(function() {

				$(this).find('.api-attachment:not([data-type=file])').each(function(idx, elem) {
					var $elem = $(elem),
						 href = $elem.attr('href'),
						 group = $elem.data('group'),
						 type  = $elem.data('type'),
						 itemSettings = {
							 src: href,
							 type: type
						 }
					;
					lightboxElements[group] = lightboxElements[group] || [];
					lightboxElements[group].push(itemSettings);
				});
			});

			//console.log(lightboxElements);
			$.each(lightboxElements, function(group, items) {

				var $handles = $(reviewsId).find('.api-attachment:not([data-type=file])[data-group="'+ group +'"]');
				$handles.on('click', function(event) {
					event.preventDefault();

					var idx = $handles.index(this) || 0;
					$.magnificPopup.open({
						items: items,
						image: {
							verticalFit: true
						},
						gallery: {
							enabled: true,
							arrows: true,
							navigateByImgClick: true,
							preload: [0,1]
						},
						type: 'image'
					}, idx);

					return false;
				});
			});
		},
		scroll: function(){
			if($('.api-block-content').length){
				$('html, body').animate({
					scrollTop: $('.api-block-content').offset().top
				}, 300);
			}
		},
		updateCount: function (cnt) {

			var count = cnt || false;

			if(count === false)
			{
				$.ajax({
					type: 'POST',
					dataType: 'json',
					async: false,
					data: {
						sessid: BX.bitrix_sessid(),
						API_REVIEWS_LIST_AJAX: 'Y',
						API_REVIEWS_LIST_ACTION: 'getCount'
					},
					error: function (jqXHR, textStatus, errorThrown) {
						console.log('textStatus: ' + textStatus);
						console.log('errorThrown: ' + errorThrown);
					},
					success: function (data) {
						if(data.status == 'ok'){
							count = parseInt(data.count);
						}
						$('.api-reviews-count').each(function(){
							$(this).text('('+count+')');
						});
					}
				});
			}
			else{
				$('.api-reviews-count').each(function(){
					$(this).text('('+count+')');
				});
			}
		},
		show: function (id) {
			var params = {
				API_REVIEWS_LIST_ACTION: 'show',
				id: id
			};
			$.fn.apiReviewsList('refresh', params);
		},
		hide: function (id) {
			var params = {
				API_REVIEWS_LIST_ACTION: 'hide',
				id: id
			};
			$.fn.apiReviewsList('refresh', params);
		},
		save: function (id) {

			var review = $.fn.apiReviewsList('getReviewId', id);

			var fields = {};
			review.find('[data-field]').each(function () {
				var field_code     = $(this).data('field');
				fields[field_code] = $(this).val();
			});

			var params = {
				API_REVIEWS_LIST_ACTION: 'save',
				id: id,
				fields: fields
			};
			$.fn.apiReviewsList('refresh', params);
		},
		cancel: function (id) {

			var review = $.fn.apiReviewsList('getReviewId', id);

			review.find('.api-edit').removeClass('api-hidden');
			review.find('.api-save').addClass('api-hidden');
			review.find('.api-cancel').addClass('api-hidden');

			review.find('[data-edit]').each(function () {

				var field_code = $(this).data('edit');

				if (field_code != 'TITLE')
					$(this).css({'display': 'inline-block'});

				var field_html = $(this).find('[data-fake-field]').html();
				$(this).html(field_html);

			});
		},
		edit: function (id) {

			var review = $.fn.apiReviewsList('getReviewId', id);

			review.find('.api-edit').addClass('api-hidden');
			review.find('.api-save').removeClass('api-hidden');
			review.find('.api-cancel').removeClass('api-hidden');

			review.find('[data-edit]').each(function () {
				var field_code = $(this).data('edit');
				var field_val  = $(this).text();
				var field_html = $(this).html(); //For cancel action

				$(this).css({'display': 'block'});

				if (field_code == 'TITLE' || field_code == 'COMPANY' || field_code == 'WEBSITE') {
					$(this).html('<input type="text" data-field="' + field_code + '" value="' + field_val + '"><div data-fake-field>' + field_html + '</div>');
				}
				else {
					$(this).html('<textarea data-field="' + field_code + '" data-autoresize>' + field_val + '</textarea><div data-fake-field>' + field_html + '</div>');
				}
			});


			//autoresize textarea
			review.find('[data-autoresize]').each(function () {
				var offset         = this.offsetHeight - this.clientHeight;
				var resizeTextarea = function (el) {
					$(el).css('height', 'auto').css('height', el.scrollHeight + offset);
				};
				$(this).on('keyup input', function () { resizeTextarea(this); }).removeAttr('data-autoresize');
				resizeTextarea(this);
			});

		},
		delete: function (id) {

			$.fn.apiAlert({
				type: 'confirm',
				class: 'warning',
				showIcon: true,
				title: options.mess.review_delete.replace(/\{id\}/g, id),
				/*labels: {
					ok:config.labelOk,
					cancel:config.labelCancel,
				},*/
				callback: {
					onConfirm: function (isConfirm) {
						if (isConfirm) {
							var params = {
								API_REVIEWS_LIST_ACTION: 'delete',
								id: id
							};
							$.fn.apiReviewsList('refresh', params);
						}
					},
				}
			});

			/*if (confirm(options.mess.review_delete.replace(/\{id\}/g, id))) {
				var params = {
					API_REVIEWS_LIST_ACTION: 'delete',
					id: id
				};
				$.fn.apiReviewsList('refresh', params);
			}*/
		},
		vote: function (_this, id, vote) {
			$(_this).addClass('api-wait-small');
			$.ajax({
				type: 'POST',
				dataType: 'json',
				data: {
					sessid: BX.bitrix_sessid(),
					API_REVIEWS_LIST_AJAX: 'Y',
					API_REVIEWS_LIST_ACTION: 'vote',
					id: id,
					value: vote
				},
				success: function (data) {
					if (data.vote != false) {
						$(_this).addClass('api-thumbs-active').find('.api-counter').html(data.vote);
					}
					$(_this).removeClass('api-wait-small');
				}
			});
		},
		send: function (_this, id) {
			$(_this).addClass('api_button_wait').parents('.api-footer').addClass('api_form_wait');
			$(_this).prop('disabled',true);

			$.ajax({
				type: 'POST',
				dataType: 'json',
				data: {
					sessid: BX.bitrix_sessid(),
					API_REVIEWS_LIST_AJAX: 'Y',
					API_REVIEWS_LIST_ACTION: 'send',
					id: id
				},
				success: function (data) {
					setTimeout(function(){
						$(_this).fadeOut(100);
					},500);
				}
			});
		},
		showReply: function (ID, bShowCeckbox) {
			var review = $.fn.apiReviewsList('getReviewId', ID);
			var answer = $('#api-answer-text-' + ID).text();

			var button_send = '';
			if (bShowCeckbox)
				button_send = '<button type="button" class="api_button api_button_small api_button_success" onclick="jQuery.fn.apiReviewsList(\'saveReply\','+ID+',1);">' + options.mess.btn_reply_send + '</button>';

			var html = '' +
				 '<div id="api-reply" style="display:none">' +
				 '<div class="api-reply-textarea">' +
				 '<textarea data-autoresize="">' + answer + '</textarea>' +
				 '</div>' +
				 '<div class="api-reply-button">' +
				 '<button type="button" class="api_button api_button_small api_button_primary" onclick="jQuery.fn.apiReviewsList(\'saveReply\','+ID+',0);">' + options.mess.btn_reply_save + '</button>' +
				 '<button type="button" class="api_button api_button_small api_button_primary" onclick="jQuery.fn.apiReviewsList(\'cancelReply\','+ID+');">' + options.mess.btn_reply_cancel + '</button>'
				 + button_send +
				 '</div>' +
				 '</div>';

			$('#api-reply').remove();
			review.find('.api-admin-controls').after(html);
			$('#api-reply').slideDown(200);

			//autoresize textarea
			review.find('[data-autoresize]').each(function () {
				var offset         = this.offsetHeight - this.clientHeight;
				var resizeTextarea = function (el) {
					$(el).css('height', 'auto').css('height', el.scrollHeight + offset);
				};
				$(this).on('keyup input', function () { resizeTextarea(this); }).removeAttr('data-autoresize');
				resizeTextarea(this);
			});
		},
		cancelReply: function (ID) {
			$('#api-reply').slideUp(200,function(){$(this).remove()});
		},
		saveReply: function (ID, bSend) {
			$.fn.apiWait('show');

			$.ajax({
				type: "POST",
				dataType: 'json',
				data: {
					sessid: BX.bitrix_sessid(),
					API_REVIEWS_LIST_AJAX: 'Y',
					API_REVIEWS_LIST_ACTION: 'reply',
					id: ID,
					reply: $('#api-reply textarea').val(),
					bSend: bSend
				},
				success: function (data) {
					var review = $.fn.apiReviewsList('getReviewId', ID);

					if (data.status && data.status == 'ok') {
						var html = '' +
							 '<div class="api-answer">' +
							 '<div class="api-shop-name">' + options.mess.shop_name_reply + '</div>' +
							 '<div class="api-shop-text" id="api-answer-text-' + ID + '">' + data.text + '</div>' +
							 '</div>';

						$('#api-reply').remove();
						review.find('.api-answer').remove();

						if (data.text && data.text.length){
							review.find('.api-footer .api-user-info').after(html);
						}

						if(data.bSend == true)
							review.find('.api-answer').addClass('api-answer-send');
					}

					$.fn.apiWait('hide');
				}
			});
		},
		getReviewId: function (ID) {

			var reviewId = '.api-reviews-list #review{id}';

			return $(reviewId.replace(/\{id\}/g, ID));
		},
		fileDelete: function (node,config,action) {
			var id     = $(node).data('id');
			var fileId = $(node).data('file');
			var link   = $(node).closest('.api-attachment-wrap');

			if(id && fileId && link){
				$.fn.apiAlert({
					type: 'confirm',
					class: 'warning',
					title: config.confirmTitle,
					content: config.confirmContent,
					labels: {
						ok:config.labelOk,
						cancel:config.labelCancel,
					},
					callback: {
						onConfirm: function (isConfirm) {
							if (isConfirm) {
								$(link).addClass('api_button_busy');
								$.ajax({
									type: 'POST',
									data: {
										sessid: BX.bitrix_sessid(),
										API_REVIEWS_LIST_AJAX: 'Y',
										API_REVIEWS_LIST_ACTION: action,
										id: id,
										fileId: fileId,
									},
									error: function (jqXHR, textStatus, errorThrown) {
										console.error('textStatus: ' + textStatus);
										console.error('errorThrown: ' + errorThrown);
									},
									success: function () {
										$(link).remove();
									}
								});
							}
						},
					}
				});
			}
		}
	};

	$.fn.apiReviewsList = function (method) {

		if (methods[method]) {
			return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
		} else if (typeof method === 'object' || !method) {
			return methods.init.apply(this, arguments);
		} else {
			$.error('Error! Method "' + method + '" not found in plugin $.fn.apiReviewsList');
		}
	};

})(jQuery);


/**
 * $.fn.apiWait
 */
(function ($) {
	var methods = {
		show: function () {

			if (!$('#api-reviews-wait').length) {
				$('<div id="api-reviews-wait"><span class="api-image"></span><span class="api-bg"></span><div>').appendTo('body');
			}

			$('#api-reviews-wait').show();
		},
		hide: function () {
			$('#api-reviews-wait').hide();
		}
	};

	$.fn.apiWait = function (method) {
		if (methods[method]) {
			return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
		} else {
			$.error('Error! Method "' + method + '" not found in plugin $.fn.apiWait');
		}
	};

})(jQuery);


/**
 * @deprecated
 *
 * Scroll to review
 */
/*
(function ($) {
	//TypeError: a.indexOf is not a function fix
	$(window).on('load', function () {

		var wndHash      = window.location.hash;
		var wndCanScroll = (wndHash.length && wndHash.substring(1, 7) === 'review');

		if (wndCanScroll) {
			$('html, body').animate({
				scrollTop: $(wndHash).offset().top
			}, 200, function () {
				$(wndHash).addClass('api-active');
			});
			return false;
		}
	});
})(jQuery);
*/