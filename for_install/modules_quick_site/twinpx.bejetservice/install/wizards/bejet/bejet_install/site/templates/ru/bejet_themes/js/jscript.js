$(function() {
	initPage();
	
	windowEvents();
	
	topMenu();
	
	$("[data-placeholder]").each(function() {
		$(this).placeholder();
	});
	
	new Form();
	
	initMap();
});

function initMap() {
	(function(d, s, id) {
		function delayedLoad() {
			var js, fjs = d.getElementsByTagName(s)[0];
			if (d.getElementById(id)) return;
			js = d.createElement(s); js.id = id;
			js.src = "http://api-maps.yandex.ru/2.0/?load=package.full&amp;lang=ru-RU";
			fjs.parentNode.insertBefore(js, fjs);
			
			initYmap();
		}
	 
		if (window.addEventListener) {
			window.addEventListener("load", delayedLoad, false);
		} else if (window.attachEvent) {
			window.attachEvent("onload",delayedLoad);
		}
	}(document, 'script', 'ymap-script'));
	
	function initYmap() {
		
		var intId = setInterval(function() {check();}, 500);
		
		function check() {
			if(window.ymaps) {
				clearInterval(intId);
				ymaps.ready(initYmapOnPage);
			}
		}
	}
			
}

function Form() {
	var self = this;
	
	init();
	
	function init() {
		initVarsAndElems();
		handleEvents();
	}
	
	function initVarsAndElems() {
		self.$elem = $("#contact-form form");
		self.$elem.data("Form", self);
		self.submitFlag = 0;
		self.firstElement = null;
		self.$submitButton = self.$elem.find(".b-form-submit .b-button");
	}
	
	function handleEvents() {
		self.$submitButton.click(clickSubmitButton);
		self.$elem.submit(submitForm);
		self.$elem.find("input, textarea").focus(focusElement);
	}
	
	function focusElement() {
		removeAttention($(this));
	}
	
	function clickSubmitButton(e) {
		self.$elem.submit();
		e.preventDefault();
	}
	
	function submitForm(e) {
		if(isValid()) {
			$.ajax({
				url: self.$elem.attr("action"),
				type: self.$elem.attr("method"),
				dataType: "json",
				data: self.$elem.serialize(),
				success: function(data) {
					showSent(data);
					setTimeout(resetForm, 5000);
				},
				error: ajaxError
			});
		}
		
		e.preventDefault();
	}
	
	function resetForm() {
		var $icon = $("#form-block .b-icon");
		$icon.animate({opacity: 0});
		$("#b-contact-form-message").text("").fadeOut(500, function() {
			self.$elem.find("input, textarea").val("").blur();
			self.$elem.fadeIn(500);
			$icon.css({backgroundPosition: "0 0"}).animate({opacity: 1});
		});
	}
	
	function showSent(data) {
		animateIcon();
		self.$elem.fadeOut(500, function() {
			if(data && data.message) $("#b-contact-form-message").text(data.message).fadeIn();
		});
		
		function animateIcon() {
			var $icon = $("#form-block .b-icon");
			for(var i = 0; i < 4; i++) {
				var func = function(i) {
					setTimeout(function() {
						$icon.css({backgroundPosition: (-1 * i * 76) + "px 0"});
					}, 80 * i);
				}(i);
			}
		}
	}
	
	function setAttention($elem) {
		$elem.closest(".b-form-field").addClass("i-attention");
		
		if(self.submitFlag == 0) {
			self.firstElement = $elem;
		}
		self.submitFlag = 1;
	}
	
	function removeAttention($elem) {
		$elem.closest(".b-form-field").removeClass("i-attention");
	}
	
	function isValid() {
		return check();
		
		function check() {
			self.submitFlag = 0;
			self.firstElement = null;
			
			checkSpecialTypes();
			checkRequiredOr();
			checkEqual();
			checkEmpty();
			
			if (self.submitFlag == 0) return true;
			
			var scrolled = window.pageYOffset || document.documentElement.scrollTop;
			if((self.firstElement.offset().top - scrolled) < 0) {
				$.scrollTo(self.firstElement.parent(), 10);
				if(self.firstElement != null) {
					self.firstElement.focus();
				}
			}
			return false;						
		}
		
		function checkEqual() {
			var orFieldsObject = {};
			self.$elem.find("[data-equal]").each(function() {
				var $filed = $(this),
					data = $filed.attr("data-equal");
					
				if(!orFieldsObject[data]) {
					orFieldsObject[data] = self.$elem.find("[data-equal=" + data + "]");
				}
			});
			
			var flag;
			for(var key in orFieldsObject) {
				flag = true;
				
				var value = $.trim($(orFieldsObject[key][0]).val());
				orFieldsObject[key].each(function() {
					if($.trim($(this).val()) != value) {
						flag = false;
					}
				});
				
				if(!flag) {
					orFieldsObject[key].each(function() {
						setAttention($(this));
					});
				}
				else {
					orFieldsObject[key].each(function() {
						removeAttention($(this));
					});
				}
			}
		}
		
		function checkEmpty() {
			self.$elem.find(".b-select.i-required").each(function() {
				if($(this).find("input:hidden").val() == "") {
					setAttention($(this));
				} else {
					removeAttention($(this));
				}
			});
			self.$elem.find("[required]").each(function() {
				var $field = $(this),
					$val = $.trim($field.val());
				
				if ($field.is("[type=radio]")) {
					if($field.closest(".b-form-field").find("input:checked").size() == 0) {
						setAttention($field);
					}
				}
				else if ($field.is("[type=checkbox]")) {
					if(!$field.is(":checked")) {
						setAttention($field);
					} else {
						removeAttention($field);
					}
				}
				else if ($field.is("[data-equal]")) {
					if($.trim($field.val()) == "") {
						setAttention($field);
					}
				}
				else if ($val == "") {
					setAttention($field);
				}
				else if(!$field.is("[type=email]") && !$field.is("[type=tel]") && !$field.is("[type=number]") && !$field.is("[type=url]")) {
					removeAttention($field);
				}
			});
		}
		
		function checkSpecialTypes() {
			checkPasswordType();
			checkEmailType();
			checkTelType();
			checkNumberType();
			checkUrlType();
			
			function checkPasswordType() {
				self.$elem.find("input:visible[type=password]").each(function() {
					var $field = $(this),
						$val = $.trim($field.val()),
						num = 6;
					
					if ($val.length < num) {
						setAttention($field);
					}
					else {
						removeAttention($field);
					}
				});
			}
			
			function checkEmailType() {
				self.$elem.find("[type=email]").each(function() {
					var $field = $(this),
						$val = $.trim($field.val()),
						mailRegex = /^([^@\s]+)@((?:[-a-z0-9]+\.)+[a-z]{2,})$/i;
					
					if ($val != "" && !mailRegex.test($val)) {
						setAttention($field);
					}
					else {
						removeAttention($field);
					}
				});
			}
			
			function checkTelType() {
				self.$elem.find("[type=tel]").each(function() {
					var $field = $(this),
						$val = $.trim($field.val()),
						phoneRegex = /^([0-9-()\++\s]{5,})$/i;
					
					if ($val != "" && !phoneRegex.test($val)) {
						setAttention($field);
					}
					else {
						removeAttention($field);
					}
				});
			}
			
			function checkNumberType() {
				self.$elem.find("[type=number]").each(function() {
					var $field = $(this),
						$val = $.trim($field.val()),
						numRegex = /^([0-9\s\.,]+)$/i;
					
					if ($val != "" && !numRegex.test($val)) {
						setAttention($field);
					}
					else {
						removeAttention($field);
					}
				});
			}
			
			function checkUrlType() {
				self.$elem.find("[type=url]").each(function() {
					var $field = $(this),
						$val = $.trim($field.val()),
						urlRegex = /^((https?:\/\/)?(www\.)?([-a-z0-9]+\.)+[a-z]{2,})$/i;
					
					if ($val != "" && !urlRegex.test($val)) {
						setAttention($field);
					}
					else {
						removeAttention($field);
					}
				});
			}
			
		}
		
		function checkRequiredOr() {
			var orFieldsObject = {};
			self.$elem.find("[data-required-or]").each(function() {
				var $filed = $(this),
					data = $filed.attr("data-required-or");
					
				if(!orFieldsObject[data]) {
					orFieldsObject[data] = self.$elem.find("[data-required-or=" + data + "]");
				}							
			});
			
			var counter;
			for(var key in orFieldsObject) {
				counter = 0;
				
				orFieldsObject[key].each(function() {
					if($.trim($(this).val()) != "") {
						counter++;
					}
				});
				
				if(counter == 0) {
					orFieldsObject[key].each(function() {
						setAttention($(this));
					});
				}
				else {
					orFieldsObject[key].each(function() {
						removeAttention($(this));
					});
				}
			}
		}
	} 
}

function topMenu() {
	if(checkPageElements()) return;
	
	var headerHeight = $(".b-header").height();
	var top = parseInt($(".b-header").css("top"));
	
	$(".b-top-menu a").click(function(e) {
		var href = $(this).attr("href");
		
		if(href == "#index") {
			$.scrollTo(0, 500);
			return;
		}
		
		for(var i = 0; i < pagesArray.length; i++) {
			if("#" + pagesArray[i].page == href) {
				$.scrollTo(pagesArray[i].top - top - headerHeight + "px", 500);
			}
		}
		e.preventDefault();
	});
	
	$("body").delegate("[href='#form']", "click", function(e) {
		$.scrollTo($("#form-block").offset().top - top - headerHeight + "px", 500);
		return false;
	});
}

function setPageArray() {
	window.pagesArray = [];
	
	$(".i-page-block").each(function(index) {
		pagesArray.push({top: Math.floor($(this).offset().top), page: $(this).find(".b-anchor").attr("name")});
	});
}

function windowEvents() {
	
	if(checkPageElements()) return;

	setPageArray();
	
	$(window)
		.scroll(scrollWindow).scroll()
		.resize(resizeWindow).resize();
		
		setTimeout(function() {
			$(".b-index-block__ill img").height("72%").animate({opacity: 1});
		}, 100);
	
	function resizeWindow(e) {
		var headerHeight = $(".b-header").height();
		var pageHeight = $(window).height() - window.pageBlocksTop - headerHeight - 120;
		setPagesHeight(pageHeight);
		setPageArray();
	}
	
	function scrollWindow(e) {
		var scrolled = getScrolled();
		if(scrolled < pagesArray[0].top) {
			$(".b-top-menu a").removeClass("i-active");
			$(".b-top-menu__link__type_index").addClass("i-active");
		}
		
		var scrolled = parseFloat(scrolled) + parseFloat($(".b-header").css("top")) + parseFloat($(".b-header").height());
		
		for(var i = 0; i < pagesArray.length; i++) {
			if(scrolled >= pagesArray[i].top) {
				if(pagesArray[i+1]) {
					if(scrolled < pagesArray[i+1].top) makeMenuItemActive(i);
				} else {
					makeMenuItemActive(i);
				}
			}
		}
	}
	
	function makeMenuItemActive(num) {
		var $item = $(".b-top-menu a[href='#" + pagesArray[num].page + "']");
		if($item.hasClass("i-active")) return;
		
		$(".b-top-menu a").removeClass("i-active");
		$item.addClass("i-active");
	}
	
	function getScrolled() {
		return window.pageYOffset || document.documentElement.scrollTop;
	}
}

function checkPageElements() {
	return $(".b-header").size() == 0 || $("#page-blocks").size() == 0;
}

function initPage() {
	if(checkPageElements()) return;
	
	var $header = $(".b-header");
	var $pageBlocks = $("#page-blocks");
	var headerHeight = $header.height();
	var $indexBlock = $("#index-block");
	window.pageBlocksTop = Math.floor($pageBlocks.offset().top);
	var pageHeight = $(window).height() - pageBlocksTop - headerHeight - 120;
	
	//header
	var $headerFixedBlock = $('<div class="b-header-fixed-block"></div>');
	$headerFixedBlock.css({height: headerHeight + "px"});
	$header.addClass("i-fixed").css({top: pageBlocksTop + "px"}).after($headerFixedBlock);
	
	$indexBlock.css({top: pageBlocksTop + headerHeight + "px"});
	$("#page-blocks").css({zIndex: parseInt($indexBlock.css("zIndex"), 10) + 1});
	
	setPagesHeight(pageHeight);
	
	$pageBlocks.animate({opacity: 1});
	
}

function setPagesHeight(pageHeight) {
	var $indexBlock = $("#index-block");
	var headerHeight = $(".b-header").height();
	
	//index page
	$indexBlock.height(pageHeight);
	$("#page-blocks").css({top: pageHeight + "px"});
	
	//pages height
	$(".i-page-block").each(function(index) {
		if(index == $(".i-page-block").size() - 1) return;
		if($(this).outerHeight() < pageHeight) $(this).height(pageHeight);
	});
}

//--placeholder--//
(function($) {
	var defaults = {
		//text:"",
		//color:"#aaaaaa"
	};
	$.fn.placeholder = function(params) {
		
		var options = $.extend({}, defaults, params);
		
		$(this).each(function() {
			
			var self = this;
			self.$elem = $(this),
			self.$formField = self.$elem.closest(".b-form-field");
			self.placeholderText = options.text || self.$elem.attr("data-placeholder");
			
			init();
			
			function init() {
				if(!self.$placeholder) {
					createPlaceholder();
				}
				turnOn();
				handleEvents();
			}
			
			function turnOn() {
				setTimeout(function() {//for chrome, for it fills the password field in some time after loading the page
					if(self.$elem.val() == "") {
						self.$formField.addClass("i-placeholder");
					}
				}, 10);
			}
			
			function handleEvents() {
				self.$placeholderText.click(function() {
					if(!self.$elem.parent().hasClass("i-disabled"))
					self.$elem.focus();
				});
				
				self.$elem
					.focus(function() {
						onFocus();
					})
					.blur(function() {
						onBlur();
					});
				
			}
			
			function createPlaceholder() {
				self.$placeholder = $('<div class="b-form-field__placeholder"><div class="b-form-field__placeholder__text">' + self.placeholderText + '</div></div>');
				self.$placeholderText = self.$placeholder.find(".b-form-field__placeholder__text");
				
				self.$elem.before(self.$placeholder);
				
				setPlaceholderSize();
			}
			
			function setPlaceholderSize() {
				if(self.$elem.is(":visible")) {
					setSize();
				}
				else {
					var $elem = self.$elem;
					var $parent = $elem.parent();
					$elem.css({position: "absolute", bottom: "0", left: "0"}).appendTo("body");
					setSize();
					$parent.append($elem);
					$elem.css({position: "static", bottom: "none", left: "none"});
				}
				
				function setSize() {
					self.$placeholderText
						.css(
							{
								width: self.$elem.outerWidth() - parseInt(self.$placeholderText.css("paddingLeft")) + "px"
							}
						);
				}
			}
			
			function onFocus() {
				self.$formField.removeClass("i-placeholder");
			}
			
			function onBlur() {
				if (self.$elem.val() == "") {
					self.$formField.addClass("i-placeholder");
				}
			}
			
		});
		return this;
	};
})(jQuery);

/**
 * Copyright (c) 2007-2012 Ariel Flesler - aflesler(at)gmail(dot)com | http://flesler.blogspot.com
 * Dual licensed under MIT and GPL.
 * @author Ariel Flesler
 * @version 1.4.3.1
 */
;(function($){var h=$.scrollTo=function(a,b,c){$(window).scrollTo(a,b,c)};h.defaults={axis:'xy',duration:parseFloat($.fn.jquery)>=1.3?0:1,limit:true};h.window=function(a){return $(window)._scrollable()};$.fn._scrollable=function(){return this.map(function(){var a=this,isWin=!a.nodeName||$.inArray(a.nodeName.toLowerCase(),['iframe','#document','html','body'])!=-1;if(!isWin)return a;var b=(a.contentWindow||a).document||a.ownerDocument||a;return/webkit/i.test(navigator.userAgent)||b.compatMode=='BackCompat'?b.body:b.documentElement})};$.fn.scrollTo=function(e,f,g){if(typeof f=='object'){g=f;f=0}if(typeof g=='function')g={onAfter:g};if(e=='max')e=9e9;g=$.extend({},h.defaults,g);f=f||g.duration;g.queue=g.queue&&g.axis.length>1;if(g.queue)f/=2;g.offset=both(g.offset);g.over=both(g.over);return this._scrollable().each(function(){if(e==null)return;var d=this,$elem=$(d),targ=e,toff,attr={},win=$elem.is('html,body');switch(typeof targ){case'number':case'string':if(/^([+-]=)?\d+(\.\d+)?(px|%)?$/.test(targ)){targ=both(targ);break}targ=$(targ,this);if(!targ.length)return;case'object':if(targ.is||targ.style)toff=(targ=$(targ)).offset()}$.each(g.axis.split(''),function(i,a){var b=a=='x'?'Left':'Top',pos=b.toLowerCase(),key='scroll'+b,old=d[key],max=h.max(d,a);if(toff){attr[key]=toff[pos]+(win?0:old-$elem.offset()[pos]);if(g.margin){attr[key]-=parseInt(targ.css('margin'+b))||0;attr[key]-=parseInt(targ.css('border'+b+'Width'))||0}attr[key]+=g.offset[pos]||0;if(g.over[pos])attr[key]+=targ[a=='x'?'width':'height']()*g.over[pos]}else{var c=targ[pos];attr[key]=c.slice&&c.slice(-1)=='%'?parseFloat(c)/100*max:c}if(g.limit&&/^\d+$/.test(attr[key]))attr[key]=attr[key]<=0?0:Math.min(attr[key],max);if(!i&&g.queue){if(old!=attr[key])animate(g.onAfterFirst);delete attr[key]}});animate(g.onAfter);function animate(a){$elem.animate(attr,f,g.easing,a&&function(){a.call(this,e,g)})}}).end()};h.max=function(a,b){var c=b=='x'?'Width':'Height',scroll='scroll'+c;if(!$(a).is('html,body'))return a[scroll]-$(a)[c.toLowerCase()]();var d='client'+c,html=a.ownerDocument.documentElement,body=a.ownerDocument.body;return Math.max(html[scroll],body[scroll])-Math.min(html[d],body[d])};function both(a){return typeof a=='object'?a:{top:a,left:a}}})(jQuery);

function ajaxError(a, b, c) {
	if(window.console) {
		console.log(a);
		console.log(b);
		console.log(c);
	}
}