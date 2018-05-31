(function (window) {

    if (!!window.JCReaspektGeobase) {
        return;
    }

    window.JCReaspektGeobase = function (arParams) {
        this.letters = '';
        this.timer = '0';
        
        if (typeof arParams === 'object') {
            this.params = arParams;
        }
    }
    
    window.JCReaspektGeobase.prototype.onClickReaspektGeobase = function(city_id) {
		var obClass = this;
        
        this.showPreloaderReaspektGeobase();
		
        $.ajax({
            url: this.params.AJAX_URL.SELECT,
            dataType: 'html',
            data: {
                'CITY_ID': city_id,
            },
            type: 'POST',
            success: function (data) {
                var dataJSON = JSON.parse(data);
                
				if (dataJSON.STATUS == "Y") {
					console.log('close');
					obClass.onClickReaspektSaveCity("Y");
					
                } else {
                    console.log('Error, change city!');
                }
                
            }
        });
    }
	
	window.JCReaspektGeobase.prototype.onClickReaspektSaveCity = function(reload) {
		var obClass = this;
		
		$('.' + obClass.params.CLASS.WRAP_QUESTION_REASAPEKT).fadeOut("700");
		
        $.ajax({
            url: this.params.AJAX_URL.SAVE,
            dataType: 'html',
            data: {},
            type: 'GET',
            success: function (data) {
                var dataJSON = JSON.parse(data);
                if (dataJSON.STATUS == "Y") {
                    console.log('save');
					$('.' + obClass.params.CLASS.WRAP_QUESTION_REASAPEKT).remove();
					
					if (reload == "Y") {
						document.location.reload();
					}
                } else {
                    console.log('Error, no save change!');
                }
                
            }
        });
    }
    
    window.JCReaspektGeobase.prototype.inpKeyReaspektGeobase = function(e) {
        e = e||window.event;
        
        var obClass = this;
        var t = (window.event) ? window.event.srcElement : e.currentTarget;
        var list = $('.reaspektGeobaseCities');
        var sFind = BX.util.trim(t.value);
        
        if(this.letters == sFind)
            return; // if nothing has changed, do not do heavy load server
        
        this.letters = sFind;
        
        if(this.timer){
            clearTimeout(this.timer);
            this.timer = 0;
        }
        
        if(this.letters.length < 2){
            //list.html("ничего не найдено");
            //list.animate({ height: 'hide' }, "fast");
            return;
        }
        
        this.timer = window.setTimeout(this.loadReaspektGeobase.bind(this), 190);
    }
    
    window.JCReaspektGeobase.prototype.loadReaspektGeobase = function() {
        var obClass = this;
        
        this.showPreloaderReaspektGeobase();
        
        this.timer = 0;
        var list = $('.reaspektGeobaseFind');
        
        
        $.ajax({
            type: "POST",
            url: this.params.AJAX_URL.GET,
            dataType: 'html',
            data: { 
                'city_name': this.letters
            },
            timeout: 10000,
            success: function(data){
                if ($('.reaspektResultCityAjax').length) {
                    $('.reaspektResultCityAjax').remove();
                }
                list.append('<div class="reaspektResultCityAjax">' + data + '</div>');
                $.fn.ReaspektModalBox('rePosition');
                
                obClass.hidePreloaderReaspektGeobase();
            }
        });
    }
    
    window.JCReaspektGeobase.prototype.showPreloaderReaspektGeobase = function(el) {
        $('.reaspektGeobaseWrapperPopup').append('<span id="reaspekt_preloader"></span>');
        $("#reaspekt_preloader").animate({opacity: 1,}, 500 );
    }
	
    window.JCReaspektGeobase.prototype.hidePreloaderReaspektGeobase = function(el) {
        if ($('#reaspekt_preloader').length) {
            $('#reaspekt_preloader').remove();
        }
    }

})(window);