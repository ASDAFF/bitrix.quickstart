(function(window){
	window.BXImageUploader = function(Params)
	{
		this.id = Params.id;
		this.initConfig = Params.initConfig;
		this.showWatermark = Params.showWatermark;
		this.showAdditionalSettings = Params.showAdditionalSettings;
		this.opacityForText = Params.opacityForText !== false;
		this.thumbnailSize = parseInt(Params.thumbnailSize);
		this.redirectUrl = Params.redirectUrl;
		this.dropUrl = Params.dropUrl;
		this.type = Params.type;
		this.typeEx = Params.typeEx;
		this.popupOpenType = this.typeEx == 'applet' ? 'top' : '';
		this.uploadMaxFileSize = parseInt(Params.uploadMaxFileSize) || 0;

		if (Params.type == 'form')
		{
			this.simpleUploader = new SimpleUploader({
				oUploader: this,
				id: Params.id,
				thumbnailSize: this.thumbnailSize
			});
		}

		this.oUploadHandler = window['oBXUploaderHandler_' + this.id];
	};

	window.BXImageUploader.prototype = {
		Init: function()
		{
			var _this = this;
			this.pForm = BX(this.id + '_form');
			// Set Album to album selector
			this.pAlbumSel = BX('photo_album_id' + this.id);
			if (this.pAlbumSel)
			{
				this.pNewAlbumName = BX('new_album_name' + this.id);
				this.pAlbumSel.onchange = function()
				{
					_this.pNewAlbumName.style.display = _this.pAlbumSel.value == 'new' ? '' : 'none';
					_this.pForm.photo_album_id.value = _this.pAlbumSel.value;


					if (_this.pAlbumSel.value == 'new')
						BX.focus(_this.pNewAlbumName);
				};
				this.pNewAlbumName.style.display = this.pAlbumSel.value == 'new' ? '' : 'none';
				this.pNewAlbumName.onfocus = function(){this.select();};
				this.pNewAlbumName.onchange = this.pNewAlbumName.onblur = this.pNewAlbumName.onkeyup = function(){_this.pForm.new_album_name.value = _this.pNewAlbumName.value;};

				this.pForm.photo_album_id.value = this.pAlbumSel.value;
				this.pForm.new_album_name.value = this.pNewAlbumName.value;
			}

			if (this.showAdditionalSettings)
			{
				// Additional params
				this.pControlsCont = BX('bxiu_controls_cont' + this.id);
				if (!BX.browser.IsDoctype() && BX.browser.IsIE())
					BX.addClass(this.pControlsCont, "photo-quirks-mode");
				this.pResizeSel = BX('bxiu_resize_' + this.id);
				this.pAddParamsCont = BX('add_params_cont' + _this.id);
				this.pWatermarkCont = BX(this.id + '_watermark_cont');
				this.pSeparator = BX('bxiu_separator_' + this.id);

				if (this.pResizeSel)
				{
					this.pResizeSel.onchange = function()
					{
						if (_this.oUploadHandler)
							_this.oUploadHandler.SetOriginalSize(this.value);
						_this.pForm.photo_resize_size.value = this.value;
					};
					this.pForm.photo_resize_size.value = this.pResizeSel.value;
				}

				if (this.showWatermark)
				{
					BX(this.id + '_use_watermark').onclick = function(){_this.SetUsing(this.checked, true);};
					this.SetUsing(this.initConfig.watermark.use, false);

					// Watermark type
					this.pTypeText = BX(this.id + '_wmark_type_text');
					this.pTypeImg = BX(this.id + '_wmark_type_img');
					this.pTypeText.onclick = function(){_this.SetType('text', true);};
					this.pTypeImg.onclick = function(){_this.SetType('image', true);};

					this.InitTextTypeControls();
					this.InitImageTypeControls();

					this.SetType(this.initConfig.watermark.type, false);
				}

				BX('show_add_params_link' + this.id).onclick = function()
				{
					BX.addClass(_this.pControlsCont, 'bxiu-top-controls-add');
					_this.SaveUserOption('additional', 'Y');
					_this.Resize();
				};
				BX('hide_add_params_link' + this.id).onclick = function()
				{
					BX.removeClass(_this.pControlsCont, 'bxiu-top-controls-add');
					_this.SaveUserOption('additional', 'N');
					_this.Resize();
				};
			}

			if (this.initConfig.add)
				BX.addClass(this.pControlsCont, 'bxiu-top-controls-add');
			else
				BX.removeClass(this.pControlsCont, 'bxiu-top-controls-add');

			this.Resize();
		},

		SaveUserOption: function(option, value)
		{
			BX.userOptions.save('main', this.id, option, value);
		},

		SetUsing: function(use, bSave)
		{
			if (use)
				BX.removeClass(this.pWatermarkCont, "bxiu-watermark-cont-hide");
			else
				BX.addClass(this.pWatermarkCont, "bxiu-watermark-cont-hide");

			if (bSave)
			{
				if (this.oUploadHandler)
					this.oUploadHandler.Watermark.Using(!!use, false);
				this.SaveUserOption('use', use ? 'Y' : 'N');
			}
			this.pForm.photo_watermark_use.value = use ? 'Y' : 'N';
			this.Resize();
		},

		SetType: function(type, bSave)
		{
			if (type != 'image')
				type = 'text';

			if (type == 'text')
			{
				this.pTypeText.checked = true;
				BX.removeClass(this.pControlsCont, 'bxiu-watermark-type-image');
				BX.addClass(this.pControlsCont, 'bxiu-watermark-type-text');
			}
			else
			{
				this.pTypeImg.checked = true;
				BX.addClass(this.pControlsCont, 'bxiu-watermark-type-image');
				BX.removeClass(this.pControlsCont, 'bxiu-watermark-type-text');
			}

			if (this.oUploadHandler)
				this.oUploadHandler.Watermark.Type(type);

			if (bSave !== false)
				this.SaveUserOption('type', type);

			this.pForm.photo_watermark_type.value = type;
			this.Resize();
		},

		SetCopyright: function(val, bSave)
		{
			if (val)
			{
				this.pCopyright.title = BXIU_MESS.CopyrightTitleOff;
				this.pWatermarkText.value = this.pWatermarkText.value;
				BX.removeClass(this.pCopyright, 'bxiu-copyright-none');
				BX.addClass(this.pWatermarkText, 'bxiu-show-copyright');
				// TODO: correction for IE
			}
			else
			{
				this.pCopyright.title = BXIU_MESS.CopyrightTitleOn;
				BX.addClass(this.pCopyright, 'bxiu-copyright-none');
				this.pWatermarkText.value = this.pWatermarkText.value;
				BX.removeClass(this.pWatermarkText, 'bxiu-show-copyright');
			}

			if (this.oUploadHandler)
				this.oUploadHandler.Watermark.Copyright(val);
			this.pForm.photo_watermark_copyright.value = val ? 'Y' : 'N';

			if (bSave !== false)
				this.SaveUserOption('copyright', val ? 'Y' : 'N');
		},

		InitTextTypeControls: function()
		{
			var _this = this;
			this.pWatermarkText = BX(this.id + '_wmark_text');
			this.textButCont = BX(this.id + '_text_but_cont');
			this.pForm.photo_watermark_text.value =  this.pWatermarkText.value = this.initConfig.watermark.text || '';

			this.pWatermarkText.onchange = this.pWatermarkText.onblur = this.pWatermarkText.onkeyup = function()
			{
				_this.pForm.photo_watermark_text.value = this.value;
				if (_this.oUploadHandler)
					_this.oUploadHandler.Watermark.Text(this.value);

				_this.SaveUserOption('text', this.value);
			};

			// Copyright but
			this.pCopyright = BX.create("DIV", {props: {className: 'bxiu-but bxiu-copyright'}});
			this.pCopyright.onclick = function(){_this.SetCopyright(_this.pForm.photo_watermark_copyright.value == 'N', true);};
			this.textButCont.appendChild(BX.create("DIV", {props: {className: 'bxiu-but-cont'}})).appendChild(this.pCopyright);
			this.SetCopyright(this.initConfig.watermark.copyright, false);

			// Color but
			this.oColorpicker = new ColorPicker({oUploader: this});
			this.textButCont.appendChild(BX.create("DIV", {props: {className: 'bxiu-but-cont'}})).appendChild(this.oColorpicker.pWnd);
			if (this.initConfig.watermark.color)
				this.pForm.photo_watermark_color.value = this.initConfig.watermark.color;

			// Position
			this.oTextPosition = new Popup({
				id: 'position_text',
				classPrefix: 'bxiu-but-pos-',
				popupOpenType: this.popupOpenType,
				items: [
					{value: "TopLeft", title: BXIU_MESS.TopLeft},
					{value: "TopCenter", title: BXIU_MESS.TopCenter},
					{value: "TopRight", title: BXIU_MESS.TopRight},
					{value: "CenterLeft", title: BXIU_MESS.CenterLeft},
					{value: "Center", title: BXIU_MESS.Center},
					{value: "CenterRight", title: BXIU_MESS.CenterRight},
					{value: "BottomLeft", title: BXIU_MESS.BottomLeft},
					{value: "BottomCenter", title: BXIU_MESS.BottomCenter},
					{value: "BottomRight", title: BXIU_MESS.BottomRight}
				],
				currentValue: this.initConfig.watermark.position,
				title: BXIU_MESS.PositionTitle,
				OnCreate: function(obj)
				{
					obj.type = _this.type;
					BX.addClass(obj.pWnd, 'bxiu-but-pos-center');
					BX.addClass(obj.oPopup.Get(), 'bxiu-pos-popup');
				},
				OnSelect: function(obj, item)
				{
					obj.pWnd.className = 'bxiu-but bxiu-but-pos-' + item.value.toLowerCase();
					if (_this.oUploadHandler)
						_this.oUploadHandler.Watermark.Position(item.value);

					_this.pForm.photo_watermark_position.value = item.value;
					_this.SaveUserOption('position', item.value);
				}
			});
			this.textButCont.appendChild(BX.create("DIV", {props: {className: 'bxiu-but-cont'}})).appendChild(this.oTextPosition.pWnd);

			// Text size
			this.oTextSize = new Popup({
				id: 'size_text',
				classPrefix: 'bxiu-but-t-size-',
				popupOpenType: this.popupOpenType,
				items: [
					{value: "big", title: BXIU_MESS.SizeBig},
					{value: "middle", title: BXIU_MESS.SizeMiddle},
					{value: "small", title: BXIU_MESS.SizeSmall}
				],
				currentValue: this.initConfig.watermark.size,
				title: BXIU_MESS.SizeTitle,
				OnCreate: function(obj)
				{
					obj.type = _this.type;
					BX.addClass(obj.pWnd, 'bxiu-but-t-size-middle');
					BX.addClass(obj.oPopup.Get(), 'bxiu-text-size-popup');
				},
				OnSelect: function(obj, item)
				{
					obj.pWnd.className = 'bxiu-but bxiu-but-t-size-' + item.value.toLowerCase();
					if (_this.oUploadHandler)
						_this.oUploadHandler.Watermark.Size(item.value);
					_this.pForm.photo_watermark_size.value = item.value;
					_this.SaveUserOption('size', item.value);
				}
			});
			this.textButCont.appendChild(BX.create("DIV", {props: {className: 'bxiu-but-cont'}})).appendChild(this.oTextSize.pWnd);

			// Text opacity
			if (this.opacityForText)
			{
				this.oTextOpacity = new OpacityControl({
					currentValue: this.initConfig.watermark.opacity,
					OnSelect: function(value)
					{
						if (_this.oUploadHandler)
							_this.oUploadHandler.Watermark.Opacity(value);
						_this.pForm.photo_watermark_opacity.value = value;
						_this.SaveUserOption('opacity', value);
					}
				});
				this.textButCont.appendChild(BX.create("DIV", {props: {className: 'bxiu-opacity-cont'}})).appendChild(this.oTextOpacity.pCont);
			}
		},

		InitImageTypeControls: function()
		{
			var _this = this;
			this.imgButCont = BX(this.id + '_img_but_cont');

			this.pImgForm = BX('bxiu_wm_form' + this.id);
			this.pImgInput = BX('bxiu_wm_img' + this.id);
			this.pImgInput.onchange = function()
			{
				BX.ajax.submit(_this.pImgForm, function()
				{
					var pCont = BX('bxiu_wm_img_iframe_cont' + _this.id);
					pCont.className = 'bxiu-iframe-cont-ok';

					setTimeout(function(){
						var res = top.bxiu_wm_img_res;
						if (res.error)
							return alert(res.error);
						_this.ShowThumbnailImage(res.path, res.width, res.height);
					}, 50);
				});
			};

			// Position
			this.oImagePosition = new Popup({
				id: 'position_image',
				classPrefix: 'bxiu-but-pos-',
				popupOpenType: this.popupOpenType,
				items: [
					{value: "TopLeft", title: BXIU_MESS.TopLeft},
					{value: "TopCenter", title: BXIU_MESS.TopCenter},
					{value: "TopRight", title: BXIU_MESS.TopRight},
					{value: "CenterLeft", title: BXIU_MESS.CenterLeft},
					{value: "Center", title: BXIU_MESS.Center},
					{value: "CenterRight", title: BXIU_MESS.CenterRight},
					{value: "BottomLeft", title: BXIU_MESS.BottomLeft},
					{value: "BottomCenter", title: BXIU_MESS.BottomCenter},
					{value: "BottomRight", title: BXIU_MESS.BottomRight}
				],
				currentValue: this.initConfig.watermark.position,
				title: BXIU_MESS.PositionTitle,
				OnCreate: function(obj)
				{
					obj.type = _this.type;
					BX.addClass(obj.pWnd, 'bxiu-but-pos-center');
					BX.addClass(obj.oPopup.Get(), 'bxiu-pos-popup');
				},
				OnSelect: function(obj, item)
				{
					obj.pWnd.className = 'bxiu-but bxiu-but-pos-' + item.value.toLowerCase();
					if (_this.oUploadHandler)
						_this.oUploadHandler.Watermark.Position(item.value);
					_this.pForm.photo_watermark_position.value = item.value;
					_this.SaveUserOption('position', item.value);
				}
			});
			this.imgButCont.appendChild(BX.create("DIV", {props: {className: 'bxiu-but-cont'}})).appendChild(this.oImagePosition.pWnd);

			// Image size
			this.oImgSize = new Popup({
				id: 'size_image',
				classPrefix: 'bxiu-but-i-size-',
				popupOpenType: this.popupOpenType,
				items: [
					{value: "real", title: BXIU_MESS.SizeReal},
					{value: "big", title: BXIU_MESS.SizeBig},
					{value: "middle", title: BXIU_MESS.SizeMiddle},
					{value: "small", title: BXIU_MESS.SizeSmall}
				],
				currentValue: this.initConfig.watermark.size,
				title: BXIU_MESS.SizeTitle,
				OnCreate: function(obj)
				{
					obj.type = _this.type;
					BX.addClass(obj.pWnd, 'bxiu-but-i-size-real');
					BX.addClass(obj.oPopup.Get(), 'bxiu-img-size-popup');
				},
				OnSelect: function(obj, item)
				{
					obj.pWnd.className = 'bxiu-but bxiu-but-i-size-' + item.value.toLowerCase();
					if (_this.oUploadHandler)
						_this.oUploadHandler.Watermark.Size(item.value);
					_this.pForm.photo_watermark_size.value = item.value;
					_this.SaveUserOption('size', item.value);
				}
			});
			this.imgButCont.appendChild(BX.create("DIV", {props: {className: 'bxiu-but-cont'}})).appendChild(this.oImgSize.pWnd);

			// Image opacity
			this.oImgOpacity = new OpacityControl({
				currentValue: this.initConfig.watermark.opacity,
				OnSelect: function(value)
				{
					if (_this.oUploadHandler)
						_this.oUploadHandler.Watermark.Opacity(value);
					_this.pForm.photo_watermark_opacity.value = value;
					_this.SaveUserOption('opacity', value);
				}
			});
			this.imgButCont.appendChild(BX.create("DIV", {props: {className: 'bxiu-img-opacity-cont'}})).appendChild(this.oImgOpacity.pCont);
		},

		ShowThumbnailImage: function(value, width, height)
		{
			var _this = this;
			if (!this.watermarkPreview)
				this.watermarkPreview = BX('watermark_img_preview' + this.id);
			if (!this.watermarkPreviewCont)
				this.watermarkPreviewCont = BX(this.id + '_wmark_preview_cont');

			if (!this.watermarkPreviewDel)
			{
				this.watermarkPreviewDel = BX(this.id + '_wmark_preview_del');
				this.watermarkPreviewDel.onclick = function()
				{
					if (_this.oUploadHandler)
						_this.oUploadHandler.Watermark.File('');
					_this.watermarkPreview.src = '';
					_this.watermarkPreviewCont.style.display = "none";
					_this.watermarkPreview.src = "/bitrix/images/1.gif";
					_this.SaveUserOption('file', '');
					_this.pForm.photo_watermark_path.value = '';
				};
			}
			this.watermarkPreviewCont.style.display = "block";

			if (this.oUploadHandler)
			{
				this.oUploadHandler.Watermark.File(value);
				this.oUploadHandler.Watermark.FileWidth(width);
				this.oUploadHandler.Watermark.FileHeight(height);
			}

			this.watermarkPreview.removeAttribute('width');
			this.watermarkPreview.removeAttribute('height');

			this.watermarkPreview.src = value;
			this.watermarkPreview.style.display = '';
			this.watermarkPreview.style.margin = '5px';
			this.pForm.photo_watermark_path.value = value;
			this.SaveUserOption('file', value);

			setTimeout(function(){
				if (width > height && width > 200)
					_this.watermarkPreview.width = '200';
				else if (height > width && height > 200)
					_this.watermarkPreview.height = '200';
			}, 5);

			this.watermarkPreview.onerror = function() {this.style.display = 'none';};
			this.watermarkPreview.onload = function()
			{
				_this.Resize();
				setTimeout(function()
				{
					_this.Resize();
					if (_this.watermarkPreview.src != "/bitrix/images/1.gif")
					{
						_this.watermarkPreviewDel.style.left = (parseInt(_this.watermarkPreview.offsetWidth) - 6) + 'px';
						_this.watermarkPreviewDel.style.display = "block";
					}
				}, 200);
			};
			_this.Resize();
		},

		Resize: function()
		{
			if (!this.showAdditionalSettings)
				return;

			var h = 0, h1 = 0;
			if (this.pWatermarkCont)
				h = this.pWatermarkCont.offsetHeight + 25;

			if (!this.pLeftColControls)
				this.pLeftColControls = BX('bxiu_left_col_' + this.id);

			if (this.pLeftColControls)
				h1 = this.pLeftColControls.offsetHeight + 25;

			if (h1 > h)
				h = h1;

			this.pAddParamsCont.style.height = h + "px";
			if (this.pSeparator)
				this.pSeparator.style.height = (h - 16) + "px";
		}
	};

	function ColorPicker(oPar)
	{
		this.bCreated = false;
		this.bOpened = false;
		this.zIndex = 5000;
		this.pWnd = BX.create("DIV", {props: {className: "bxiu-color-but bxiu-but"}});
		if (oPar.oUploader.initConfig.watermark.color)
			this.pWnd.style.backgroundColor = oPar.oUploader.initConfig.watermark.color;

		this.oPar = oPar;
		var _this = this;
		this.pWnd.onmousedown = function(e){_this.OnClick(e, this)};
	}

	ColorPicker.prototype = {
		Create: function ()
		{
			var _this = this;
			this.pColCont = document.body.appendChild(BX.create("DIV", {props: {className: "wm-colpick-cont"}, style: {zIndex: this.zIndex}}));

			var
				arColors = ['#FF0000', '#FFFF00', '#00FF00', '#00FFFF', '#0000FF', '#FF00FF', '#FFFFFF', '#EBEBEB', '#E1E1E1', '#D7D7D7', '#CCCCCC', '#C2C2C2', '#B7B7B7', '#ACACAC', '#A0A0A0', '#959595',
				'#EE1D24', '#FFF100', '#00A650', '#00AEEF', '#2F3192', '#ED008C', '#898989', '#7D7D7D', '#707070', '#626262', '#555', '#464646', '#363636', '#262626', '#111', '#000000',
				'#F7977A', '#FBAD82', '#FDC68C', '#FFF799', '#C6DF9C', '#A4D49D', '#81CA9D', '#7BCDC9', '#6CCFF7', '#7CA6D8', '#8293CA', '#8881BE', '#A286BD', '#BC8CBF', '#F49BC1', '#F5999D',
				'#F16C4D', '#F68E54', '#FBAF5A', '#FFF467', '#ACD372', '#7DC473', '#39B778', '#16BCB4', '#00BFF3', '#438CCB', '#5573B7', '#5E5CA7', '#855FA8', '#A763A9', '#EF6EA8', '#F16D7E',
				'#EE1D24', '#F16522', '#F7941D', '#FFF100', '#8FC63D', '#37B44A', '#00A650', '#00A99E', '#00AEEF', '#0072BC', '#0054A5', '#2F3192', '#652C91', '#91278F', '#ED008C', '#EE105A',
				'#9D0A0F', '#A1410D', '#A36209', '#ABA000', '#588528', '#197B30', '#007236', '#00736A', '#0076A4', '#004A80', '#003370', '#1D1363', '#450E61', '#62055F', '#9E005C', '#9D0039',
				'#790000', '#7B3000', '#7C4900', '#827A00', '#3E6617', '#045F20', '#005824', '#005951', '#005B7E', '#003562', '#002056', '#0C004B', '#30004A', '#4B0048', '#7A0045', '#7A0026'],
				row, cell, colorCell,
				tbl = BX.create("TABLE", {props: {className: 'wm-colpic-tbl'}}),
				i, l = arColors.length;

			row = tbl.insertRow(-1);
			cell = row.insertCell(-1);
			cell.colSpan = 8;
			var defBut = cell.appendChild(BX.create("SPAN", {props: {className: 'wm-colpic-def-but'}, text: BXIU_MESS.DefaultColor}));
			defBut.onmouseover = function()
			{
				this.className = 'wm-colpic-def-but wm-colpic-def-but-over';
				colorCell.style.backgroundColor = '#FF0000';
			};
			defBut.onmouseout = function(){this.className = 'wm-colpic-def-but';};
			defBut.onmousedown = function(e){_this.Select('#FF0000');};

			colorCell = row.insertCell(-1);
			colorCell.colSpan = 8;
			colorCell.className = 'wm-color-inp-cell';
			colorCell.style.backgroundColor = arColors[38];

			for(i = 0; i < l; i++)
			{
				if (Math.round(i / 16) == i / 16) // new row
					row = tbl.insertRow(-1);

				cell = row.insertCell(-1);
				cell.innerHTML = '&nbsp;';
				cell.className = 'wm-col-cell';
				cell.style.backgroundColor = arColors[i];
				cell.id = 'lhe_color_id__' + i;

				cell.onmouseover = function ()
				{
					this.className = 'wm-col-cell wm-col-cell-over';
					colorCell.style.backgroundColor = arColors[this.id.substring('lhe_color_id__'.length)];
				};
				cell.onmouseout = function (){this.className = 'wm-col-cell';};
				cell.onmousedown = function ()
				{
					var k = this.id.substring('lhe_color_id__'.length);
					_this.Select(arColors[k]);
				};
			}

			this.pColCont.appendChild(tbl);
			this.bCreated = true;
		},

		OnClick: function (e, pEl)
		{
			if(this.disabled)
				return false;

			if (!this.bCreated)
				this.Create();

			if (this.bOpened)
				return this.Close();

			this.Open();
		},

		Open: function ()
		{
			var
				pos = BX.pos(this.pWnd),
				_this = this, top, left = pos.left;

			this.pColCont.style.display = 'block';
			if (BX.browser.IsIE() && this.oPar.oUploader.type == 'applet')
			{
				top = pos.top - parseInt(this.pColCont.offsetHeight) - 2;
			}
			else
			{
				pos = BX.align(pos, 325, 155, this.oPar.oUploader.popupOpenType);
				top = pos.top;
				left = pos.left;
			}

			BX.bind(window, "keypress", BX.proxy(this.OnKeyPress, this));
			oTransOverlay.Show({onclick: function(){_this.Close()}});

			this.pColCont.style.top = top + 'px';
			this.pColCont.style.left = left + 'px';
			this.bOpened = true;
		},

		Close: function ()
		{
			this.pColCont.style.display = 'none';
			oTransOverlay.Hide();
			BX.unbind(window, "keypress", BX.proxy(this.OnKeyPress, this));
			this.bOpened = false;
		},

		OnKeyPress: function(e)
		{
			if(!e) e = window.event
			if(e.keyCode == 27)
				this.Close();
		},

		Select: function (color)
		{
			if (this.oPar.oUploader.oUploadHandler)
				this.oPar.oUploader.oUploadHandler.Watermark.Color(color);
			this.oPar.oUploader.pForm.photo_watermark_color.value = color;
			this.oPar.oUploader.SaveUserOption('color', color);
			this.pWnd.style.backgroundColor = color;

			if (this.oPar.OnSelect && typeof this.oPar.OnSelect == 'function')
				this.oPar.OnSelect(color, this);
			this.Close();
		}
	};

	function Popup(oPar)
	{
		var _this = this;
		this.bCreated = false;
		this.bOpened = false;
		this.zIndex = 5000;
		this.oPar = oPar;
		this.pWnd = BX.create("DIV", {props: {className: "bxiu-but bxiu-but-" + oPar.id}});
		this.pWnd.onmousedown = function(e){_this.OnClick(e, this)};
		if (oPar.title)
			this.pWnd.title = oPar.title;

		this.oPopup = new BX.CWindow(false, 'float');

		if (this.oPar && typeof this.oPar.OnCreate == 'function')
			this.oPar.OnCreate(this);

		var i, l = this.oPar.items.length;
		for (i = 0; i < l; i++)
		{
			this.oPar.items[i].pItem = BX.create("DIV", {props: {id: 'bxiu__item_' + i, className: "bxiu-popup-but " + this.oPar.classPrefix + this.oPar.items[i].value.toLowerCase()}});
			if (this.oPar.items[i].title)
				this.oPar.items[i].pItem.title = this.oPar.items[i].title;

			this.oPopup.Get().appendChild(this.oPar.items[i].pItem);
			this.oPar.items[i].pItem.onmousedown = function(){_this.SelectItem(this.id.substr(parseInt('bxiu__item_'.length)));}
		}

		if (typeof oPar.currentValue != 'undefined')
			this.SelectItem(false, oPar.currentValue);

		this.pWnd.onmousedown = function(e){_this.OnClick(e, this)};
	}

	Popup.prototype = {
		OnClick: function (e, pEl)
		{
			if (this.bOpened)
				return this.Close();
			this.Open();
		},

		Close: function ()
		{
			oTransOverlay.Hide();
			this.oPopup.Close();
			this.bOpened = false;
		},

		Open: function ()
		{
			this.oPopup.Show();
			var
				pos = BX.pos(this.pWnd),
				top = pos.top, left = pos.left;

			if (this.oPar.popupOpenType == 'top')
				top -= this.oPopup.Get().offsetHeight;
			else
				top += 18;

			this.oPopup.Get().style.top = top + 'px';
			this.oPopup.Get().style.left = left + 'px';

			var _this = this;
			oTransOverlay.Show({onclick: function(){_this.Close()}}),
			this.bOpened = true;
		},

		SelectItem: function(ind, value)
		{
			if (ind === false && value)
			{
				var i, l = this.oPar.items.length, item;
				for (i = 0; i < l; i++)
					if (this.oPar.items[i].value == value)
						break;
				ind = i;
			}

			var oItem = this.oPar.items[ind] ? this.oPar.items[ind] : this.oPar.items[0];
			if (this.oPar.OnSelect && typeof this.oPar.OnSelect == 'function')
				this.oPar.OnSelect(this, oItem);

			if (this.oPar.items[this.activeItemInd])
				BX.removeClass(this.oPar.items[this.activeItemInd].pItem, 'bxiu-active');

			this.activeItemInd = ind;
			if (this.oPar.items[ind] && this.oPar.items[ind].pItem)
				BX.addClass(this.oPar.items[ind].pItem, 'bxiu-active');

			this.Close();
		}
	}

	function OpacityControl(oPar)
	{
		this.pCont = BX.create("DIV", {props: {className: "bxiu-opacity"}});

		this.pCont.appendChild(BX.create("DIV", {props: {className: "bxiu-opacity-label"}, text: BXIU_MESS.Opacity}));
		var pDiv = this.pCont.appendChild(BX.create("DIV", {props: {className: "bxiu-op-div"}}));

		this.oPar = oPar;
		this.values = [
			{value:100, title: '0%'},
			{value:75, title: '25%'},
			{value:50, title: '50%'},
			{value:25, title: '75%'}
		];

		var
			_this = this,
			i, l = this.values.length, valCont;

		for (i = 0; i < l; i++)
		{
			valCont = pDiv.appendChild(BX.create("DIV", {props: {id: "bxiu_op_item_" + i, className: "bxiu-op-val-cont"}}));
			valCont.appendChild(BX.create("DIV", {props: {className: "bxiu-op-l-corn"}}));
			valCont.appendChild(BX.create("DIV", {props: {className: "bxiu-op-center"}, html: '<span>' + this.values[i].title + '</span>'}));
			valCont.appendChild(BX.create("DIV", {props: {className: "bxiu-op-r-corn"}}));
			valCont.onmousedown = function(){_this.SelectItem(parseInt(this.id.substr('bxiu_op_item_'.length)));};
			this.values[i].cont = valCont;
		}

		if (typeof oPar.currentValue != 'undefined')
			this.SelectItem(false, oPar.currentValue);
	}

	OpacityControl.prototype = {
		SelectItem: function(ind, value)
		{
			if (ind === false && typeof value != 'undefined')
			{
				var i, l = this.values.length;
				for (i = 0; i < l; i++)
					if (this.values[i].value == value)
						break;
				ind = i;
			}

			if (this.oPar.OnSelect && typeof this.oPar.OnSelect == 'function')
				this.oPar.OnSelect(this.values[ind].value);

			if (this.values[this.activeItemInd])
				BX.removeClass(this.values[this.activeItemInd].cont, 'bxiu-op-val-cont-active');

			this.activeItemInd = ind;
			BX.addClass(this.values[ind].cont, 'bxiu-op-val-cont-active');
		}
	};


	function Overlay()
	{
		this.id = 'bxiu_trans_overlay';
		this.zIndex = 100;
	}

	Overlay.prototype =
	{
		Create: function ()
		{
			this.bCreated = true;
			this.bShowed = false;
			var ws = BX.GetWindowScrollSize();
			this.pWnd = document.body.appendChild(BX.create("DIV", {props: {id: this.id, className: "bxiu-trans-overlay"}, style: {zIndex: this.zIndex, width: ws.scrollWidth + "px", height: ws.scrollHeight + "px"}}));

			this.pWnd.ondrag = BX.False;
			this.pWnd.onselectstart = BX.False;
		},

		Show: function(arParams)
		{
			if (!this.bCreated)
				this.Create();
			this.bShowed = true;

			var ws = BX.GetWindowScrollSize();

			this.pWnd.style.display = 'block';
			this.pWnd.style.width = ws.scrollWidth + "px";
			this.pWnd.style.height = ws.scrollHeight + "px";

			if (!arParams)
				arParams = {};

			if (arParams.zIndex)
				this.pWnd.style.zIndex = arParams.zIndex;

			if (arParams.onclick && typeof arParams.onclick == 'function')
				this.pWnd.onclick = arParams.onclick;

			BX.bind(window, "resize", BX.proxy(this.Resize, this));
			return this.pWnd;
		},

		Hide: function ()
		{
			if (!this.bShowed)
				return;
			this.bShowed = false;
			this.pWnd.style.display = 'none';
			BX.unbind(window, "resize", BX.proxy(this.Resize, this));
			this.pWnd.onclick = null;
		},

		Resize: function ()
		{
			if (this.bCreated)
				this.pWnd.style.width = BX.GetWindowScrollSize().scrollWidth + "px";
		}
	};

	var oTransOverlay = new Overlay();

	/* Upload form */
	var SimpleUploader = function(Params)
	{
		this.id = Params.id;
		this.oUploader = Params.oUploader;
		this.thumbnailSize = Params.thumbnailSize;

		// Visual data
		this.form = false;
		this.container = false;

		// Main data
		this.files = [];
		this.container = BX('bxiu_simple_cont' + this.id);
		this.form = BX(this.id + '_form');
		this.pGoToAlbum = BX('bxiu_simple_go' + this.id);
		var _this = this;

		this.pGoToAlbum.onmousedown = function(){window.location = _this.oUploader.redirectUrl;};

		this.pInput = BX('bxiu_upload_inp' + this.id);
		this.pList = BX('bxiu_files_list' + this.id);
		this.pInputParent = this.pInput.parentNode;
		this.PackageGuid = Math.random() * 10E16;

		this.pInput.onchange = BX.proxy(this.OnChangeFile, this);
	};

	SimpleUploader.prototype = {
		AddEntry: function(fileName, form, pFile)
		{
			var size = this.thumbnailSize;
			var
				_this = this,
				el = this.pList.appendChild(BX.create("div", {props : { className : "bxiu-file-cont bxiu-loading"}, style: {width: this.thumbnailSize + 'px'}})),
				pThumb = el.appendChild(BX.create("div", {props : {className : "bxiu-file-thumb"}, style: {width: this.thumbnailSize + 'px', height: this.thumbnailSize + 'px'}})),
				pImg = pThumb.appendChild(BX.create("IMG", {props:{src: '/bitrix/images/1.gif'}, style: {width: this.thumbnailSize + 'px', height: this.thumbnailSize + 'px'}})),
				pTitle = el.appendChild(BX.create("div", {props : {className : "bxiu-file-title"}, text: fileName, style: {width: size + 'px'}, title: fileName})),
				pDel = el.appendChild(BX.create("div", {props : {className : "bxiu-file-del", id: 'bxiu_file_' + this.files.length,title: BXIU_MESS.DelEntry}}));

			pDel.onclick = function() {_this.DelEntry(this.id.substr(parseInt('bxiu_file_'.length)));};
			BX.proxy(this.DelEntry, this);
			this.files.push({
				name: fileName,
				pWnd: el,
				pThumb: pThumb,
				pImg: pImg,
				pTitle: pTitle
			});
		},

		AdjustThumb: function(img, w, h)
		{
			w = parseInt(w);
			h = parseInt(h);
			if (!w || !h || !img)
				return;

			var r = w / h;
			if (r > 1)
			{
				img.style.width = (this.thumbnailSize * r) + "px";
				img.style.height = this.thumbnailSize + "px";
				img.style.left = Math.round((this.thumbnailSize - this.thumbnailSize * r /* width*/) / 2) + "px";
				img.style.top = 0;
			}
			else
			{
				img.style.height = Math.round(this.thumbnailSize / r) + "px";
				img.style.width = this.thumbnailSize + "px";
				img.style.top = Math.round((this.thumbnailSize - this.thumbnailSize / r /* height*/) / 2) + "px";
				img.style.left = 0;
			}
		},

		SubmitFiles: function(form)
		{
			form.appendChild(BX.create("input", {props : {type : "hidden", name : "PackageGuid", value : this.PackageGuid}}));

			var i, l = this.oUploader.pForm.childNodes.length, inp;
			for (i = 0; i < l; i++)
			{
				inp = this.oUploader.pForm.childNodes[i];
				if (inp.name && inp.name.length > 0 && typeof inp.value != 'undefined')
					form.appendChild(BX.create("input", {props : {type : "hidden", name : inp.name, value : inp.value}}));
			}

			top.bxiu_simple_res = false;
			BX.ajax.submit(form, BX.proxy(this.OnSubmit, this));
		},

		DelEntry: function(ind)
		{
			if (!confirm(BXIU_MESS.DelEntryConfirm) || !this.files[ind])
				return;

			this.files[ind].deleted = true;
			this.files[ind].pWnd.parentNode.removeChild(this.files[ind].pWnd);

			var url = this.oUploader.dropUrl;
			url = url.replace(/#SECTION_ID#/ig, this.oUploader.pAlbumSel.value);
			url = url.replace(/#ELEMENT_ID#/ig, parseInt(this.files[ind].id));
			BX.ajax.get(url, {sessid: BX.bitrix_sessid(), AJAX_CALL : "Y"}, function(result){});
		},

		OnChangeFile: function()
		{
			var files = [];
			if (!BX.browser.IsIE() && this.pInput.files && this.pInput.files.length > 0)
			{
				files = this.pInput.files;
			}
			else
			{
				var filePath = this.pInput.value;
				var fileTitle = filePath.replace(/.*\\(.*)/, "$1");
				fileTitle = fileTitle.replace(/.*\/(.*)/, "$1");
				files = [{fileName : fileTitle}];
			}

			var form = document.body.appendChild(BX.create("form", {props : {method : "post",action : this.form.action,enctype : "multipart/form-data", encoding : "multipart/form-data"},style : {display : "none"}}));

			for (var i = 0; i < files.length; i++)
			{
				if (files[i] && false)
				{
					if (files[i].size && this.oUploader.uploadMaxFileSize && files[i].size > this.oUploader.uploadMaxFileSize)
					{
						return alert(BXIU_MESS.LargeSizeWarn);
					}
					if (files[i].type && files[i].type.toLowerCase() && files[i].type.toLowerCase().indexOf('image') == -1)
					{
						return alert(BXIU_MESS.WrongTypeWarn);
					}
				}

				if (!files[i].fileName && files[i].name)
					files[i].fileName = files[i].name;
				this.AddEntry(files[i].fileName, form, this.pInput);
			}

			this.EnableGoBut(false);
			form.appendChild(this.pInput);
			this.SubmitFiles(form);

			var _this = this;
			setTimeout(function(){
				_this.pInput.parentNode.removeChild(_this.pInput);
				_this.pInput = _this.pInputParent.appendChild(BX.create("INPUT", {props: {type: "file", name: "photos[]", size: "1", multiple: _this.pInput.multiple, id: _this.pInput.id, className: "bxiu-fake-input"}}));
				_this.pInput.onchange = BX.proxy(_this.OnChangeFile, _this);
			},100);
		},

		OnSubmit: function()
		{
			var
				wrongLoaded = {},
				b_stop_all_unloaded = false,
				b_response = !!top.bxiu_simple_res;

			if (!b_response)
				alert(BXIU_MESS.WrongServerResponse);

			if (top.bxiu_simple_res && top.bxiu_simple_res.error && top.bxiu_simple_res.error[0] && b_response)
			{
				var er, ind, err = top.bxiu_simple_res.error[0];
				alert('[' + err.id + '] ' + err.text);

				for (ind in top.bxiu_simple_res.error)
				{
					er = top.bxiu_simple_res.error[ind];
					if (er.file)
						wrongLoaded[er.file] = true;
					else
						b_stop_all_unloaded = true;
				}
			}

			var
				bComplete = true,
				i, l = this.files.length, oFile,
				res = b_response ? top.bxiu_simple_res.files : {};

			for (i = 0; i < l; i++)
			{
				oFile = this.files[i];
				if (!oFile.loaded && res[oFile.name])
				{
					oFile.id = res[oFile.name]['ID'];
					oFile.loaded = true;
					oFile.pImg.src = res[oFile.name]['PATH'];
					this.AdjustThumb(oFile.pImg, res[oFile.name]['WIDTH'], res[oFile.name]['HEIGHT']);
					BX.removeClass(oFile.pWnd, "bxiu-loading");
				}
				else if (!oFile.loaded && (wrongLoaded[oFile.name] || !b_response || b_stop_all_unloaded))
				{
					oFile.deleted = true;
					if (oFile.pWnd.parentNode)
					{
						oFile.pWnd.parentNode.removeChild(oFile.pWnd);
						BX.removeClass(oFile.pWnd, "bxiu-loading");
					}
				}
				else if (!oFile.loaded && !oFile.deleted)
				{
					bComplete = false;
				}
			}

			this.EnableGoBut(bComplete);

			if (b_response && top.bxiu_simple_res && top.bxiu_simple_res.redirectUrl)
				this.oUploader.redirectUrl = top.bxiu_simple_res.redirectUrl;

			if (b_response && top.bxiu_simple_res.newSection && this.oUploader.pAlbumSel)
			{
				this.oUploader.pAlbumSel.options.add(new Option(top.bxiu_simple_res.newSection.title, top.bxiu_simple_res.newSection.id, true, true));
				this.oUploader.pAlbumSel.onchange();
			}
		},

		EnableGoBut: function(bEnabele)
		{
			if (bEnabele)
			{
				this.pGoToAlbum.style.display = "";
				this.pGoToAlbum.disabled = false;
			}
			else
			{
				this.pGoToAlbum.disabled = true;
			}
		}
	}
})(window)