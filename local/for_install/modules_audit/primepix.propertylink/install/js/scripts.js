(function(w) {
	'use strict';

	var app = w.app = {};

	app.Service = {
		url     : '/bitrix/tools/primepix.propertylink_ajax.php',
		request : function(type, action, params) {

			var data = {
				action : action,
				ajax   : true,
				sessid : BX.bitrix_sessid(),
				params : params
			};

			return $.ajax({
				url      : app.Service.url,
				type     : type,
				data     : data,
				dataType : 'json'
			});
		}	
	};

	app.PropertyLink = {
		controls: {},
		init: function(initData) {

			// set controls
			app.PropertyLink.controls.select = $('.-iblock-select');
			app.PropertyLink.controls.saveCb = $('.-save-links');
			app.PropertyLink.controls.eraseCb = $('.-erase-links');
			app.PropertyLink.controls.submit = $('.-link-properties');
			app.PropertyLink.controls.result = $('.-result-message');
			app.PropertyLink.controls.clear  = $('.-clear');

			app.PropertyLink.controls.tabs   = $(".-tab-btns");
			app.PropertyLink.controls.save   = $(".-save-settings");
			app.PropertyLink.controls.formEx = $(".-settings-form");
			app.PropertyLink.controls.addEx  = $('.-add-ext');
			app.PropertyLink.controls.stat   = $('.-stat');

			app.PropertyLink.controls.openStat = $('.-openStat');
			app.PropertyLink.controls.dialog   = new BX.CDialog();
			
			app.PropertyLink.controls.submit.on('click', app.PropertyLink.events.makeSync);
			app.PropertyLink.controls.openStat.on('click', app.PropertyLink.events.getStat);
			app.PropertyLink.controls.clear.on('click', app.PropertyLink.events.clear);
			app.PropertyLink.controls.save.on('click', app.PropertyLink.events.saveSettings);
			app.PropertyLink.controls.addEx.on('click', app.PropertyLink.events.addExt);
		},
		showMessage: function(response) {

			app.PropertyLink.controls.formMessage.removeClass('error');

			if (response) {
			} else {
			}

		},
		process: function(state) {
			app.PropertyLink.controls.submit
				.add(app.PropertyLink.controls.clear)
				.add(app.PropertyLink.controls.openStat)
				.prop('disabled', state);

			if (state) {
				BX.showWait();
			} else {
				BX.closeWait();
			}
		},
		events: {
			saveSettings: function(){
				app.PropertyLink.controls.formEx.submit();
				console.log("save");
			},
			showButton: function(tab){
				app.PropertyLink.controls.tabs.hide().filter('[data-tab-id="'+tab+'"]').show();
			},
			clear: function(){

				var status   = '',
					select   = app.PropertyLink.controls.select,
					result   = app.PropertyLink.controls.result,
					data     = {iblock_id: select.val(), pack : 0, size : 50},
					syncTime = 3000, 
					linked   = 0;

				app.PropertyLink.process(true);
				result.empty().removeClass('error success');

				var syncer = setTimeout(function linkProperties() {

					app.Service.request('POST', 'clearProperties', data)
						.done(function(resp) {
							app.PropertyLink.process(false);
							 if (resp.status == 'OK') {
								result
									.text(BX.message('pxpl_cleared'))
									.addClass('success');
								app.PropertyLink.process(false);

								return false;
							 } else{
							 	result
									.text(BX.message('pxpl_error_sync') + resp.msg)
									.addClass('error');
							 	app.PropertyLink.process(false);
							 }

						})
						.fail(function() {
							clearTimeout(syncer);
							app.PropertyLink.process(false);
						})
					}, syncTime);
			},
			makeSync: function() {

				var status   = '',
					select   = app.PropertyLink.controls.select,
					result   = app.PropertyLink.controls.result,
					saveLink = app.PropertyLink.controls.saveCb.prop('checked') ? 'Y' : 'N',
					eraseLink = app.PropertyLink.controls.eraseCb.prop('checked') ? 'Y' : 'N',
					data     = {iblock_id: select.val(), save_links: saveLink, erase_links: eraseLink, pack : 0, size : 50},
					syncTime = 3000, 
					linked   = 0;

				app.PropertyLink.process(true);
				result.empty().removeClass('error success');

				var syncer = setTimeout(function linkProperties() {

					app.Service.request('POST', 'linkProperties', data)
						.done(function(resp) {

							if (resp.status == 'ERROR') {
								result
									.text(BX.message('pxpl_error_sync') + resp.msg)
									.addClass('error');
								app.PropertyLink.process(false);

								return false;
							}

							var sync = resp.data;

							linked += sync.count ? sync.count : 0;
							status  = BX.message('pxpl_sync_status').replace('{{linked}}', linked);

							if (sync.status == 'inProgress') {
								data.pack++;
								syncer = setTimeout(linkProperties, syncTime);

								result
									.text(BX.message('pxpl_sync') + status)
									.addClass('success');

							} else {

								result
									.text(BX.message('pxpl_success_sync') + status)
									.addClass('success');

								clearTimeout(syncer);
								app.PropertyLink.process(false);
							}
						})
						.fail(function() {
							clearTimeout(syncer);
							app.PropertyLink.process(false);
						})
				}, syncTime);
			},
			addExt: function(){

				var mssect = BX.message('pxpl_section'),
					msprop = BX.message('pxpl_property');

				var $to = $('.-settings-form').find('tbody'),
					id  = $('.-last-id').val(),
					template = '<tr> \
						<td> \
						<input \
							type="text" \
							id="exeptions" \
							placeholder="'+msprop+'" \
							name="iblocks[{{ID}}]"> \
						<input \
							type="text" \
							id="exeptions" \
							placeholder="'+mssect+'" \
							name="props[{{ID}}]"> \
						</td> \
						</tr>';

				$(template.replace(/{{ID}}/g, ++id)).insertBefore('.-insert');

				$('.-last-id').val(id);
			},
			getStat: function() {

				var status   = '',
					select   = app.PropertyLink.controls.select,
					stat     = app.PropertyLink.controls.stat,
					data     = {iblock_id: select.val()},
					syncTime = 3000;

				app.PropertyLink.process(true);

				var syncer = setTimeout(function linkProperties() {

					app.Service.request('POST', 'getStat', data)
						.done(function(resp) {
							app.PropertyLink.process(false);
							 if (resp.status == 'OK') {
								app.PropertyLink.events.openPopup(resp.data);
							 }

						})
						.fail(function() {
							clearTimeout(syncer);
							app.PropertyLink.process(false);
						})
					}, syncTime);

				

			},
			openPopup: function(data) {

			var dialogDiv = $(app.PropertyLink.controls.dialog.DIV),
				name      = app.PropertyLink.controls.select.find(':selected').attr('data'),
				title     = ' "{{NAME}}"'.replace('{{NAME}}', name);

			if (data.stat) {
				var	lineTmpl  = '<tr> \
								<td>{{SECTION}}</td> \
								<td>{{COUNT}}</td> \
							</tr>',
				tmpl      = '<table class="ppx-stat"> \
								<tr> \
									<th>{{SECT}}</th> \
									<th>{{COUNTS}}</th> \
								</tr>'
								.replace('{{SECT}}', BX.message('pxpl_popup_sect'))
								.replace('{{COUNTS}}', BX.message('pxpl_popup_count'));

				$.each( data.stat, function( section, count ) {

					tmpl = tmpl + lineTmpl
							.replace('{{SECTION}}', section)
							.replace('{{COUNT}}', count);

				});

				tmpl = tmpl + '</table>';

			} else {
				tmpl = BX.message('pxpl_nostat');
			}
			

			app.PropertyLink.controls.dialog.SetTitle(BX.message('pxpl_popup_title'));
			app.PropertyLink.controls.dialog.SetHead(BX.message('pxpl_popup_head') + title);
			app.PropertyLink.controls.dialog.SetContent(tmpl);
			app.PropertyLink.controls.dialog.SetSize({ width : 700, height: 300 });
			app.PropertyLink.controls.dialog.Show();

			}
		}
	}

})(window);