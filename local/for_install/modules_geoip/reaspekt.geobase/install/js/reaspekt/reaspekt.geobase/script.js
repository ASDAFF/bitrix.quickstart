(function(window, document, $, undefined){
    "use strict";
   
    var paramsDefault = {
        height : "250",
        width : "500",
        ajax  : {
            dataType : 'html',
            headers  : { 'X-reaspektPopupBox': true }
        },
        content : null,
        fixedPosition : false
    };
    var params = {
        htmlPopup : '<div class="ReaspektPopupOverlay"></div><div id="ReaspektPopupBody"><div class="ReaspektClosePosition"><div id="ReaspektCloseBtn"></div></div><div id="ReaspektPopupContainer">Загрузка...</div></div>',
        objPopupIdBody : '#ReaspektPopupBody',
        objPopupIdOverlay : '.ReaspektPopupOverlay',
        objPopupIdCloseBtn : '#ReaspektCloseBtn',
        objPopupIdContainer : '#ReaspektPopupContainer',
		activeClassBodyReaspekt : 'activeClassBodyReaspekt'
    };
    var methods = {
        init : function( options ) {
            
            
            return this.click(function(element){
                var obClass = $(this);
				paramsDefault['href'] = obClass.data('reaspektmodalbox-href') || obClass.attr('href');
				
				var settings = $.extend($.ReaspektModalBox, paramsDefault, options);
                
                methods.addHtmlTemplate(settings);
                
                
                if (!settings.fixedPosition) {
                    $(window).bind('resize.ReaspektPopupOverlay', $.proxy( methods.rePosition, this) );
                    methods.rePosition();
                }
            });
        },
        
        //Добавляем Div`s
        addHtmlTemplate : function(settings) {
            methods.closeReaspektPopup();
			$('body').append(params.htmlPopup);
            $('body').addClass(params.activeClassBodyReaspekt);
            methods.addContainerData(settings);
        },
        
        //Add data in popup html
        addContainerData : function(settings) {
            //Add event click close button
            $(params.objPopupIdCloseBtn).bind("click", function(e){
                e.preventDefault();
                
                methods.closeReaspektPopup();
            });
            
            //Add event click overlay
            $(params.objPopupIdOverlay).bind("click", function(e){
                e.preventDefault();
                
                methods.closeReaspektPopup();
            });
            
            methods._loadAjax(settings);
        },
        
        //Close popup
        closeReaspektPopup : function() {
            $(window).unbind('resize.ReaspektPopupOverlay');
            $('body').removeClass(params.activeClassBodyReaspekt);
            $(params.objPopupIdBody).remove();
            $(params.objPopupIdOverlay).remove();
        },
        
        rePosition : function() {
            
            $(params.objPopupIdBody).css("top", Math.max(0, (($(window).height() - $(params.objPopupIdBody).outerHeight()) / 2) + $(window).scrollTop()) + "px");
            
            $(params.objPopupIdBody).css("left", Math.max(0, (($(window).width() - $(params.objPopupIdBody).outerWidth()) / 2) + $(window).scrollLeft()) + "px");
        },
        
        _loadAjax: function (settings) {
           if (settings.href) {
                $.ajax($.extend({}, settings.ajax, {
                    url: settings.href,
                    error: function (jqXHR, textStatus) {
                        console.log(jqXHR);
                        console.log(textStatus);
                    },
                    success: function (data, textStatus) {
                        if (textStatus === 'success') {
                            settings.content = data;

                            methods._afterLoad(settings);
                        }
                    }
                }));
           } else {
               console.log('Error, not atribute href or data-reaspektmodalbox-href');
           }
		},
        
        _afterLoad: function (settings) {
            $(params.objPopupIdContainer).html(settings.content);
            
            methods.rePosition();
        }
    };

    $.fn.ReaspektModalBox = function( method ) {

        // логика вызова метода
        if ( methods[method] ) {
          return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
        } else if ( typeof method === 'object' || ! method ) {
          return methods.init.apply( this, arguments );
        } else {
          $.error( 'Метод с именем ' +  method + ' не существует для jQuery.ReaspektModalBox' );
        } 
    };
    
})(window, document, jQuery);