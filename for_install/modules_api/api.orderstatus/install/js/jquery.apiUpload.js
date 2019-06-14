(function ($) {

    // Настройки по умолчанию
    var defaults = {

        url: document.URL,
        method: 'POST',
        extraData: {},
        headers: {},
        maxFileSize: 0,
        maxFiles: 0,
        allowedTypes: '*',
        extFilter: null, //jpg;png;gif
        dataType: 'json', //null, xml, json, script, or html
        fileName: 'file',
        timeout: 20000,

        // events
        onLoad      : function(element, event, percent){},
        //onInit: function(){},
        //onFallbackMode: function(message) {},
        //onNewFile: function(id, file){},
        //onBeforeUpload: function(id){},
        onComplete: function(element,response,xhr){
            if(response.result)
            {
                if(response.result == 'ok')
                {
                    if(response.id)
                    {
                        $(element)
                            .find('.api-icon-cancel')
                            .attr('onclick','AOS_DeleteOrderFile(this,'+ response.id +')')
                    }
                }
                else if(response.message)
                {
                    $(element).addClass('api-error');
                    alert(response.message);
                }
                else
                {
                    $(element).addClass('api-error');
                    alert('Error upload!');
                }
            }
        },
        onUploadProgress: function(progress, percent){
            progress.attr('rel', percent).css('width', percent + '%');
        },
        onUploadSuccess: function(progress, settings){
            //progress.css('background', '#8CC14C');
            //$.fn.apiUpload('updateProgress', progress, 100);
            $(progress).removeClass('api-active');
        },
        onUploadError: function(element, lastError){

            alert('Error trying to upload ' + lastError);
            //console.log('Error trying to upload ' + lastError);
            //$(progress).css('background', '#DA314B');
            $(element).addClass('api-error');
        },
        onFileTypeError: function(element, settings){},
        onFileSizeError: function(element, settings){

            $(element).addClass('api-error');

            //alert('Only the following file types are allowed: '+settings.allow);
            alert('File size(' + element.file.size + ' bytes) of ' + element.file.name + ' exceeds the limit ' + settings.maxFileSize);
            //console.log('File size(' + element.file.size + ' bytes) of ' + element.file.name + ' exceeds the limit ' + settings.maxFileSize);
        },
        onFileExtError: function(element, settings){
            $(element).addClass('api-error');
            alert('Only the following file types are allowed: '+settings.extFilter);
            //console.log('File extension of ' + element.file.name + ' is not allowed');
        },
        onFilesMaxError: function(element, settings){
            //console.log(element.file.name + ' cannot be added to queue due to upload limits.');
        }
    };

    //execCallback(settings.callback.onComplete,[element,event]);
    /**
     * @private
     * Executes an anonymous function or a string reached from the window scope.
     *
     * @example
     * Note: These examples works with every callbacks (onInit, onError, onSubmit, onBeforeSubmit & onAfterSubmit)
     *
     * // An anonymous function inside the "onInit" option
     * onInit: function() { console.log(':D'); };
     *
     * * // myFunction() located on window.coucou scope
     * onInit: 'window.coucou.myFunction'
     *
     * // myFunction(a,b) located on window.coucou scope passing 2 parameters
     * onInit: ['window.coucou.myFunction', [':D', ':)']];
     *
     * // Anonymous function to execute a local function
     * onInit: function () { myFunction(':D'); }
     *
     * @param {string|array} callback The function to be called
     * @param {array} [extraParams] In some cases the function can be called with Extra parameters (onError)
     *
     * @returns {boolean}
     */
    var execCallback = function (callback, extraParams) {

        if (!callback) {
            return false;
        }

        var _callback;

        if (typeof callback === "function") {

            _callback = callback;

        }
        else if (typeof callback === "string" || callback instanceof Array)
        {

            _callback = window;

            if (typeof callback === "string") {
                callback = [callback, []];
            }

            var _exploded = callback[0].split('.'),
                _params = callback[1],
                _isValid = true,
                _splitIndex = 0;

            while (_splitIndex < _exploded.length) {

                if (typeof _callback !== 'undefined') {
                    _callback = _callback[_exploded[_splitIndex++]];
                } else {
                    _isValid = false;
                    break;
                }
            }

            if (!_isValid || typeof _callback !== "function") {

                return false;
            }

        }

        _callback.apply(this, $.merge(_params || [], (extraParams) ? extraParams : []));
        return true;
    };

    // публичные методы
    var methods = {

        // инициализация плагина
        init: function (params, arguments) {

            // актуальные настройки, будут индивидуальными при каждом запуске
            var options = $.extend({}, defaults, params);

            // инициализируем лишь единожды
            if (!this.data('apiUpload')){

                // закинем настройки в реестр data
                this.data('apiUpload', options);

                var fileListID  = this.find('.api-file-list');
                var fileInputID = this.find('.api-upload-file');
                var fileDropID  = this.find('.api-upload-drop');
                var uploadBtnID = this.find('.api-upload-button');

                fileInputID.on('change', function () {
                    $.fn.apiUpload('displayFiles', fileListID, this.files);
                });

                fileDropID
                    .on('drop', function (e) {
                        var dataTransfer = e.originalEvent.dataTransfer;
                        if(dataTransfer && dataTransfer.files)
                        {
                            e.stopPropagation();
                            e.preventDefault();

                            $(this).removeClass('api-dragover');
                            $.fn.apiUpload('displayFiles',  fileListID, dataTransfer.files);
                        }
                    })
                    .on('dragenter', function(e){
                        e.stopPropagation();
                        e.preventDefault();
                    })
                    .on('dragover', function (e) {
                        e.stopPropagation();
                        e.preventDefault();

                        $(this).addClass('api-dragover');
                    })
                    .on('dragleave', function(e){
                        e.stopPropagation();
                        e.preventDefault();

                        $(this).removeClass('api-dragover');
                    });


                uploadBtnID.on('click',function () {

                    fileListID.find('li').each(function () {

                        var progress = $(this).find('.api-progress');
                        if (parseInt(progress.attr('rel')) !== 100) {
                            progress.addClass('api-active');

                            options.file = this.file;
                            $.fn.apiUpload('xhrUpload', options, progress, this);
                        }
                    });
                });
            }

            return this;
        },


        //Отображает превью выбранных файлов
        displayFiles: function (fileListID, files) {

            fileListID.show();
            $.each(files, function (i, file) {

                var li = $('<li/>').appendTo(fileListID);
                var fileName = file.name;
                var fileSize = '';
                var fileExt  = fileName.split(".").pop() || '';

                if (file.size >= 1000000000) {
                    fileSize = (file.size / 1073741824).toFixed(1) + ' GB';
                }
                else if (file.size >= 1000000) {
                    fileSize = (file.size / 1048576).toFixed(1) + ' MB';
                }
                else
                    fileSize = (file.size / 1024).toFixed(1) + ' KB';


                $('<div class="api-progress-bar"><div class="api-progress"></div><div class="api-icon-cancel" onclick="AOS_DeleteOrderFile(this,0)"></div></div>').appendTo(li);
                $('<div class="api-file-label"><span class="api-file-ext-' + fileExt +'"></span><span class="api-file-name">'+ fileName +'</span><span class="api-file-size">' + fileSize +'</span></div>').appendTo(li);

                //Помещаем в объект списка в свойство file выбранный файл
                li.get(0).file = file;
            });
        },

        //Загружает файл на сервер
        xhrUpload: function (settings, progress, element) {

            if (!element.file) {
                return false;
            }

            var xhr = new XMLHttpRequest();
            var formData = new FormData();

            this.percent = 0;
            this.uploaded = false;
            this.successful = false;
            this.lastError = false;
            this.queue++;

            var self = this;

            // Check file size
            if((settings.maxFileSize > 0) && (element.file.size > settings.maxFileSize)){
                settings.onFileSizeError(element, settings);
                return false;
            }

            // Check file type
            if((settings.allowedTypes != '*') &&  !element.file.type.match(settings.allowedTypes)){
                settings.onFileTypeError.call(element, settings);
                return false;
            }

            // Check file extension
            if(settings.extFilter != null){
                var extList = settings.extFilter.toLowerCase().split(',');

                var ext = element.file.name.toLowerCase().split('.').pop();

                if($.inArray(ext, extList) < 0){
                    settings.onFileExtError(element, settings);
                    return false;
                }
            }


            // Check max files
            if(settings.maxFiles > 0) {
                if(self.queue.length >= settings.maxFiles) {
                    settings.onFilesMaxError(element, settings);
                    return false;
                }
            }



            //Event handlers
            xhr.upload.addEventListener("progress", function (e) {
                if (e.lengthComputable) {
                    self.percent = (e.loaded / e.total) * 100;

                    settings.onUploadProgress(progress, Math.round(self.percent));
                }
            }, false);

            xhr.upload.addEventListener("load", function (e) {
                self.percent = 100;
                self.uploaded = true;

                if (settings.onLoad instanceof Function) {
                    settings.onLoad(element, e, self.percent);
                }

            }, false);

            xhr.upload.addEventListener("error", function (e) {
                self.lastError = {
                    code: 1,
                    text: 'Error uploading on server'
                };

                settings.onUploadError(element, self.lastError);

            }, false);


            //xhr settings
            xhr.open(settings.method, settings.url, true);

            if (settings.dataType == "json") {
                xhr.setRequestHeader("Accept", "application/json");
            }

            for (var h in settings.headers) {
                xhr.setRequestHeader(h, settings.headers[h]);
            }

            for (var p in settings.extraData) {
                formData.append(p, settings.extraData[p]);
            }

            // Действия после загрузки файлов
            xhr.onreadystatechange = function () {
                if (this.readyState === 4) {

                    // Действия после успешной загрузки
                    if (this.status === 200) {
                        if (!self.uploaded) {
                            settings.onUploadError(element, self.lastError);
                        } else {
                            self.successful = true;
                            settings.onUploadSuccess(progress, settings);
                        }

                        // Действия после ошибки загрузки
                    } else {
                        self.lastError = {
                            code: this.status,
                            text: 'Error code (' + this.status + ')',
                        };

                        settings.onUploadError(element, self.lastError);
                    }


                    var response = xhr.responseText;
                    if (settings.dataType == "json") {
                        try {
                            response = $.parseJSON(response);
                        } catch(e) {
                            response = false;
                        }
                    }
                    settings.onComplete(element,response,xhr);
                }
            };

            formData.append((settings.fileName  || 'file'), element.file);
            xhr.send(formData);
        }
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