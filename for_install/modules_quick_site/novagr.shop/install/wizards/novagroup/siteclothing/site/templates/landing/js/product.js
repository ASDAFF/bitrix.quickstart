
var product = {
	
	CHANGE_URL	: 0,	// flag for not change URL first time in detail card via ajax request
	
	currentColorId: 0,
	currentSizeId: 0,
	currentPrice: 0,
	currentSet : [],
	productUrl : '',
	curPhotosSmall : [],
	curPhotosBig : [],
	curPhotosMiddle: [],
	curPhotosBigHeight : [],
	ajaxUrl : '',
	productId : '',
	messages: {},
    siteID: 0,
    offersSubsribed: [],
    isAuthorized:0,
    userEmail: '',
    subscribesFlag: false,
    tooltipSelector: '', // selector for tooltip div
    tooltipText: '',
    /**
     * 	1 - the image in a popup window on the whole screen (by default)
     *  2 - increase when the mouse on the picture
     *  3 - the image in a popup window is scaled to the size of the screen
     */
    detailCardView:1,
    /**
	 * returns class for subscr button(different for authorized and not users - different handlers)
	 * @return {string}
	 */
	getClassNotify: function() {
		var notifyClass = '';
		if (product.userEmail != '') {	
		//if (product.isAuthorized == 1) {
			notifyClass = 'authNotifySize2';
		} else {
			notifyClass = 'notify2';
		}
		return notifyClass;
	},
	changeRealSizes: function(sizeId) {
		$("#myTab li").removeClass('active');
        $("#li_"+sizeId).addClass('active');
        $('#myTabContent div.tab-pane').removeClass('active').removeClass('in');
        $('#myTabContent div#tab'+sizeId).addClass('active in');
	},
	/**
	 * called when you click on kvadritik with size. changing the current size, color shown only for desired size
	 * 
	 * @param {int} sizeId
	 */
	changeSize: function(sizeId) {
        $("#quickView").hide();
		product.currentSizeId = sizeId;
		// looking at proposals goods in the desired size and color
		if(product.CHANGE_URL)
			window.history.replaceState(null,null, product.productUrl +'?cs='+product.currentSizeId+'&#color-'+ product.currentColorId + '-' + product.productId);
		for (var i in product.currentSet) {
			
			if (product.currentSet[i].color == product.currentColorId 
				&& product.currentSet[i].size == product.currentSizeId) 
			{
                $("#notify_elem_id").attr('value', i );
				product.changePrice(product.currentSet[i].price, i);
				//changing pictures
				product.curPhotosBig = product.currentSet[i].curPhotosBig;
				product.curPhotosSmall = product.currentSet[i].curPhotosSmall;
				product.curPhotosMiddle = product.currentSet[i].curPhotosMiddle;
				product.changePhotos();
                $("#quickView").show();
                product.changeRealSizes(sizeId);
				return;
			}					
		}
        $("#quickView").show();
        product.changeRealSizes(sizeId);
	},
    checkSize: function(sizeId) {
        for (var i in product.currentSet) {

            if (product.currentSet[i].color == product.currentColorId
                && product.currentSet[i].size == sizeId)
            {
                return true;
            }
        }
        return false;
    },
	/**
	 * called when you click on kvadritik with color. changing the current color,shows sizes and photos
	 * only for this color
	 *   
	 * @param {int} colorId
	 * @param {bool} first 
	 * caused by the initialization, parameter means that will be searched all active size for the color
	 * with the current price and searched for the smallest size for illumination
	 * 
	 */
	changeColor: function(colorId, first) {
        $("#quickView").hide();
		product.currentColorId = colorId;
		if(product.CHANGE_URL)
			window.history.replaceState(null,null, product.productUrl+'?cs='+product.currentSizeId+'&#color-'+ product.currentColorId + '-' + product.productId);
		/**
		 *  ID of a suitable supply of goods, when the function is invoked as first == true
		 *  Looks for the smallest size sorting
		 *  
		 */
		var itemIndex = '';
		
		// looking at proposals goods with a given color and size
				
		// tp sort out - we are looking for the size of this color
		
		var activeSizes = []; // active sizes
		
		for (var i in product.currentSet) {
			var li = $('#li_'+product.currentSet[i].size);
			
			if (product.currentSet[i].color == colorId) {
				// color of tp coincided with the current color - size falls in the number of active 
				li.show();
				if (jQuery.inArray(product.currentSet[i].size, activeSizes) == -1) {
					//add to the array of active dimensions
					activeSizes.push(product.currentSet[i].size);
					
				}
			}
		}
		//a sign that the new color is the current size
		var activeSizeMatchFlag = false;
		// make inactive sizes not available in that color
		for (var i in product.currentSet) {
			
			var li = $('#li_'+product.currentSet[i].size);
			if (jQuery.inArray(product.currentSet[i].size, activeSizes) == -1) {
				// make the size of inactive
				//li.removeAttr("class").attr("data-placement", "top").attr("rel", "tooltip").attr("data-original-title", product.messages.NO_SIZE_LABEL).html('<span class="none-gt">'+product.currentSet[i].sizeName+'</span>');
				li.removeAttr("class").html('<span class="none-gt">'+product.currentSet[i].sizeName+'</span>');
				li.hide();
			} else {
							
				// size exist in nomenklatura
				if (product.currentSet[i].color == colorId) {
					if (product.subscribesFlag == true && product.currentSet[i].quantity <= 0) {

						li.removeAttr("class").attr("data-placement", "top").attr("rel", "tooltip2").attr("data-original-title", product.messages.SUBSCR_MESSAGE).html('<span class="none-gt"><a data-size="'+product.currentSet[i].size+'"  href="#tab'+product.currentSet[i].size+'" class="'+product.getClassNotify()+'" data-elem-id="'+i+'" >'+product.currentSet[i].sizeName+'</a></span>');
					} else {
						li.removeAttr("class").removeAttr("data-placement").removeAttr("rel").removeAttr("data-original-title").html('<a data-size="'+product.currentSet[i].size+'"  href="#tab'+product.currentSet[i].size+'">'+product.currentSet[i].sizeName+'</a>');
					}
					
					if (first) {
						// looking for the minimum (with minimal sorting) with the current price
						var curPriceItem = parseInt(product.currentSet[i].price);
	
						if (curPriceItem == product.currentPrice && product.currentSet[i].color == colorId) {
							// price matched, color matched - to find the smallest size
	
							if (product.currentSet[i].quantity>0) {
								if (product.currentSet[i].sizeSort>0 && itemIndex == '' ) {
									
									itemIndex = i;
								} else if (itemIndex > 0 
									&& product.currentSet[i].sizeSort < product.currentSet[itemIndex].sizeSort) {
									itemIndex = i;
	
								}
							}
						}
						
					} else {
						// current size
						if (product.currentSet[i].size == product.currentSizeId && product.currentSet[i].color == colorId)
						{
							itemIndex = i;
						}				
					
					} // end if (first) {
				}	
			}				
		} // end for (var i in product.currentSet) {
		
		$("[rel=tooltip]").tooltip({});
		
		if (itemIndex != '') {
			product.changePrice(product.currentSet[itemIndex].price, itemIndex);
			// changing photos
			product.curPhotosBig = product.currentSet[itemIndex].curPhotosBig;
			product.curPhotosSmall = product.currentSet[itemIndex].curPhotosSmall;
			product.curPhotosMiddle = product.currentSet[itemIndex].curPhotosMiddle;
			product.changePhotos();
			product.currentSizeId = product.currentSet[itemIndex].size;
			
			$('#li_'+product.currentSizeId).attr("class", "active");
            $('#myTabContent div.tab-pane').removeClass('active').removeClass('in');
            $('#myTabContent div#tab'+product.currentSizeId).addClass('active in');
			activeSizeMatchFlag = true;
		}
		// select the cell with the size if the current size matched with the current color
		
		if (activeSizeMatchFlag == false) {

			// if the new color is not the current size - we take the first of the list
			product.chooseFirstSize(); // changing size
            $('#myTabContent div.tab-pane').removeClass('active').removeClass('in');
            $('#myTabContent div#tab'+product.currentSizeId).addClass('active in');
							
		} // end if (activeSizeMatchFlag == false) {
        $("#quickView").show();
	},
	chooseFirstSize: function() {
        if(window.SET_PRODUCT_FIRST_SIZE===false)return false;
		var li_a = $('#myTab li').find('a').first();
		product.currentSizeId = li_a.data("size");
		var sizeId = li_a.data("size");
		product.changeSize(sizeId); // changing size
	},
	/**
	 * adds to the product offer in the product object
	 * 
	 * @param {int} itemId 
	 * @param {string} price 
	 * @param {int} colorId ID 
	 * @param {int} sizeId ID 
	 * @param {string} colorName 
	 * @param {string} colorPicture 
	 * @param {array} curPhotosSmall 
	 * @param {array} curPhotosBig 
	 * @param {string} sizeName
	 * @param {string} sizeSort
	 * @param {int} quantity
	 * @param {string} oldPrice
	 */
	addToSet: function(itemId, price, colorId, sizeId, colorName, colorPicture, curPhotosSmall, curPhotosBig, curPhotosBigHeight, sizeName, sizeSort, quantity, oldPrice, curPhotosMiddle) {
		if (!sizeSort) sizeSort = 0;
		
		product.currentSet[itemId] = { 'price' : price, 'color' : colorId, 'size' : sizeId, 'colorName' : colorName, 'colorPicture' : colorPicture, 'curPhotosSmall' : curPhotosSmall, 'curPhotosBig' : curPhotosBig, 'curPhotosBigHeight' : curPhotosBigHeight, 'sizeName' : sizeName, 'sizeSort' : sizeSort, 'quantity' : quantity, 'oldPrice' : oldPrice, 'curPhotosMiddle' : curPhotosMiddle };
	},
	
	/**
	 * Formatting the price of goods
	 * 
	 * @param {String} price 
	 * @return {String} 
	 */
	formatPrice: function(price) {
		var nStr = price.replace('.00',"");	
		nStr += '';
		x = nStr.split('.');
		x1 = x[0];
		x2 = x.length > 1 ? '.' + x[1] : '';
		var rgx = /(\d+)(\d{3})/;
		while (rgx.test(x1)) {
			x1 = x1.replace(rgx, '$1' + ' ' + '$2');
		}
		newPrice = x1 + x2;
		return newPrice;
	},
	checkCommentForm: function() { 
		var text = $("#REVIEW_TEXT").val();
		var error = [];
		
		if ( $('#REVIEW_AUTHOR').length ) {
			var name = $("#REVIEW_AUTHOR").val();
			if (name == "") {
				error.push(product.messages.ALERT_NAME);
                $("#controlGroupName").attr("class", "control-group error");
            } else {
                $("#controlGroupName").attr("class", "control-group");
            }
		}
		if (text == "") {
			error.push(product.messages.ALERT_MESSAGE);
            $("#controlGroupText").attr("class", "control-group error");
        } else {
            $("#controlGroupText").attr("class", "control-group");
        }
		
		var str_error = error.join("<br>");
		if (str_error != '') {
			$("#alert").attr("class", "alert alert-error").html(str_error);
			
			return false;	
		}
        product.showAjaxLoader();
		return true;
	},
    setSiteID: function(siteID) {
        this.siteID = siteID;
    },
    /**
     * функция скрывает или показывает стрелки в зависимости от текущей картинки в карусели
     * @param {int} current
     * @param {int} current
     */
    showHideArrows: function(curImageIndex, total) {
        if (curImageIndex==1) {
            $("#left-arr").hide();
        } else {
            $("#left-arr").show();
        }

        if (curImageIndex==total) {
            $("#right-arr").hide();
        } else {
            $("#right-arr").show();
        }
    },
	getComments: function(iNumPage) {
		if (!iNumPage) iNumPage = 1;
		
		var dataArr = {
			"AJAX_REFRESH"		: 1,
			'ELEMENT_ID'	: this.productId,
			'iNumPage'	: iNumPage,
            'siteID' : this.siteID
		}
		
		$.ajax({
			type			: "POST",
			url				: this.ajaxUrl,
			data			: dataArr,
			dataType	: "json",
			beforeSend	: function() {

                product.showAjaxLoader();
			},
			success: function (data, textStatus) {

                product.hideAjaxLoader();

                if(data && data.result)
                {
                    if (data.result == 'OK') {

                        $("#comments-list").html(data.commentsText);
                        //$("#controlGroupName").html(data.fields);
                       // $("#controlGroupEmail").html(data.fieldsEmail);
                        if (data.count>0) {
                            //$("#comment-refresh").show();
                        } else {

                            $('.comm').hide();
                        }

                    }
                }
		    } 
		});
	},
	/**
	 * change prices and links to buy in the blockd
	 * 
	 * @param {String} price - new price
	 * @param {Int} itemId - tp
	 */
	changePrice: function(price, itemId) {
		
		var oldPrice = product.currentSet[itemId].oldPrice;
        oldPriceFloat = oldPrice.replace(/ /gi,"");
        oldPriceFloat = parseFloat(oldPriceFloat);

        priceFloat = price.replace(/ /gi,"");
        priceFloat = parseFloat(priceFloat);
		
		var sumClass = "default-value";
		// if count < 1 then hide the price and buy button
		
		//$('#notifyme').hide();
		
		if (product.currentSet[itemId].quantity < 1) {

			$('#notifyme').show();
			//$("#fields").show();
            $("#fields").hide();
			$('#sum').text(product.messages.NO_IN_STOCK);
			$('#old-price').text('');
			//$('.addToBasket, .actual-price .basket-tab').hide();
            $("#buy-btn-span").hide();
			//$(".oneClick").hide();
			sumClass = 'discount';
		} else {

            $('#notifyme').hide();
			var pr = product.formatPrice(price);
			
			$('#sum').text(pr);
			// if the price is at a discount then show old strikeout price
			if ( oldPriceFloat > priceFloat ) {
				sumClass = 'discount';
				var prOld = product.formatPrice(oldPrice);

				$('#old-price').text(prOld);
			} else {
                $('#old-price').text('');
            }

            if (pr == '' && product.isOpt == 1) {
                sumClass = 'discount';
                $('#sum').text(product.messages.NO_IN_STOCK);
                $("#buy-btn-span").hide();
                $("#fields").hide();
            } else {
                $("#buy-btn-span").show();
                $("#fields").show();
            }

		}

		$('#sum').attr("class", sumClass);

        //$("#shelve-product").attr("data-elem-id", itemId);
	},
	
	/**
	 * pictures changing
	 */
	changePhotos: function(arrPic) {
		var arrPic = arrPic || 0;

		var bigPhotosHtml = '', smallPhotosHtml = '';
		var firstPicSrc = '';
		var j=1;
		var continuePicture = '';
		if (product.detailCardView == 3) {
			
			if (arrPic !== 0) {

				continuePicture = arrPic['big'];
				var style = '';
				bigPhotosHtml += '<a class="detailLink fancybox" rel="gallery" href="'+arrPic['big']+'" '+style+'><img id="detailImg'+j+'" src="'+arrPic['middle']+'" width="450" height="373" alt="" /></a>';
				smallPhotosHtml += '<img data-big-pic="'+arrPic['big']+'" class="previewImg" src="'+arrPic['small']+'" width="90" height="120" alt="" href="'+arrPic['middle']+'"  />';
				j=2;
			}
			
			
			for (var i in product.curPhotosBig) {
				
				if (continuePicture == product.curPhotosBig[i]) continue;
				if (j==1) {
					var style = '';
				} else {
					var style = 'style="display:none;"';
				}
				
				bigPhotosHtml += '<a class="detailLink fancybox" rel="gallery" href="'+product.curPhotosBig[i]+'" '+style+'><img id="detailImg'+j+'" src="'+product.curPhotosMiddle[i]+'" width="450" height="373" alt="" /></a>';
				smallPhotosHtml += '<img data-big-pic="'+product.curPhotosBig[i]+'" class="previewImg" src="'+product.curPhotosSmall[i]+'" width="90" height="120" alt="" href="'+product.curPhotosMiddle[i]+'"  />';
				
				j++;
			}
		} else {	
			for (var i in product.curPhotosBig) {
				
				if (j==1) {
					firstPicSrc = product.curPhotosBig[i];	
					bigPhotosHtml += '<a class="detailLink" id="fLinkPic" href="#myModal" role="button" ><img data-big-pic="'+product.curPhotosBig[i]+'" id="detailImg'+j+'" src="'+product.curPhotosMiddle[i]+'" data-height="'+product.curPhotosBigHeight[i]+'" width="450" height="580" alt="'+product.messages.PRODUCT_NAME+'" /></a>';
				}
				
				smallPhotosHtml += '<img data-big-pic="'+product.curPhotosBig[i]+'" class="previewImg" src="'+product.curPhotosSmall[i]+'" width="86" height="114" alt="'+product.messages.PRODUCT_NAME+'" href="'+product.curPhotosMiddle[i]+'"  />';
				
				j++;
			}
		}
		$("#photos").html(bigPhotosHtml);
		
		if (continuePicture == '' ) $("#thumbs").html(smallPhotosHtml);
		
		if (product.detailCardView == 2) {
			$('#photos').zoom({url: firstPicSrc});
		} else if (product.detailCardView == 3) {
			product.setFancyBox();
		}	
		
	},
	/**
	 * handler click the link to buy the detail card
	 * @param {object} obj - Link-button Buy
	 */
	buyClick: function(obj) {
		var elemId = $(obj).attr('data-elem-id');
      	if (elemId > 0) {

            var dataArr = {'ADD2BASKET' : '1', 'elemId' : elemId };
            var action = $(obj).attr('data-action');
            if (action == 'addToShelve') {
                dataArr['act'] = 'addToShelve';
                product.tooltipSelector = "#box-shelve";
                var textAddedToCart = product.messages.ADDED_TO_SHELVES;
            } else {
                product.tooltipSelector = "#buy-popup";
                var textAddedToCart = product.messages.PRODUCT_ADDED_TO_CART;
            }

      		$.ajax({
    			type			: "POST",
    			url				: this.ajaxUrl,
    			data			: dataArr,
    			dataType	: "json",
    			success: function (json, textStatus) {

                    product.currentSet[elemId].quantity = 1;

    				if (json.status == 'ERROR' && json.type == 'PRODUCT_EXCEEDED_LIMIT') {
                        product.tooltipText = product.messages.PRODUCT_ALREADY_IN_CART;

                        //$("#message-demo").text(product.messages.PRODUCT_ALREADY_IN_CART);

    				} else if (json.status == 'ERROR' && json.type == 'PRODUCT_NOT_AVAILABLE') {

                        product.currentSet[elemId].quantity = 0;
                        product.changeColor(product.currentColorId);
                        $('#li_'+product.currentSizeId+' a').click();
                        return;
                    } else if (json.status == 'OK') {

    					// Highlights the basket
                        product.tooltipText = textAddedToCart;
    					//$("#message-demo").text(product.messages.PRODUCT_ADDED_TO_CART);
    					$("#cart_line_1").html($(json.html).html()).addClass('basket-active');
                        UpdateBasketAfterLoadOrderList();
                        setTimeout(product.stopCartLight, 2500);
    				}

                    $(product.tooltipSelector+ " div").text(product.tooltipText); // insert message in tooltip
    				$(product.tooltipSelector).show(); // show and hide tooltip
          			setTimeout(product.closeTooltip, 2500);
    		    }      			
    		});
      	}
	},
	
	closeTooltip: function() { 
		$(product.tooltipSelector).hide();
	},	
	stopCartLight: function() {
		$("#cart_line_1").removeClass('basket-active');
	},
	enableSmiles: function() {
		// require SITE_TEMPLATE_PATH . '/js/comments.js'
		
		var jcEditor = new JCommentsEditor('REVIEW_TEXT', true);
		jcEditor.initSmiles(JAVASCRIPT_SITE_DIR+'include/images/smiles');
		jcEditor.addSmile(':D','laugh.gif');
		jcEditor.addSmile(':lol:','lol.gif');
		jcEditor.addSmile(':-)','smile.gif');
		jcEditor.addSmile(';-)','wink.gif');
		jcEditor.addSmile('8)','cool.gif');
		jcEditor.addSmile(':-|','normal.gif');
		jcEditor.addSmile(':oops:','redface.gif');
		jcEditor.addSmile(':sad:','sad.gif');
		jcEditor.addSmile(':roll:','rolleyes.gif');
	},
	chooseFirstColor: function() {
		//set color by anchor
		if (window.SET_PRODUCT_FIRST_PHOTO==false)
        { /*nothing*/ } else {
            var getAnchor = location.hash;
            if (getAnchor!="" && $('.color-ch '+getAnchor+'-set-by-hash').is('button')) {
                $(window).ready(function(){
                    $(getAnchor+'-set-by-hash').click();
                });
            } else {
                $('.color-ch button:first').click();
            }
            product.chooseFirstSize();
        }
	},
	/**
	 * return subscribed items
	 */
	getSubscribed: function() {
		/*var dataArr = {'action' : 'getSubscribed'};
		$.ajax({
			type			: "POST",
			url				: this.ajaxUrl,
			data			: dataArr,
			dataType	: "json",
			success: function (data, textStatus) { 
				
				if (data && data.isAuthorized == 1) {

					product.isAuthorized = 1;
					//product.userEmail = data.userEmail;
				}
		    } 
		});*/
	},
	setFancyBox: function() {
		$(".fancybox").fancybox({ helpers:  { title:  null } });
		return false;
	},
    showAjaxLoader: function() {
        var height = $(".page-container").height();

        var waitHtml = '<div class="centerbg1" id="preloaderbg" style="display: block;height: '+height+'px"> \
          <div class="centerbg2"> \
            <div id="preloader"></div> \
          </div> \
        </div>';
        $(waitHtml).prependTo('body');
    },
    hideAjaxLoader: function () {
        $("#preloaderbg").remove();
    },
	/**
	 * Initialization of the product in detail. card
	 * @param {int} itemId ID  
	 * @param {string} price 
	 * @param {int} colorId 
	 * @param {int} sizeId ID 
	 * @param {string} colorName 
	 * @param {string} colorPicture 
	 * @param {array} curPhotosSmall 
	 * @param {array} curPhotosBig
	 * @param {string} sizeName 
	 * @param {string} sizeSort
	 * @param {int} quantity -
	 * @param {string} oldPrice
	 * @param {string} ajaxUrl
	 * @param {int} productId
	 * @param {array} messages
	 * @param {int} commentsFlag
	 * @param {string} productUrl
	 * @param {string} subscribesFlag  
	 */
	init: function (itemId, price, currentColorId, currentSizeId, colorName, colorPicture, curPhotosSmall, curPhotosBig, curPhotosBigHeight, sizeName, sizeSort, quantity, oldPrice,  ajaxUrl, productId, messages, commentsFlag, productUrl, subscribesFlag, curPhotosMiddle, detailCardView, userArr) {

		if (!sizeSort) sizeSort = 0;
		var self = this;

        self.isOpt = userArr["is_opt"];
		self.messages = messages;
		self.ajaxUrl = ajaxUrl;
		self.productId = productId;	
		self.subscribesFlag = subscribesFlag;
		
		//self.userEmail = JW_USER_EMAIL; // from header
		self.currentPrice = parseInt(price);
		self.productUrl = productUrl;
		self.currentColorId = currentColorId;
		self.currentSizeId = currentSizeId;
		
        self.currentSet = []; //set as new Array
		self.currentSet[itemId] = { 'price' : price, 'color' : currentColorId, 'size' : currentSizeId, 'colorName' : colorName, 'colorPicture' : colorPicture, 'curPhotosSmall' : curPhotosSmall, 'curPhotosBig' : curPhotosBig, 'curPhotosBigHeight' : curPhotosBigHeight, 'sizeName' : sizeName, 'sizeSort' : sizeSort, 'quantity' : quantity, 'oldPrice' : oldPrice, 'curPhotosMiddle' : curPhotosMiddle };
		// assign pictures
		self.curPhotosBig = curPhotosBig;
		self.curPhotosSmall = curPhotosSmall;
		self.curPhotosBigHeight = curPhotosBigHeight;
		self.curPhotosMiddle = curPhotosMiddle;
		self.detailCardView = detailCardView;
		//self.changePhotos();
		if (commentsFlag == 1) {
			//self.enableSmiles();
            self.getComments();
		}
		//if (subscribesFlag == true) {
		//	self.getSubscribed();
		//}

	    // click handler in color
	    $("#color-ch button").live("click", function(){
	        var colorId = $(this).data("color");
	        product.changeColor(colorId);

	    });
        // click handler send product to shelves
        $("#shelve-product").on("click", function(){
            product.buyClick(this);
            return false;
        });

	    if (self.detailCardView == 1) {
	    	//Click on the picture - a popup modal box with a carousel

            $("#zoom").on("click", function(){
                $("#fLinkPic").trigger("click");
                return false;
            })

            $("#fLinkPic").die('click');
		    $("#fLinkPic").live('click',function() {
		
		        product.showAjaxLoader();
		        var picHTML = '';
		        var picArr = [];
		        //var curPic = $(this).find("img").attr("src");
		        var curPic = $(this).find("img").attr("data-big-pic");
		        var total = $('#thumbs img').length;

		        var title = $('#product-name').html();
		        $('#thumbs img').each(function(i, val) {
		            picArr[i] = $(this).data("big-pic");
		        });
		        //console.log(picArr);
		        var j = 1;
		        var curImageIndex = 1;

		        for (var i in picArr) {
		            var active = '';
	
		            if (picArr[i] == curPic) {
		                active = 'active ';
		                curImageIndex = j;
		            }

		            picHTML += '<div class="'+active+'item">' +
		                '<div class="modal-header">' +
		                '<button type="button" class="close" data-dismiss="modal">&times;</button>' +
		                '<h3>'+title+' </h3>' +
		                '</div>' +
		                '<div class="modal-body">' +
		                '<img alt="" src="'+picArr[i]+'">' +
		                '</div>' +
		                '<div class="modal-footer">'+product.messages.CAROUSEL_LABEL1+' <span class="curImg">'+j+'</span> '+product.messages.CAROUSEL_LABEL2+' <span class="totalImg">'+total+'</span></div>' +
		                '</div>';
		            ++j;
		        }

		        // arrows show, depending on the current page
		        product.showHideArrows(curImageIndex, total);

		        $("#carousel-inner").html(picHTML);

		        var $myCarousel = $('#myCarousel').carousel({'interval': false});
		        //hide the arrow when the last picture
		        $myCarousel.on('slid', function() {

		            var curImageIndex = $("#carousel-inner .active .curImg").html();
                    product.showHideArrows(curImageIndex, total);
		            product.showAjaxLoader();

		            var preloadImage = new Image();
		            preloadImage.onload = function(){
                        product.hideAjaxLoader();
		                var marginLeft = ((preloadImage.width+30)/2);
		                $("#myModal").css('marginLeft', "-"+marginLeft+"px");
		            }
		            preloadImage.src = $("#myModal .carousel-inner .active .modal-body img").attr("src");

		        });

		        var preloadImage = new Image();
		        preloadImage.onload = function(){
		            product.hideAjaxLoader();
		            var marginLeft = ((preloadImage.width+30)/2);
		            $("#myModal").modal({'marginLeft': marginLeft});
		        }
		        preloadImage.src = curPic;
		        return false;
		    });
	    	
	    	// handler hover on the preview gallery
		    $("#thumbs img").live("mouseenter" ,function() {
		        //document.getElementById('detailImg1').src = $(this).data('big-pic');
		        $('#detailImg1')
		            .attr("src", $(this).attr('href'))
		            .attr("data-big-pic", $(this).data('big-pic'));
		    });

		    // when you click on a small picture pops up large
            $("#thumbs img").die('click');
            $("#thumbs img").live('click', function(){

		        $("#detailImg1").trigger('click');
		        return false;
		    });
		    
	    } else if (self.detailCardView == 2) { 
	    	 // handler hover on the preview gallery
            $("#thumbs img").die("mouseenter");
            $("#thumbs img").live("mouseenter" ,function() {
		        //document.getElementById('detailImg1').src = $(this).data('big-pic');
		        $('#detailImg1')
		            .attr("src", $(this).attr('href'))
		            .attr("data-big-pic", $(this).data('big-pic'));
		        
		     // prescribe zoom handler for the picture
				$('img.zoomImg').remove();
				$('#photos').zoom({url:  $(this).data('big-pic')});
		    });

	    } else if (self.detailCardView == 3) {

            $("#thumbs img").die("mouseenter");
	    	$("#thumbs img").live("mouseenter" ,function() { 
	    		
	    		var arrPic = [];
	    		arrPic['small'] = $(this).attr('src');
	    		arrPic['middle'] = $(this).attr('href');
	    		arrPic['big'] = $(this).data('big-pic');	
	    		product.changePhotos(arrPic);

		    });
	    	
	    	// when you click on a small picture pops up large	
            $("#thumbs img").die('click');
            $("#thumbs img").live('click', function(){
							
				$("#detailImg1").trigger('click');
				return false;
			});
	    } 	
	}		
}
