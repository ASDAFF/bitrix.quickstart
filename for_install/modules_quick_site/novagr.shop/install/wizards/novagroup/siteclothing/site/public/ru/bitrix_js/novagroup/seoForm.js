var seoSettings = {
	windowObj: false, // окно
	requestUri: '', // урл
	messages: {}, // текстовые надписи/алерты
	counter: 1,
	/**
	 * параметры которые нужно убрать из урла
	 * @type mixed
	 */
	deleteParams: ['back_url_admin', 'clear_cache', 'logout_butt', 'bitrix_include_areas', 'login', 'logout'],
	/**
	 * инициализация - определяем урл
	 */
	init: function(messages) {
		this.messages = messages;
	},
	clearQuerySrting: function(url) {
		if (url == '') return '';
		var querySrting = '';
		var tmp = new Array();
		var tmp2 = new Array();
		var getArr = [];
		
		tmp = (url.substr(1)).split('&');
		
		for (var i=0; i < tmp.length; i++) {
			
			tmp2 = tmp[i].split('=');

			if (jQuery.inArray(tmp2[0], this.deleteParams) == -1) {
				getArr.push(tmp[i]);
			}
		}
		
		if (getArr.length > 0 ) {
			querySrting = '?' + getArr.join('&');
		}
		return querySrting;
	},
	urlsUpdate: function() {
		BX.showWait();
		$.post("/local/js/novagroup/seoAjax.php", {"action" : "updateUrlsFile"},
			function(data){
				BX.closeWait();
				if (data == 1) $("#okDiv").show();
			},
		"html");		
					
	},
	/**
	 * создаем окно для редактирования seo
	 */
	makeWindow: function() {
		var self = this;
		
		self.windowObj = new BX.CDialog({
			title: self.messages.SEO_URLS_EDIT_LABEL,
			content: "<div id='seoPopup"+ self.counter+"'></div>",
			resizable: true,
			draggable: true,
			height: '400',
			width: '550'
		});
		self.windowObj.SetButtons([
			{
				'title': self.messages.SAVE,
				'id': 'seoSaveBtn',
				'action': function() {
					
					var oldUrl = $("#old_url").val();
					if (oldUrl == "") {
						alert(self.messages.SEO_URLS_ALERT_OLD_URL);
						return false;						
					}
					BX.showWait();
					$.post("/local/js/novagroup/seoAjax.php", $("#seoPopup"+ self.counter +" form").serialize(),
						function(data){
							BX.closeWait();
							self.windowObj.Close();
						},
					"html");	
	
				}
			},
			{
				'id': 'seoCloseBtn',
				'title': self.messages.SEO_URLS_CLOSE_LABEL,
				'action': function(){
					self.windowObj.Close(); 
				}
			}, 
			{
				'id': 'seoUpdateUrls',
				'title': self.messages.SEO_URLS_UPDATE_LABEL,
				'action': function(){
					self.urlsUpdate(); 
				}
			}
		]);
		// получим содержимое окна
		BX.showWait();
		$.post("/local/js/novagroup/seoAjax.php", { "action": "content", "uri": this.requestUri },
			function(data){
				if (data == 'ACCESS DENIED') {
					
					self.windowObj.Close(); 
				} else {
					self.windowObj.Show();
					
					$('#seoPopup'+self.counter).html(data);
					
				}
				
				BX.closeWait();
			},
		"html");
		
	},
	/**
	 * Показываем окно для редактирования СЕО
	 * @param {String} uri
	 * @return {Boolean}
	 */
	seoWindowOpen: function(uri) {
		
		
		var current_uri = window.location.pathname +  this.clearQuerySrting(window.location.search)
		
		if (current_uri != this.requestUri) {
			
			this.counter++;
			this.requestUri = current_uri;
			this.makeWindow();
			
		} else {
			this.windowObj.Show();
		}
		
		
		return false;
	}
	
};
//seoSettings.init();