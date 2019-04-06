(function(w) {
	'use strict';

	var app = w.app = {};

	app.Service = {
		url     : '/bitrix/tools/asdaff.proplink_ajax.php',
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

	app.PropLink = {
		controls: {},
		init: function(initData) {

			// set controls
			app.PropLink.controls.select = $('.-iblock-select');
			app.PropLink.controls.saveCb = $('.-save-links');
			app.PropLink.controls.eraseCb = $('.-erase-links');
			app.PropLink.controls.submit = $('.-link-properties');
			app.PropLink.controls.result = $('.-result-message');
			app.PropLink.controls.clear  = $('.-clear');

			app.PropLink.controls.tabs   = $(".-tab-btns");
			app.PropLink.controls.save   = $(".-save-settings");
			app.PropLink.controls.formEx = $(".-settings-form");
			app.PropLink.controls.addEx  = $('.-add-ext');
			app.PropLink.controls.stat   = $('.-stat');

			app.PropLink.controls.openStat = $('.-openStat');
			app.PropLink.controls.dialog   = new BX.CDialog();

			app.PropLink.controls.submit.on('click', app.PropLink.events.makeSync);
			app.PropLink.controls.openStat.on('click', app.PropLink.events.getStat);
			app.PropLink.controls.clear.on('click', app.PropLink.events.clear);
			app.PropLink.controls.save.on('click', app.PropLink.events.saveSettings);
			app.PropLink.controls.addEx.on('click', app.PropLink.events.addExt);
		},
		showMessage: function(response) {

			app.PropLink.controls.formMessage.removeClass('error');

			if (response) {
			} else {
			}

		},
		process: function(state) {
			app.PropLink.controls.submit
				.add(app.PropLink.controls.clear)
				.add(app.PropLink.controls.openStat)
				.prop('disabled', state);

			if (state) {
				BX.showWait();
			} else {
				BX.closeWait();
			}
		},
		events: {
			saveSettings: function(){
				app.PropLink.controls.formEx.submit();
				console.log("save");
			},
			showButton: function(tab){
				app.PropLink.controls.tabs.hide().filter('[data-tab-id="'+tab+'"]').show();
			},
			clear: function(){

				var status   = '',
					select   = app.PropLink.controls.select,
					result   = app.PropLink.controls.result,
					data     = {iblock_id: select.val(), pack : 0, size : 50},
					syncTime = 3000,
					linked   = 0;

				app.PropLink.process(true);
				result.empty().removeClass('error success');

				var syncer = setTimeout(function linkProperties() {

					app.Service.request('POST', 'clearProperties', data)
						.done(function(resp) {
							app.PropLink.process(false);
							 if (resp.status == 'OK') {
								result
									.text(BX.message('cleared'))
									.addClass('success');
								app.PropLink.process(false);

								return false;
							 } else{
							 	result
									.text(BX.message('error_sync') + resp.msg)
									.addClass('error');
							 	app.PropLink.process(false);
							 }

						})
						.fail(function() {
							clearTimeout(syncer);
							app.PropLink.process(false);
						})
					}, syncTime);
			},
			makeSync: function() {

				var status   = '',
					select   = app.PropLink.controls.select,
					result   = app.PropLink.controls.result,
					saveLink = app.PropLink.controls.saveCb.prop('checked') ? 'Y' : 'N',
					eraseLink = app.PropLink.controls.eraseCb.prop('checked') ? 'Y' : 'N',
					data     = {iblock_id: select.val(), save_links: saveLink, erase_links: eraseLink, pack : 0, size : 50},
					syncTime = 3000,
					linked   = 0;

				app.PropLink.process(true);
				result.empty().removeClass('error success');

				var syncer = setTimeout(function linkProperties() {

					app.Service.request('POST', 'linkProperties', data)
						.done(function(resp) {

							if (resp.status == 'ERROR') {
								result
									.text(BX.message('error_sync') + resp.msg)
									.addClass('error');
								app.PropLink.process(false);

								return false;
							}

							var sync = resp.data;

							linked += sync.count ? sync.count : 0;
							status  = BX.message('sync_status').replace('{{linked}}', linked);

							if (sync.status == 'inProgress') {
								data.pack++;
								syncer = setTimeout(linkProperties, syncTime);

								result
									.text(BX.message('sync') + status)
									.addClass('success');

							} else {

								result
									.text(BX.message('success_sync') + status)
									.addClass('success');

								clearTimeout(syncer);
								app.PropLink.process(false);
							}
						})
						.fail(function() {
							clearTimeout(syncer);
							app.PropLink.process(false);
						})
				}, syncTime);
			},
			addExt: function(){

				var mssect = BX.message('section'),
					msprop = BX.message('property');

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
					select   = app.PropLink.controls.select,
					stat     = app.PropLink.controls.stat,
					data     = {iblock_id: select.val()},
					syncTime = 3000;

				app.PropLink.process(true);

				var syncer = setTimeout(function linkProperties() {

					app.Service.request('POST', 'getStat', data)
						.done(function(resp) {
							app.PropLink.process(false);
							 if (resp.status == 'OK') {
								app.PropLink.events.openPopup(resp.data);
							 }

						})
						.fail(function() {
							clearTimeout(syncer);
							app.PropLink.process(false);
						})
					}, syncTime);



			},
			openPopup: function(data) {

			var dialogDiv = $(app.PropLink.controls.dialog.DIV),
				name      = app.PropLink.controls.select.find(':selected').attr('data'),
				title     = ' "{{NAME}}"'.replace('{{NAME}}', name);

			if (data.stat) {
				var	lineTmpl  = '<tr> \
								<td>{{SECTION}}</td> \
								<td>{{COUNT}}</td> \
							</tr>',
				tmpl      = '<table class="stat"> \
								<tr> \
									<th>{{SECT}}</th> \
									<th>{{COUNTS}}</th> \
								</tr>'
								.replace('{{SECT}}', BX.message('popup_sect'))
								.replace('{{COUNTS}}', BX.message('popup_count'));

				$.each( data.stat, function( section, count ) {

					tmpl = tmpl + lineTmpl
							.replace('{{SECTION}}', section)
							.replace('{{COUNT}}', count);

				});

				tmpl = tmpl + '</table>';

			} else {
				tmpl = BX.message('nostat');
			}


			app.PropLink.controls.dialog.SetTitle(BX.message('popup_title'));
			app.PropLink.controls.dialog.SetHead(BX.message('popup_head') + title);
			app.PropLink.controls.dialog.SetContent(tmpl);
			app.PropLink.controls.dialog.SetSize({ width : 700, height: 300 });
			app.PropLink.controls.dialog.Show();

			}
		}
	}

})(window);
