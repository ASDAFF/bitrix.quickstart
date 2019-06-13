/**
 * $.fn.apiUpload
 */
(function ($, window) {

	"use strict";

	// Настройки по умолчанию
	var defaults = {
		formId: '',
		url: document.URL,
		method: 'POST',
		extraData: {},
		headers: {},
		maxFileSize: 0,
		maxFiles: 0,
		allowedTypes: '', //image/*
		extFilter: '', //jpg,png,gif
		dataType: 'json', //null, xml, json, script, or html
		fileName: 'file',
		timeout: 300000,
		autoSubmit: true,
		errors: {
			onFileSizeError: 'File size({{fileSize}} bytes) of {{fileName}} exceeds the limit {{maxFileSize}}',
			onFileTypeError: 'File type {{fileType}} exceeds the type {{allowedTypes}}',
			onFileExtError: 'Only the following file types are allowed: {{extFilter}}',
			onFilesMaxError: 'Maximum number of {{maxFiles}} files exceeded',
			onFallbackMode: 'Your Browser doesn\'t support FormData API',
		},
		callback: {
			onLoad: function (node, event) {},
			onAbort: function (node, event) {},
			onLoadStart: function (node, event) {
				if(this.formId.length){
					$(this.formId).find(':submit').attr('disabled',true);
				}
			},
			onLoadEnd: function (node, event) {
				if(this.formId.length){
					$(this.formId).find(':submit').attr('disabled',false);
				}
			},
			onError: function (node, errors) {
				//console.log('this', this);
				var mess = '';
				for (var i in errors) {
					mess += errors[i] + "\n";
				}
				console.error(mess);
			},
			onInit: function (node){
				//console.trace($(node).attr('id') + ' init');
				//console.log($(node).attr('id') + ' init');
			},
			onFallbackMode: function (message) {
				console.error(message);
			},
			//onNewFile: function(id, file){},
			//onBeforeUpload: function(id){},
			onComplete: function (node, response, xhr) {},
			onUploadProgress: function (progress, event, percent) {},
			onUploadSuccess: function (progress) {},
			onUploadError: function (node, event, lastError) {},
			onFileTypeError: function (node) {},
			onFileSizeError: function (node) {},
			onFileExtError: function (node) {},
			onFilesMaxError: function (node, length) {}
		}
	};

	var methods = {

		init: function (params) {

			var options = $.extend(true, {}, defaults, params);

			if (!this.data('apiUpload')) {
				this.data('apiUpload', options);

				options.callback.onInit.call(options, this);

				var fileListID = $(this).find('.api_file_list');
				var fileInputID = $(this).find('.api_upload_file');
				var fileDropID = $(this).find('.api_upload_drop');
				var uploadBtnID = $(this).find('.api_upload_button');

				if(!methods.checkBrowser(options)){
					return false;
				}

				fileInputID.on('change', function () {
					$.fn.apiUpload('displayFiles', fileListID, this.files);
				});

				fileDropID
					 .on('drop', function (e) {
						 var dataTransfer = e.originalEvent.dataTransfer;
						 if (dataTransfer && dataTransfer.files) {
							 e.stopPropagation();
							 e.preventDefault();

							 $(this).removeClass('api_dragover');
							 $.fn.apiUpload('displayFiles', fileListID, dataTransfer.files);

							 if (options.autoSubmit) {
								 methods.submitFile(fileListID, options);
							 }
						 }
					 })
					 .on('dragenter', function (e) {
						 e.stopPropagation();
						 e.preventDefault();
					 })
					 .on('dragover', function (e) {
						 e.stopPropagation();
						 e.preventDefault();
						 $(this).addClass('api_dragover');
					 })
					 .on('dragleave', function (e) {
						 e.stopPropagation();
						 e.preventDefault();
						 $(this).removeClass('api_dragover');
					 });

				if (options.autoSubmit) {
					fileDropID.on('change', function () {
						methods.submitFile(fileListID, options);
					});
				}
				else {
					uploadBtnID.on('click', function () {
						methods.submitFile(fileListID, options);
					});
				}
			}

			return this;
		},

		checkBrowser: function (settings) {
			if (typeof FormData === 'undefined') {
				settings.callback.onFallbackMode.call(settings, settings.errors.onFallbackMode);
				return false;
			}

			return true;
		},

		//Запускает загрузку файла
		submitFile: function (fileListID, options) {
			fileListID.find('li:not(.api_error)').each(function () {
				var progress = $(this).find('.api_progress');
				if (parseInt(progress.attr('rel')) !== 100) {
					progress.addClass('api_active');
					options.file = this.file;
					$.fn.apiUpload('xhrUpload', options, progress, this);
				}
			});
		},

		//Отображает превью выбранных файлов
		displayFiles: function (fileListID, files) {

			fileListID.show();
			$.each(files, function (i, file) {

				var li = $('<li/>').appendTo(fileListID);
				var fileName = file.name;
				var fileSize = methods.formatFileSize(file.size);
				var fileExt = fileName.split(".").pop() || '';

				var html_1= '<div class="api_progress_bar">' +
										'<div class="api_progress"></div>' +
										'<div class="api_file_remove" data-code="" data-type=""></div>' +
										'</div>';

				$(html_1).appendTo(li);


				var html_2 = '<div class="api_file_label">' +
										 '<span class="api_file_ext_' + fileExt + '"></span> ' +
										 '<span class="api_file_name">' + fileName + '</span> ' +
										 '<span class="api_file_size">' + fileSize + '</span> ' +
										 '</div>';

				$(html_2).appendTo(li);

				//Помещаем в объект списка выбранный файл
				li.get(0).file = file;
			});
		},

		//Форматирует размер файла
		formatFileSize: function(size) {
			var i = Math.floor( Math.log(size) / Math.log(1024) );
			return ( size / Math.pow(1024, i) ).toFixed() * 1 + '&nbsp;' + ['B', 'KB', 'MB', 'GB', 'TB'][i];
		},

		//Загружает файл на сервер
		xhrUpload: function (settings, progress, node) {

			if (!node.file) {
				return false;
			}

			//https://developer.mozilla.org/ru/docs/Web/API/XMLHttpRequest/Using_XMLHttpRequest
			var xhr = XMLHttpRequest ? new XMLHttpRequest : new ActiveXObject("Microsoft.XMLHTTP");

			//>=IE 10
			var formData = new FormData();

			var self = {
				percent: 0,
				percent_round: 0,
				uploaded: false,
				successful: false,
				lastError: false,
				errors: {}
			};

			// Check file size
			if (settings.maxFileSize > 0 && node.file.size > settings.maxFileSize) {
				settings.callback.onFileSizeError.call(settings, node);
				self.errors['onFileSizeError'] = settings.errors.onFileSizeError
					 .replace('{{fileSize}}', methods.formatFileSize(node.file.size))
					 .replace('{{fileName}}', node.file.name)
					 .replace('{{maxFileSize}}', methods.formatFileSize(settings.maxFileSize));
			}

			// Check file type
			if (settings.allowedTypes.length && !node.file.type.match(settings.allowedTypes)) {
				settings.callback.onFileTypeError.call(settings, node);
				self.errors['onFileTypeError'] = settings.errors.onFileTypeError
					 .replace('{{fileType}}', node.file.type)
					 .replace('{{allowedTypes}}', settings.allowedTypes);
			}

			// Check file extension
			if (settings.extFilter.length) {
				//Delete all spaces (/\s*/g,'') and split extensions to Array [ "jpg", "gif", "bmp", "png", "jpeg" ]
				var extList = settings.extFilter.toLowerCase().replace(/\s*/g,'').split(',');
				var ext = node.file.name.toLowerCase().split('.').pop();

				if ($.inArray(ext, extList) < 0) {
					settings.callback.onFileExtError.call(settings, node);
					self.errors['onFileExtError'] = settings.errors.onFileExtError
						 .replace('{{extFilter}}', settings.extFilter);
				}
			}

			// Check max files
			if (settings.maxFiles > 0) {
				var filesCount = $(node).siblings().length;
				if (filesCount >= settings.maxFiles) {
					settings.callback.onFilesMaxError.call(settings, node, filesCount);
					self.errors['onFilesMaxError'] = settings.errors.onFilesMaxError.replace('{{maxFiles}}', settings.maxFiles);
				}
			}

			//onError callback
			if (!$.isEmptyObject(self.errors)) {
				$(node).addClass('api_error');
				settings.callback.onError.call(settings, node, self.errors);
				return false;
				//Как вариант, когда неизвестно колич. параметров
				//settings.callback.onError.apply(settings, [node, self.errors]);
			}


			/////////////////////////////////////////////////////////////////////////
			//Отслеживание событий "исходящего"  процесса загрузки (loadstart,timeout)
			/////////////////////////////////////////////////////////////////////////

			// состояние передачи от сервера к клиенту (загрузка)
			xhr.upload.addEventListener("progress", function (e) {
				if (e.lengthComputable) {
					self.percent = (e.loaded / e.total) * 100;
					self.percent_round = Math.round(self.percent);

					progress.attr('rel', self.percent_round).css('width', self.percent_round + '%');
					settings.callback.onUploadProgress(progress, e, self.percent_round);
				}
				else {
					//Невозможно вычислить состояние загрузки, так как размер неизвестен
					settings.callback.onUploadProgress(progress, e, 0);
					//console.error('progress','Size unknown');
				}
			}, false);

			//Загрузка завершена
			xhr.upload.addEventListener("load", function (e) {
				self.uploaded = true;
				settings.callback.onLoad(node, e);
				//if(settings.callback.onLoad instanceof Function) {}
			}, false);

			//При загрузке файла произошла ошибка
			xhr.upload.addEventListener("error", function (e) {
				self.lastError = {
					code: 1,
					text: 'Error uploading on server'
				};
				settings.callback.onUploadError.call(settings, node, e, self.lastError);

			}, false);

			//Пользователь отменил загрузку
			xhr.upload.addEventListener("abort", function(e) {
				settings.callback.onAbort.call(settings, node, e);
				//console.error('abort','Abort uploading on server');
			}, false);

			//Начало загрузки
			xhr.upload.addEventListener("loadstart", function(e) {
				//Передача данных завершена (но мы не знаем, успешно ли)
				settings.callback.onLoadStart.call(settings, node, e);
				//console.info('loadstart','Data transfer complete');
			}, false);

			//Также возможно засечь все три события, завершающие загрузку (abort, load, or error) через событие loadend:
			xhr.upload.addEventListener("loadend", function(e) {
				//Передача данных завершена (но мы не знаем, успешно ли)
				settings.callback.onLoadEnd.call(settings, node, e);
				//console.error('loadend','Data transfer complete');
			}, false);

			//\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

			//xhr settings
			xhr.open(settings.method, settings.url, true);

			if (settings.dataType === "json") {
				xhr.setRequestHeader("Accept", "application/json");
			}

			for (var h in settings.headers) {
				xhr.setRequestHeader(h, settings.headers[h]);
			}

			for (var p in settings.extraData) {
				formData.append(p, settings.extraData[p]);
			}

			//Действия после загрузки файлов
			//https://developer.mozilla.org/ru/docs/Web/API/XMLHttpRequest/onreadystatechange
			xhr.onreadystatechange = function () {

				//https://xhr.spec.whatwg.org/#handler-xhr-onreadystatechange
				/*if(xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
				 console.log(xhr.responseText);
				 };*/

				//Файл полностью загружен
				if (xhr.readyState === XMLHttpRequest.DONE) {

					//Действия после успешной загрузки
					//https://developer.mozilla.org/en-US/docs/Web/API/XMLHttpRequest/status
					if (xhr.status === 200) {
						if (!self.uploaded) {
							settings.callback.onUploadError.call(settings, node, self.lastError);
						} else {
							$(progress).removeClass('api_active');

							self.successful = true;
							settings.callback.onUploadSuccess.call(settings, progress);
						}

						// Действия после ошибки загрузки
					} else {
						self.lastError = {
							code: xhr.status,
							text: 'Error code (' + xhr.status + ')'
						};
						settings.callback.onUploadError.call(settings, node, self.lastError);
					}

					var response = xhr.responseText;
					if (settings.dataType === "json") {
						try {
							response = $.parseJSON(response);
						} catch (e) {
							response = false;
						}
					}

					if (response !== false) {
						if (response.result === 'ok') {
							if (response.file) {
								$(node).find('.api_file_remove')
									 .attr('data-code', response.file.code)
									 .attr('data-type', response.file.type);
							}
						}
						else {
							$(node).addClass('api_error');
						}
					}

					settings.callback.onComplete.call(settings, node, response, xhr);
				}
			};

			//https://xhr.spec.whatwg.org/#event-xhr-loadstart
			//The append(name, value) and append(name, blobValue, filename) methods, when invoked, must run these steps:
			formData.append((settings.fileName || 'file'), node.file);

			//xhr.abort() обрывает текущий запрос.
			xhr.send(formData);
		},

		log: function (obj) {
			var result = '';
			for (var i in obj) {
				result += i+': ' + obj[i] + "\n";
			}
			alert(result);
		},
	};

	$.fn.apiUpload = function (method) {
		if (methods[method]) {
			return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
		} else if (typeof method === 'object' || !method) {
			return methods.init.apply(this, arguments);
		} else {
			$.error('Error! Method "' + method + '" not found in plugin $.fn.apiUpload');
		}
	};

})(jQuery);