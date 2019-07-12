var dcProduct = {
	ajaxHandler: '',
	catalogIblockID: '',
	offersIblockID: '',
	htmlNewSize: '',
	choosedPhotos: {},
	choosedSizes: {},
	offerCounter: 0,
	errorScrollElemId: '',
	pictureCounter: 0,
	selectColors:[], // colors for select
	colors: [], // all colors
	messages: [],
	strReplace: function(search, replace, subject) {
		return subject.replace(new RegExp (search, 'g'), replace);
	},
	init: function (ajaxHandler, catalogIblockID, offersIblockID, messages) {
		var self = this;
		self.messages = messages;
		self.catalogIblockID = catalogIblockID;
		self.offersIblockID = offersIblockID;
		self.ajaxHandler = ajaxHandler;
		$('#products-t a.pic_popup').popover({ title : '', trigger : 'hover', html:true });

		if ($.cookie('show_foto') == 1) {
			$('#products-t a.pic_popup').popover('disable');
		}

		$('#optionsCheckbox').change(function() {
			if (this.checked == true) {
				$.cookie("show_foto", "1", { path : "/managers-cabinet"});
				$( "#products-t td.photo-td" ).attr("class", "photo-td");
				$( "#products-t th.photo-td" ).attr("class", "photo-td");
				$('#products-t a.pic_popup').popover('disable');
			} else {
				$.cookie("show_foto", "0", { path : "/managers-cabinet"});
				$( "#products-t td.photo-td" ).attr("class", "photo-td hidden");
				$( "#products-t th.photo-td" ).attr("class", "photo-td hidden");
				$('#products-t a.pic_popup').popover('enable');
			}
		});
		// chose picture event
		$('#fileInput').live('change',function(){

			$("#show_picture").val("1");  // set flag that need preview only
			$('#productEditForm').submit(); // get preview
		})
		
		$('#price_product').live('change',function(){

			var priceAll = $(this).val(); 
			$(".priceOf").each(function (i) {
				var value = $(this).val();
				if (value == "" || value == 0) {
					$(this).val(priceAll);
				}			
			});	
		});		
		
		$('#saveProductBtn').live('click', function() {
			dcProduct.preSaveProduct();
		});

		 $('#firstBtn').live('click', function(){
	    	$('#modal_div_confirm').modal('hide');// close window after edit
	    	location.reload();
			return false;
	    });
		$('#secondBtn').live('click', function(){
			dcProduct.addProductForm();
			$('#modal_div_confirm').modal('hide');
			return false;
		});
			
		// удаление фото
		$("a.delPhoto").live('click', function() {
			var photoID = $(this).data("photo-id");
			for (var j in dcProduct.choosedPhotos) {

				if (j == photoID) {
					delete dcProduct.choosedPhotos[j];
					$(this).parents(".photo_block").remove();
					
					return;
				}
			}
			self.showEditForm($(this).data("item-id"), $(this).data("iblock-id"));
			return false;
		});
		/**
		 * show add form
		 */
		$(".addForm").on('click', function() {

			dcProduct.addProductForm();
			return false;
		});

		$('.set-active').change(function() {

			var product_id = $(this).data('product-id');
			$.ajax({
				type		: "POST",
				url			: self.ajaxHandler,
				data		: { "state": this.checked, "action": 'change_active', "product_id" : product_id  },
				dataType	: "JSON",
				beforeSend	: function() {
					self.showAjaxLoader();
				},
				success		: function(json){
					self.hideAjaxLoader();
					if (json.result == 'OK') {
						if (json.state == "Y") {
							$( "#price-s-" + product_id ).removeClass("hidden");
							$( "#snyat-s-" + product_id ).addClass("hidden");
						} else {
							$( "#price-s-" + product_id ).addClass("hidden");
							$( "#snyat-s-" + product_id ).removeClass("hidden");
						}
					}
				}
			});
		});
		$('a.editlink').live('click', function() {
			self.showEditForm($(this).data("item-id"), $(this).data("iblock-id"));
			return false;
		});

		$("#ListTree a").on('click', function() {

			$("#section_name").text($(this).text());
			$("#section").val($(this).data('section-id'));
			$("#ListTree").modal("hide");
			return false;
		});

		$("#photo_block select").live('change', function(){

			var value = $(this).val();
			var photoID = $(this).data("photo-id");
			if (value != 0) {
				for (var j in dcProduct.choosedPhotos) {
					
					if (j == photoID) {
						dcProduct.choosedPhotos[j].elem_color = value;
						
						return;
					}
				}
			}
		});
		// handler of navigation in sizes window
		$("#navigate-size a.navig").live('click', function(){
			var secid= '';
			
			var iblid = $(this).attr('iblock');
			if (this.hasAttribute("secid")) {
				
				secid = $(this).attr('secid');
			}
			dcProduct.GetElementsAjax($(this).attr('nPageSize'), $(this).attr('inumpage'), iblid, secid);
			return false;
		});
		// handler of click on size section
		$("#size_select").live('click', function(){
						
			$.ajax({
				type: "POST",
				url: dcProduct.ajaxHandler,
				data: {
					'action'				: 'get_classificator_popup',
					'name'					: 'add_size',
					'AJAX'					: "Y",					
					'iblid'					: $(this).attr('iblid'),
					'secid'					: $(this).val(),
					'currentValues'			: dcProduct.getCurrentSizesValues()
				},
				dataType:"json",
				success: function(json){
					if (json.result == 'OK') {
						$("#modal_div").html(json.html);
					}
				}
			});
			return false;
		});
		$("#way .btn-success, #way tr").live('click', function() {
			var sizeID = $(this).data('size-id');
	
			var btn = $("#btn"+sizeID);
			$(btn).toggleClass("active");
			if ($(btn).hasClass('active')) {
				$(btn).text(dcProduct.messages.RECONSIDER);
				dcProduct.choosedSizes[sizeID] = {};
				dcProduct.choosedSizes[sizeID].id = sizeID;
				dcProduct.choosedSizes[sizeID].name = $(this).data('size-name');
			} else {
				$(btn).text(dcProduct.messages.LABEL_CHOOSE);
				delete dcProduct.choosedSizes[sizeID];
			}
			$("#size-tr-"+sizeID+" td").toggleClass("selected");
			return false;
		});
		// click on button Chose selected sizes
		$("#btnChoose").live('click', function() {

			$('#modal_div').modal('hide');

			var htmlAdd = '';
			for (var i in dcProduct.choosedSizes) {
				var sizeID = dcProduct.choosedSizes[i].id;
				var sizeName = dcProduct.choosedSizes[i].name;
				var values = self.strReplace('###', sizeID, self.htmlNewSize);
				
				htmlAdd += '<tr id="tr-for-size-'+sizeID+'" class="sizeTR" data-size-id="'+sizeID+'"><td>'+sizeName+'<input type="hidden" value="'+sizeID+'" name="DIMENSION_STD['+sizeID+']"></td> \
			<td class="size_tabl">'+values+'</td> \
			<td class="wrap-choice"> \
				<div id="wrapchoice_'+sizeID+'"> \
				</div> \
				<p class="valid-f "><span name="colorSizeInput'+sizeID+'" id="colorSizeInput'+sizeID+'" value="" data-val="true" data-val-required="'+dcProduct.messages.LABEL_FIL_FIELD+'"> \
				<a class="colorAdd name-label" data-size-id="'+sizeID+'" href="#">'+dcProduct.messages.LABEL_ADD_COLOR+' <span class="arrow-required">*</span></a><span class="field-validation-valid help-inline" data-valmsg-for="colorSizeInput'+sizeID+'" data-valmsg-replace="true"></span></span></td> \
				<td class="tooltip-demo"><a data-original-title="'+dcProduct.messages.LABEL_DEL+'" data-placement="top" rel="tooltip" class="btn btn-danger del-size" data-size-id="'+sizeID+'" href="#"><i class="icon-remove icon-white"></i></a></td></tr>'
			}
			if (htmlAdd != '') {
				
				var html = $(htmlAdd).appendTo("#sizes_body");
				
				html.find(".colorAdd").each(function (i){

					$(this).trigger("click");
				});
			}
			if ($("tr.sizeTR").length) {
				
				dcProduct.addSizeButtonOk();
			}
			return false;
		});

		// del size
		$(".del-size").live('click', function() {

			$("#tr-for-size-"+$(this).data('size-id')).remove();
			// generate array with colors
			dcProduct.selectColorsArrGenerate();
			return false;
		});
		// del color in size
		$('.dell-choice a').live('click', function() {
			$(this).parents(".table-choice").remove();
			//generate array with colors
			dcProduct.selectColorsArrGenerate();

			return false;
		});
		
		// add color in size
		$('a.colorAdd').live('click', function() {
			var sizeID = $(this).data("size-id");
			dcProduct.offerCounter++;
			var offerNew = dcProduct.offerCounter;
			var allColors = '';
			for (var i in dcProduct.colors) {
				allColors += '<option value="'+ dcProduct.colors[i].ID +'">'+ dcProduct.colors[i].NAME +'</option>'
			}
			var html = $('<div class="table-choice"> \
						<div class="dell-choice"><a class="btn btn-danger" href="#">x</a></div> \
						<table class="my-table"> \
						<tbody><tr> \
						<td> \
						<div class="control-group my-td"> \
							<label>'+dcProduct.messages.AJAX_FORM_COLOR+'</label> \
							<div class="controls"> \
								 <select data-title-mess="'+dcProduct.messages.AJAX_FORM_TITLE_MESS+'" data-add-label="'+dcProduct.messages.ADD_VALUE_LABEL+'" data-send-btn="'+dcProduct.messages.LABEL_BTN_SEND+'" name="colorOffer['+sizeID+']['+offerNew+']" class="span3 colorSelect" data-color-id="" data-color-name="" data-add-mess="'+dcProduct.messages.AJAX_FORM_ADD_COLOR+'" data-search-container="add_color'+offerNew+'" data-ajax-handler="'+dcProduct.ajaxHandler+'">'+allColors+'</select></div> \
						</div> \
						<div class="control-group my-td"> \
							<label>'+dcProduct.messages.AJAX_FORM_QUANTINY+'</label> \
							<div class="controls"> \
								<input type="text" value="" name="quantityOffer['+sizeID+']['+offerNew+']" class="span1"> \
							</div> \
						</div> \
							<div class="valid-f price-valid"> \
							<div class="control-group my-td"> \
								<label>'+dcProduct.messages.AJAX_FORM_PRICE+' \
								<span class="arrow-required">*</span></label> \
								<div class="controls"> \
									<input class="span1 priceOf" type="text" data-val-required="'+dcProduct.messages.AJAX_FORM_FILL_FIELD+'" data-val="true" value="" name="priceOffer['+sizeID+']['+offerNew+']"> \
									</div> \
							</div> \
						</div></td></tr></tbody></table></div>').appendTo("#wrapchoice_"+sizeID);

			html.find("select").select2({formatNoMatches:dcProduct.formatNoMatchesCustom }).on('selected', function() {
  					dcProduct.selectColorsArrGenerate();
  			});;
						
			return false;
		});
		$("#choose_size").live('click', function() {
			
			var data = {
				'name': 'add_size',
				'action': 'get_classificator_popup',
				'offersIblockID' : self.offersIblockID,
				'currentValues'			: dcProduct.getCurrentSizesValues()
			};
			$.post(self.ajaxHandler, data, function(json) {
				// show form choose in classificator
				if (json.result == 'OK') {
					$("#modal_div").html(json.html);
					$("#modal_div").modal('show');
					dcProduct.htmlNewSize = json.htmlNewSize;
					
					dcProduct.choosedSizes = {}; // set null

				}
			}, "json");
			return false;
		});
	},
	/**
	 * generates array with color with can be chosed during photo adding
	 *  
	 */
	selectColorsArrGenerate: function() {
		dcProduct.selectColors = [];
		$("tr.sizeTR").each(function (i){
					
			$(this).find("select.colorSelect").each(function (i){
			
				var val = $(this).select2("val");
		
				if (val != '') {								
					dcProduct.selectColors[val] = dcProduct.findColorNameById(val);
				}
			});
		});
	},	
	findColorNameById: function(id) {
		for (var i in dcProduct.colors) {
			if (id == dcProduct.colors[i].ID) {
				return dcProduct.colors[i].NAME;
			}
		}
		return '';				
	},
	getCurrentSizesValues: function() {
		var currentValues = [];
		$("tr.sizeTR").each(function (i){
			
			currentValues.push($(this).data("size-id"));
		})	
		return currentValues.join(',');
	},
	getColorsOptions: function() {
		var result = '';

		for (var i in dcProduct.selectColors) {
			result += '<option value="'+i+'">'+dcProduct.selectColors[i]+'</option>';
		}
 		return result;
	},
	GetElementsAjax: function(nPageSize, iNumPage, iblock, secid) {
		$.ajax({
			type: "POST",
			url: dcProduct.ajaxHandler,
			data: {
				'AJAX'					: "Y",
				'nPageSize'				: nPageSize,
				'iNumPage'				: iNumPage,
				'iblid'					: iblock,
				'secid'					: secid,
				'currentValues'			: dcProduct.getCurrentSizesValues()
			},
			dataType:"json",
			success: function(json){
				if (json.result == 'OK') {
					$('#way').html(json.html);
					// light needle buttons
					dcProduct.sizesButtonsCheck();
				}
			}
		});
	},
	sizesButtonsCheck: function() {

		var sizesIDS = [];
		for (var i in dcProduct.choosedSizes) {
			sizesIDS.push(dcProduct.choosedSizes[i].id);
		}
		$("#way tr").each(function (i){
			var curSize = $(this).data("size-id");
			if (curSize && jQuery.inArray(curSize, sizesIDS) != -1) {
				var btn = $("#btn"+curSize);
				$(btn).text(dcProduct.messages.LABEL_CHANGE_MIND).addClass('active');
				$(this).find("td").addClass("selected");
			}		
		});
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
	hideAjaxLoader: function() {
		$("#preloaderbg").remove();
	},
	/**
	 * prevpare product to save
	 */
	preSaveProduct: function() {
		$("#show_picture").val("0"); // set  show_picture value == 0
		// place photo object in form
		
		$("#photo_object").val(JSON.stringify(dcProduct.choosedPhotos));		
	},
	fieldError: function(stringID, addClass) {
		var nameInput = $(stringID);
		nameInput.parents(".control-group").attr("class","control-group error");
		var errClass = "input-validation-error"
		if (addClass) errClass = addClass + ' ' + errClass;
		nameInput.attr("class", errClass);
		
	},
	fieldOK: function(stringID, addClass) {
		var nameInput = $(stringID);
		nameInput.parents(".control-group").removeClass("error");
		nameInput.attr("class", addClass);
	},
	/**
	 * validate form
	 */
	ValidateEditForm: function() {
		dcProduct.errorScrollElemId = '';
		var show_picture = $("#show_picture").val();
		var error_str = '';
		var error = [];
		if (show_picture == 0) {

			if ($("#name_product").val() == "") {
				dcProduct.makeErrorScrollElem("#name_product");
				dcProduct.fieldError("#name_product", "span5");
				error.push(dcProduct.messages.WARNING_NAME);
			} else {
				dcProduct.fieldOK("#name_product", "span5");
			}
			/*if ($("#price_product").val() == "") {
				dcProduct.fieldError("#price_product", "span1");
				error.push(dcProduct.messages.WARNING_PRICE);
			} else {
				dcProduct.fieldOK("#price_product", "span1");
			}*/
			if ($("#section").val() == "") {
				$("#addGroup").addClass("error");
				$("#section").parents(".control-group").addClass("error");
				
				error.push(dcProduct.messages.WARNING_GROUP);
			} else {
				$("#addGroup").removeClass("error");
				$("#section").parents(".control-group").removeClass("error");
				
			}
		
			var brandInput = $('.select2-container input[name="brand_id"]');

			if (brandInput.val() == "") {
				$("#brandLabel").addClass("error");
				error.push(dcProduct.messages.WARNING_BRAND);
			} else {
				$("#brandLabel").removeClass("error");
			}
			var materInput = $('.select2-container input[name="material_id"]');

			if (materInput.val() == "") {
				$("#materLabel").addClass("error");
				error.push(dcProduct.messages.WARNING_MATERIAL);
			} else {
				$("#materLabel").removeClass("error");
			}
			
			if ($("#photo_block select").length) {
				$("#photo_block select.selectpicker").each(function (i){

					if ($(this).val() == 0) {
						$(this).parents(".control-group").find(".lab-left").addClass("error");
						error.push(dcProduct.messages.WARNING_COLOR);
					} else {
						$(this).parents(".control-group").find(".lab-left").removeClass("error");
					}
				});
			}
			//проверяем есть ли размеры
			var sizeStr = dcProduct.getCurrentSizesValues();
			if (sizeStr  == "") {
				$("#choose_size").removeClass("btn-info").addClass("btn-danger").text(dcProduct.messages.OBLIGATORY_ADD_SIZE);
				error.push(dcProduct.messages.WARNING_SIZE);
			} else {
				dcProduct.addSizeButtonOk();
			}
			
			// check if color filled in sizes
			if ($("tr.sizeTR").length) {
				
				$("tr.sizeTR").each(function (i){
					var errorColor = false;
					
					$(this).find("select.colorSelect").each(function (i){
						
						var value = $(this).select2("val");
						
						if (value == 0 || value == "") {
							
							errorColor = true;
							return false;
						}
					});

					if (errorColor == true) {
						error.push(dcProduct.messages.WARNING_COLOR);
						$(this).find(".colorAdd").addClass("error");

					} else {
						$(this).find(".colorAdd").removeClass("error");
						
					}
					
					// check price field
					$(this).find(".priceOf").each(function (i) {
						var value = $(this).val();
						if (value == "") {
							error.push(dcProduct.messages.WARNING_PRICE_TP);
							$(this).parents(".control-group").addClass("error");
						}
					});					
				});
			}
			// check product photo
			if ($("#photo_block img").length == 0 ) {
				$("#fileLabel").addClass("error");
				error.push(dcProduct.messages.WARNING_PHOTO);
			} else {
				$("#fileLabel").removeClass("error");
			}
		}
		
		var str_error = error.join("\n");
		if (str_error != '') {
			//$.scrollTo(dcProduct.errorScrollElemId, 800, {} );
			//alert(str_error);
			return false;
		}
		dcProduct.showAjaxLoader();
		return true;
	},
	makeErrorScrollElem: function(id) {
		if (dcProduct.errorScrollElemId == '') {
			dcProduct.errorScrollElemId = id;
		}
	},
	addSizeButtonOk: function() {
		$("#choose_size").addClass("btn-info").removeClass("btn-danger").text(dcProduct.messages.LABEL_ADD_SIZE);
	},	
	addProductForm: function() {
		dcProduct.showEditForm(0, dcProduct.catalogIblockID);
	},
	formatNoMatchesCustom: function () {
		return dcProduct.messages.NO_MATCHES_FOUND;
	},
	showEditForm: function (product_id, iblid) {

		var action = 'get_edit_form';
		if (product_id == 0) action = 'get_add_form';
		var data = { "action" : action, "product_id" : product_id, "iblid" : iblid };
		dcProduct.showAjaxLoader();
		$.post(this.ajaxHandler, data, function(res) {
			dcProduct.hideAjaxLoader();
			$("#editItem").html(res.html);
			
			$("#editItem").modal({ keyboard: false }).modal('show');
			$('.selectpicker').selectpicker('hide');
			
			$("#brand_id").select2({formatNoMatches:dcProduct.formatNoMatchesCustom });
			if (res.brand.ID != '') {
				$("#brand_id").select2("val", res.brand.ID);
			}
			$("#material_id").select2({formatNoMatches:dcProduct.formatNoMatchesCustom });
			if (res.material.ID != '') {
				$("#material_id").select2("val", res.material.ID);
			}
			
			$(".table-choice select.colorSelect").each(function (i){
				$(this).select2({formatNoMatches:dcProduct.formatNoMatchesCustom }).on('selected', function() {
  					dcProduct.selectColorsArrGenerate();
  				});
			});
			
			// set initial values in lists
			//dcProduct.choosedSizes = res.sizes;
			dcProduct.choosedPhotos = res.photos;
			dcProduct.colors = res.colors;
			
			dcProduct.selectColorsArrGenerate();
			var htmlForSelects = dcProduct.getColorsOptions();
			
			$(".selectpickerP").each(function (i){				
				
				var value = $(this).val();
				$(this).html(htmlForSelects).selectpicker('hide');
				if (value) {
					
					$(this).selectpicker("val", value);
				}
			});
			
			tinymce.init({
				width: 528,
				language : "ru",
				selector: "#DETAIL_TEXT",
				plugins: [
				    " autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen table contextmenu paste jbimages"
				],
				toolbar: "insertfile undo redo | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image jbimages",
				relative_urls: false
			});
						
			// handler sending form
			$('#productEditForm').ajaxForm({
				type: 'post',
				dataType:  'json',
				beforeSubmit: dcProduct.ValidateEditForm,
				complete:   function(xhr) {
					dcProduct.hideAjaxLoader();
	        		var json = JSON.parse(xhr.responseText);
	        		
	        		if (json.only_pic_flag == 1) {
	        			// handling product picture
	        			if ( json.result != 'FILE_EMPTY') {
	        				if (json.result == 'OK') {

	        					dcProduct.pictureCounter++;
	        					var picNew = dcProduct.pictureCounter;

	        					var html = $('<div class="photo_block"> \
												<div class="photo_l"> \
													<div class="fl_photo"> \
												<img width="101" height="135" src="'+json.tmp_name+'"></div> \
											<div class="bot_sel"> \
												<div class="control-group"> \
													<label class="lab-left">'+dcProduct.messages.LABEL_CHOOSE_COLOR+' <span class="arrow-required">*</span></label> \
													<div class="controls span"> \
														<select style="display: none;" data-photo-id="'+picNew+'" class="selectpicker show-tick">'+dcProduct.getColorsOptions()+ '</select> </div></div></div> \
															<div class="bot_sel tooltip-demo"> \
																<span class="span"><a data-original-title="'+dcProduct.messages.LABEL_DEL+'" data-placement="top" rel="tooltip" href="#" class="btn btn-danger delPhoto" data-photo-id="'+picNew+'"><i class="icon-remove icon-white"></i></a> \
															</span></div></div></div>').prependTo("#photo_block");

								
								var select = html.find("select");
								select.selectpicker('hide');
								var colorValue = select.selectpicker('val');
								
	        					dcProduct.choosedPhotos[picNew] = {'elem_id':picNew, 'elem_name':'elem_name','elem_pic' :json.tmp_name,'action_flag':'upload', 'elem_color':colorValue};

	        				} else if (json.result== 'ERROR') {
	        		          alert(json.message);
	        				}
	        				
	        			}
	        			$("#show_picture").val("0"); 
	        		} else {
	        			
	        			var message = json.message.split("<br>").join("\n");
	        			if (json.result == 'OK') {
							var html_in = '<div class="modal-header"> \
								<button class="close" data-dismiss="modal">x</button> \
								<h3>'+json.zagolFormConfirm+'</h3> \
								</div>\
								<div class="modal-body"> \
								<p>'+json.textFormConfirm+'</p> \
								</div> \
								<div class="modal-footer"> \
								<a href="#" id="firstBtn" class="btn">'+json.firstBtnText+'</a> \
								<a href="#" id="secondBtn" class="btn btn-primary"> '+json.secondBtnText +'</a>\
								</div>';

			        		$("#modal_div_confirm").html(html_in);
				        	$("#modal_div_confirm").modal('show');
			        		$("#editItem").modal('hide');
							
				        } else {
				        	alert( message);
				        }
	        		}
				}
			});
		}, "json");
	}
}
