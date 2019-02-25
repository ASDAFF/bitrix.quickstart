<?
//платежные системы
$PayDefault = COption::GetOptionString($module_id,'paySystems','Y');
if($PayDefault != 'Y')
	$tmpPaySys=unserialize($PayDefault);

$paySysS=CSalePaySystem::GetList(array(),array('ACTIVE'=>'Y'));
$paySysHtml='<select name="paySystems[]" multiple size="5">';
while($paySys=$paySysS->Fetch()){
	$paySysHtml.='<option value="'.$paySys['ID'].'" ';
	if($PayDefault == 'Y') {
		$name = strtolower($paySys['NAME']);
		if( strpos($name, GetMessage('IPOLSDEK_cashe')) === false && 
			strpos($name, GetMessage('IPOLSDEK_cashe2')) === false && 
			strpos($name, GetMessage('IPOLSDEK_cashe3')) === false)
			$paySysHtml.='selected';
	}
	else {
		if(in_array($paySys['ID'],$tmpPaySys))
			$paySysHtml.='selected';
	}
	$paySysHtml.='>'.$paySys['NAME'].'</option>';
}
$paySysHtml.="</select>";
?>
<link href="/bitrix/js/<?=$module_id?>/jquery-ui.css?<?=mktime()?>" type="text/css"  rel="stylesheet" />
<link href="/bitrix/js/<?=$module_id?>/jquery-ui.structure.css?<?=mktime()?>" type="text/css"  rel="stylesheet" />
<script src='/bitrix/js/<?=$module_id?>/jquery-ui.js?<?=mktime()?>' type='text/javascript'></script>
<?=sdekdriver::getModuleExt('courierTimeCheck')?>
<style>
	.PropHint { 
		background: url("/bitrix/images/ipol.sdek/hint.gif") no-repeat transparent;
		display: inline-block;
		height: 12px;
		position: relative;
		width: 12px;
	}
	.b-popup { 
		background-color: #FEFEFE;
		border: 1px solid #9A9B9B;
		box-shadow: 0px 0px 10px #B9B9B9;
		display: none;
		font-size: 12px;
		padding: 19px 13px 15px;
		position: absolute;
		top: 38px;
		width: 300px;
		z-index: 50;
	}
	.b-popup .pop-text { 
		margin-bottom: 10px;
		color:#000;
	}
	.pop-text i {color:#AC12B1;}
	.b-popup .close { 
		background: url("/bitrix/images/ipol.sdek/popup_close.gif") no-repeat transparent;
		cursor: pointer;
		height: 10px;
		position: absolute;
		right: 4px;
		top: 4px;
		width: 10px;
	}
	.IPOLSDEK_clz{
		background:url(/bitrix/panel/main/images/bx-admin-sprite-small.png) 0px -2989px no-repeat; 
		width:18px; 
		height:18px;
		cursor: pointer;
		margin-left:100%;
	}
	.IPOLSDEK_clz:hover{
		background-position: -0px -3016px;
	}
	.errorText{
		color:red;
		font-size:11px;
	}
	.IPOLSDEK_errorInput{
		background-color: #ffb4b4 !important;
	}
	.IPOLSDEK_sender{
		border: 1px dotted black !important;
		margin: 5px !important;
		float: left;
	}
	.subHeading td{
		padding: 8px 70px 10px !important;
		background-color: #EDF7F9;
		border-top: 11px solid #F5F9F9;
		border-bottom: 11px solid #F5F9F9;
		color: #4B6267;
		font-size: 14px;
		font-weight: bold;
		text-align: center !important;
		text-shadow: 0px 1px #FFF;
	}
	.IPOLSDEK_errTextCourier{
		font-size:10px;
		max-width:300px;
		margin:auto;
		color: red;
	}
	.IPOLSDEK_sepTable{
		width: 50%;
		float: left;
		text-align: center;
		font-weight: bold;
	}

	#IPOLSDEK_accountWnd #IPOLSDEK_addAcc{
		display:none;
	}
	#IPOLSDEK_accountWnd.IPOLSDEK_addAcc #IPOLSDEK_addAcc{
		display:table;
	}
	#IPOLSDEK_accountWnd #IPOLSDEK_newAcc{
		display:inline;
	}
	#IPOLSDEK_accountWnd.IPOLSDEK_addAcc #IPOLSDEK_newAcc{
		display:none;
	}
	#IPOLSDEK_accountTable{
		margin:auto;
	}
	#IPOLSDEK_accountTable td{
		text-align:center;
	}
</style>
<script>
	<?=sdekdriver::getModuleExt('mask_input')?>

	IPOLSDEK_setups.base = {
		ready: function(){
			$('[name="termInc"]').on('keyup',IPOLSDEK_setups.base.onTermChange);
			$('[name="mindEnsure"]').on('change',IPOLSDEK_setups.base.onEnsChange);
			IPOLSDEK_setups.base.senders.init();
			IPOLSDEK_setups.base.depature.init();
			IPOLSDEK_setups.base.onEnsChange();
		},

		senderCities: [<?=$senderCitiesJS?>],

		// кнопки хэдера
		clearCache: function(){
			$('#IPOLSDEK_cacheKiller').attr('disabled','disabled');
			IPOLSDEK_setups.ajax({
				data: {isdek_action:'clearCache'},
				success: function(){
					alert("<?=GetMessage('IPOLSDEK_LBL_CACHEKILLED')?>");
					$('#IPOLSDEK_cacheKiller').removeAttr('disabled');
				}
			});
		},

		ressurect: function(){
			$('#IPOLSDEK_ressurect').attr('disabled','disabled');
			IPOLSDEK_setups.ajax({
				data: {isdek_action:'ressurect'},
				success: function(){
					$('#IPOLSDEK_ressurect').closest('tr').replaceWith(' ');
				}
			});
		},

		// аккаунты
		accounts: {
			wnd: false,

			show: function(){
				if(!IPOLSDEK_setups.base.accounts.wnd){
					IPOLSDEK_setups.base.accounts.wnd = new BX.CDialog({
						title: "<?=GetMessage('IPOLSDEK_OTHR_accHeader')?>",
						content: '<div id="IPOLSDEK_accountWnd"><div style="text-align:center"><img style="border:none" src="/bitrix/images/ipol.sdek/bigAjax.gif"></div></div>',
						icon: 'head-block',
						resizable: true,
						draggable: true,
						height: '270',
						width: '600',
						buttons: []
					});
				}
				IPOLSDEK_setups.base.accounts.wnd.Show();
				IPOLSDEK_setups.base.accounts.markAjax();
				IPOLSDEK_setups.base.accounts.requestAcs();
			},

			markAjax: function(){
				$('#IPOLSDEK_accountWnd').removeClass('IPOLSDEK_addAcc');
				$('#IPOLSDEK_accountWnd').html('<div style="text-align:center"><img src="/bitrix/images/ipol.sdek/bigAjax.gif" style="border:none"></div>');
			},

			requestAcs: function(){
				IPOLSDEK_setups.ajax({
					data: {isdek_action:'callAccounts'},
					dataType: 'json',
					success: IPOLSDEK_setups.base.accounts.loadAcs
				});
			},

			loadAcs: function(data){
				var html = '<table id="IPOLSDEK_accountTable">';
				var cnt=0;
				for(var i in data){
					cnt++;
					html += "<tr><td>"+data[i].account.ACCOUNT+"</td><td>"+((data[i].default)?"<?=GetMessage('IPOLSDEK_OTHR_accDefault')?>":"<input type='button' value='<?=GetMessage('IPOLSDEK_OTHR_accMakeDefault')?>' onclick='IPOLSDEK_setups.base.accounts.makeDefault("+i+")'>")+"</td><td>"+data[i].account.LABEL+"</td><td><input type='button' value='<?=GetMessage('IPOLSDEK_OTHR_accDelete')?>' onclick='"+((data[i].default)?"IPOLSDEK_setups.base.accounts.defaultDelete("+i+")":"IPOLSDEK_setups.base.accounts.delete("+i+")")+"'></td></tr>";
				}
				if(cnt == 1)
					html = html.replace('firstDelete','lastDelete');
				html += "</table><table id='IPOLSDEK_addAcc'><tr><td>Account</td><td><input type='text' size='35' id='IPOLSDEK_addAccAccount'></td></tr><tr><td>Password</td><td><input type='text' size='35' id='IPOLSDEK_addAccPassword'></td></tr><tr><td><?=GetMessage('IPOLSDEK_OTHR_acComent')?></td><td><input type='text' size='15' id='IPOLSDEK_addAccComment'></td></tr><tr><td colspan='2'><input type='button' value='<?=GetMessage('IPOLSDEK_OTHR_accAdd')?>' onclick='IPOLSDEK_setups.base.accounts.add()'></td></tr></table><input id='IPOLSDEK_newAcc' type='button' value='<?=GetMessage('IPOLSDEK_OTHR_accNew')?>' onclick=\"IPOLSDEK_setups.base.accounts.new()\"/>";
				$('#IPOLSDEK_accountWnd').html(html);
				$('#IPOLSDEK_addAccComment').on('keyup',IPOLSDEK_setups.base.accounts.onPressComment);
			},

				// добавление
			new: function(){
				$('#IPOLSDEK_accountWnd').addClass('IPOLSDEK_addAcc');
			},
			add: function(){
				var fail = false;
				var account  = $('#IPOLSDEK_addAccAccount').val();
				var password = $('#IPOLSDEK_addAccPassword').val();
				var comment  = $('#IPOLSDEK_addAccComment').val();
				if(account.length != 32){
					$('#IPOLSDEK_addAccAccount').addClass('IPOLSDEK_errorInput');
					fail=true;
				}else
					$('#IPOLSDEK_addAccAccount').removeClass('IPOLSDEK_errorInput');

				if(password.length != 32){
					$('#IPOLSDEK_addAccPassword').addClass('IPOLSDEK_errorInput');
					fail=true;
				}else
					$('#IPOLSDEK_addAccPassword').removeClass('IPOLSDEK_errorInput');

				if(comment.length > 15)
					comment = comment.substr(0,15);

				if(!fail){
					IPOLSDEK_setups.base.accounts.markAjax();
					IPOLSDEK_setups.ajax({
						data: {isdek_action:'newAccount',ACCOUNT:account,PASSWORD:password,LABEL:comment},
						dataType: 'json',
						success: IPOLSDEK_setups.base.accounts.onAnswerAccount
					});
				}
			},
			onAnswerAccount: function(data){
				if(typeof(data.text) != 'undefined' && data.text)
					alert(data.text);
				if(data.result == 'collapse')
					IPOLSDEK_setups.reload();
				else
					IPOLSDEK_setups.base.accounts.requestAcs();
			},
				// удаление
			defaultDelete: function(id){
				if(confirm("<?=GetMessage("IPOLSDEK_OTHR_accDefaultDelete")?>"))
					IPOLSDEK_setups.base.accounts.delete(id);
			},

			lastDelete: function(id){
				if(confirm("<?=GetMessage("IPOLSDEK_OTHR_accLastDelete")?>"))
					IPOLSDEK_setups.base.accounts.delete(id);
			},

			delete: function(id){
				IPOLSDEK_setups.base.accounts.markAjax();
				IPOLSDEK_setups.ajax({
					data: {isdek_action:'optionDeleteAccount',ID:id},
					dataType: 'json',
					success: IPOLSDEK_setups.base.accounts.onAnswerAccount
				});
			},
				// основной
			makeDefault: function(id){
				if(confirm("<?=GetMessage("IPOLSDEK_OTHR_accDefaultDelete")?>")){
					IPOLSDEK_setups.base.accounts.markAjax();
					IPOLSDEK_setups.ajax({
						data: {isdek_action:'optionMakeAccDefault',ID:id},
						dataType: 'json',
						success: IPOLSDEK_setups.base.accounts.onAnswerAccount
					});
				}
			},

			onPressComment: function(comInput){
				if($(comInput.currentTarget).val().length > 15)
					$(comInput.currentTarget).val($(comInput.currentTarget).val().substr(0,15));
			}
		},

		// отправители
		senders: {
			init: function(){
				if($('.IPOLSDEK_sender').length)
					$('.IPOLSDEK_sender').each(function(){IPOLSDEK_setups.base.senders.setEvents($(this))});
			},

			onTurnOn: function(wat){
				if(wat.attr('checked'))
					alert('<?=GetMessage("IPOLSDEK_LABEL_sendersWarning")?>');
			},

			add: function(settings){
				if(typeof(settings) == 'undefined') settings = {senderName:"",cityName:"",courierCity:'',courierStreet:'',courierHouse:'',courierFlat:'',courierPhone:'',courierName:'',courierTimeBeg:'',courierTimeEnd:''};
				if(typeof(settings.courierComment) == 'undefined') settings.courierComment = '';
				var cnt = $('.IPOLSDEK_sender').length;
				var HTML = "<table class='IPOLSDEK_sender' id='IPOLSDEK_added'>";
					HTML += "<tr><td><?=GetMessage("IPOLSDEK_LBL_SENDER")?></td><td><input type='text' name='senders["+cnt+"][senderName]' value='"+settings.senderName+"'></td></tr>";
					HTML += "<tr><td><?=GetMessage("IPOLSDEK_LBL_COURIERTIME")?></td><td><input type='text' style='width:56px' name='senders["+cnt+"][courierTimeBeg]' value='"+settings.courierTimeBeg+"'> - <input type='text' style='width:56px' name='senders["+cnt+"][courierTimeEnd]' value='"+settings.courierTimeEnd+"'></td></tr>";
					HTML += "<tr><td><?=GetMessage("IPOLSDEK_JS_SOD_courierCity")?></td><td><input type='text' class='IPOLSDEK_senderCity' value='"+settings.cityName+"'/><input type='hidden' name='senders["+cnt+"][courierCity]' value='"+settings.courierCity+"'></td></tr>";
					HTML += "<tr><td><?=GetMessage("IPOLSDEK_JS_SOD_courierStreet")?></td><td><input type='text' name='senders["+cnt+"][courierStreet]' value='"+settings.courierStreet+"'></td></tr>";
					HTML += "<tr><td><?=GetMessage("IPOLSDEK_JS_SOD_courierHouse")?></td><td><input type='text' name='senders["+cnt+"][courierHouse]' value='"+settings.courierHouse+"'></td></tr>";
					HTML += "<tr><td><?=GetMessage("IPOLSDEK_JS_SOD_courierFlat")?></td><td><input type='text' name='senders["+cnt+"][courierFlat]' value='"+settings.courierFlat+"'></td></tr>";
					HTML += "<tr><td><?=GetMessage("IPOLSDEK_JS_SOD_courierPhone")?></td><td><input type='text' class='IPOLSDEK_phone' name='senders["+cnt+"][courierPhone]' value='"+settings.courierPhone+"'></td></tr>";
					HTML += "<tr><td><?=GetMessage("IPOLSDEK_JS_SOD_courierName")?></td><td><input type='text' name='senders["+cnt+"][courierName]' value='"+settings.courierName+"'></td></tr>";
					HTML += "<tr><td><?=GetMessage("IPOLSDEK_JS_SOD_courierComment")?></td><td><input type='text' name='senders["+cnt+"][courierComment]' value='"+settings.courierComment+"'></td></tr>";
					HTML += "</table>";
				$('#IPOLSDEK_sendersPlace').append(HTML);
				IPOLSDEK_setups.base.senders.setEvents('#IPOLSDEK_added');
				$("#IPOLSDEK_added").removeAttr('id');
			},

			changeCity: function(ev,ui){
				window.setTimeout(function(){
						$(arguments[0]).val(arguments[1]);
					},100,ev.target,ui.item.label);
				$(ev.target).siblings("[type='hidden']").val(ui.item.value);
			},

			setEvents: function(mark){
				var chz = (typeof(mark.html) == 'undefined') ? $(mark) : mark;
				chz.find(".IPOLSDEK_senderCity").autocomplete({
				  source: IPOLSDEK_setups.base.senderCities,
				  select: function(ev,ui){IPOLSDEK_setups.base.senders.changeCity(ev,ui);}
				});
				chz.find(".IPOLSDEK_phone").mask("99999999999");
				chz.find("[name*='[courierTimeBeg]']").mask("29:59").on('change',IPOLSDEK_setups.base.senders.timeChanged);
				chz.find("[name*='[courierTimeEnd]']").mask("29:59").on('change',IPOLSDEK_setups.base.senders.timeChanged);
			},

			timeChanged: function(link){ // IPOLSDEK_courierTimeCheck - /bitrix/js/ipol.sdek/courierTimeCheck.php
				var tr = $(link.delegateTarget).parents('tr:first');
				var check = IPOLSDEK_courierTimeCheck(tr.find("[name*='[courierTimeBeg]']").val(),tr.find("[name*='[courierTimeEnd]']").val());

				if(check === true || (!tr.find('[name*="[courierTimeBeg]"]').val() && !tr.find('[name*="[courierTimeEnd]"]').val())){
					tr.find('.IPOLSDEK_badInput').removeClass('IPOLSDEK_badInput');
					tr.parent().find('.IPOLSDEK_errTextCourier').html('');
				}else{
					if(check.error == 'start' || check.error == 'both')
						tr.find('[name*="[courierTimeBeg]"]').addClass('IPOLSDEK_badInput');
					if(check.error == 'end' || check.error == 'both')
						tr.find('[name*="[courierTimeEnd]"]').addClass('IPOLSDEK_badInput');
					tr.parent().find('.IPOLSDEK_errTextCourier').html(check.text);
				}
			},
		},

		// Города-отправители
		depature:{
			add: function(){
				$('#IPOLSDEK_addDeparturePlace').append("<div><input type='text' class='IPOLSDEK_addDeparture rescent'><input type='hidden' name='addDeparture[]'></div>");
				IPOLSDEK_setups.base.depature.input($('.IPOLSDEK_addDeparture.rescent'));
				$('.IPOLSDEK_addDeparture.rescent').removeClass('rescent');
			},
			input: function(wat){
				wat.autocomplete({
				  source: IPOLSDEK_setups.base.senderCities,
				  select: IPOLSDEK_setups.base.depature.onSelect
				});
			},
			init: function(){
				$('.IPOLSDEK_addDeparture').each(function(){IPOLSDEK_setups.base.depature.input($(this));});
			},
			onSelect: function(ev,ui){
				window.setTimeout(function(){
					$(arguments[0]).val(arguments[1]);
				},100,ev.target,ui.item.label);
				$(ev.target).siblings("[type='hidden']").val(ui.item.value);
			},
			delete: function(wat){
				wat.parent().replaceWith('');
			}
		},

		// сервисные
		serverShow: function(){
			$('.IPOLSDEK_service').each(function(){
				$(this).css('display','table-row');
			});
			$('[onclick^="IPOLSDEK_setups.base.serverShow("]').css('cursor','auto');
			$('[onclick^="IPOLSDEK_setups.base.serverShow("]').css('textDecoration','none');
		},

		counterReset: function(){
			if(confirm('<?=GetMessage('IPOLSDEK_OTHR_schet_ALERT')?>'))
				IPOLSDEK_setups.ajax({
					data: {isdek_action:'killSchet'},
					success: function(data){
						if(data=='1'){
							alert('<?=GetMessage("IPOLSDEK_OTHR_schet_DONE")?>');
							$("[onclick^='IPOLSDEK_setups.base.counterReset(']").parent().html('0');
						}else
							alert('<?=GetMessage("IPOLSDEK_OTHR_schet_NONE")?>'+data);
					}
				});
		},

		clrUpdt: function(){
			if(confirm('<?=GetMessage('IPOLSDEK_OPT_clrUpdt_ALERT')?>')){
				$('.IPOLSDEK_clz').css('display','none');
				IPOLSDEK_setups.ajax({
					data: {isdek_action:'killUpdt'},
					success: function(data){
						if(data=='done')
							$("#IPOLSDEK_updtPlc").replaceWith('');
						else{
							$('.IPOLSDEK_clz').css('display','');
							alert('<?=GetMessage("IPOLSDEK_OPT_clrUpdt_ERR")?>');
						}
					}
				});
			}
		},

		syncList(params){
			var dataObj = {text:false,status:false};
			var reqObj  = {isdek_action: 'callUpdateList',full: true};
			if(typeof(params) == 'undefined'){
				IPOLSDEK_setups.cities.controlSunc();
				dataObj.text = '<?=GetMessage("IPOLSDEK_OTHR_lastModList_START")?>';
			}else{
				dataObj.text   = params.text;
				dataObj.status = params.result;
				if(params.result != 'error')
					reqObj['listDone'] = true;
			}

			if(dataObj.text){
				if($('#IPOLSDEK_syncInfo').length == 0)
					$("[onclick='IPOLSDEK_setups.base.syncList()']").after('<br><span id="IPOLSDEK_syncInfo"></span>');
				$('#IPOLSDEK_syncInfo').html(dataObj.text);
				if(dataObj.status == 'error')
					$('#IPOLSDEK_syncInfo').css('color','red');
			}

			if(dataObj.status != 'error' && dataObj.status != 'end'){
				IPOLSDEK_setups.ajax({
					data: reqObj,
					dataType: 'json',
					success: IPOLSDEK_setups.base.syncList,
					error: function(a,b,c){alert("sync "+b+" "+c);}
				});
			}else
				if(dataObj.status == 'end'){
					alert(dataObj.text);
					IPOLSDEK_setups.cities.controlSunc(true);
					IPOLSDEK_setups.reload();
				}
		},

		syncOrdrs: function(){
			$('[onclick="IPOLSDEK_setups.base.syncOrdrs()"]').css('display','none');
			IPOLSDEK_setups.ajax({
				data: {'isdek_action':'callOrderStates'},
				success: function(data){
					$('#IPOLSDEK_SO').parent().html(data+"&nbsp;<input type='button' value='<?=GetMessage('IPOLSDEK_OTHR_getOutLst_BUTTON')?>' id='IPOLSDEK_SO' onclick='IPOLSDEK_setups.base.syncOrdrs()'/>");
					IPOLSDEK_setups.table.getTable();
				}
			});
		},

		importCities: function(){
			$('#IPOLSDEK_IMPORTCITIES').attr('disabled','disabled');
			IPOLSDEK_setups.ajax({
				data: {'isdek_action':'setImport'},
				success: IPOLSDEK_setups.reload
			});
		},

		autoloads: function(){
			$('#IPOLSDEK_AUTOLOADS').attr('disabled','disabled');
			IPOLSDEK_setups.ajax({
				data: {'isdek_action':'setAutoloads'},
				success: IPOLSDEK_setups.reload
			});
		},

		logOff: function(){
			$("#IPOLSDEK_logoffer").attr('disabled','disabled');
			if(confirm('<?=GetMessage("IPOLSDEK_LBL_ISLOGOFF")?>'))
				IPOLSDEK_setups.ajax({
					data: {'isdek_action':'logoff'},
					success: IPOLSDEK_setups.reload
				});
			else
				$("#IPOLSDEK_logoffer").removeAttr('disabled');
		},

		// прочие
		onTermChange: function(){
			var day = parseInt($('[name="termInc"]').val());
			if(isNaN(day))
				day = '';			
			$('[name="termInc"]').val(day);
		},

		onEnsChange: function(){
			if($('[name="mindEnsure"]').attr('checked'))
				$('[name="ensureProc"]').closest('tr').css('display','');
			else
				$('[name="ensureProc"]').closest('tr').css('display','none');
		}
	};
</script>

<?
foreach(array("depature","showInOrders","realSeller","addDeparture","shipments","prntActOrdr","numberOfPrints","NDSUseCatalog","address","pvzPicker","noPVZnoOrder","hideNal","hideNOC","autoSelOne","profilesMode","cntExpress","mindEnsure","AS","noVats","statusSTORE","statusTRANZT","statusCORIER","tarifs","warhouses","dostTimeout","timeoutRollback","TURNOFF","TARSHOW") as $code)
	sdekOption::placeHint($code);

$deadServerCheck = COption::GetOptionString($module_id,'sdekDeadServer',false);
if($deadServerCheck && (mktime() - $deadServerCheck) < (COption::GetOptionString($module_id,'timeoutRollback',15) * 60)){?>
	<tr><td colspan='2'>
		<div class="adm-info-message-wrap adm-info-message-red">
		  <div class="adm-info-message">
			<div class="adm-info-message-title"><?=GetMessage('IPOLSDEK_DEAD_SERVER_HEADER')?></div>
				<?=GetMessage('IPOLSDEK_DEAD_SERVER_TITLE')?>&nbsp;<?=date('H:i:s d.m.Y',$deadServerCheck)?>.
				<br>
				<input type='button' id='IPOLSDEK_ressurect' onclick='IPOLSDEK_setups.base.ressurect()' value='<?=GetMessage("IPOLSDEK_DEAD_SERVER_BTN")?>'>
			<div class="adm-info-message-icon"></div>
		  </div>
		</div>
	</td></tr>
<?}

if(file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/js/".$module_id."/errorLog.txt")){
	$errorStr=file_get_contents($_SERVER["DOCUMENT_ROOT"]."/bitrix/js/".$module_id."/errorLog.txt");
	if(strlen($errorStr)>0){?>
		<tr><td colspan='2'>
			<div class="adm-info-message-wrap adm-info-message-red">
			  <div class="adm-info-message">
				<div class="adm-info-message-title"><?=GetMessage('IPOLSDEK_FNDD_ERR_HEADER')?></div>
					<?=GetMessage('IPOLSDEK_FNDD_ERR_TITLE')?>
				<div class="adm-info-message-icon"></div>
			  </div>
			</div>
		</td></tr>
	<?}
}
if(file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/js/".$module_id."/hint.txt")){
	$updateStr=file_get_contents($_SERVER["DOCUMENT_ROOT"]."/bitrix/js/".$module_id."/hint.txt");
	if(strlen($updateStr)>0){?>
		<tr id='IPOLSDEK_updtPlc'><td colspan='2'>
			<div class="adm-info-message-wrap">
				<div class="adm-info-message" style='color: #000000'>
					<div class='IPOLSDEK_clz' onclick='IPOLSDEK_setups.base.clrUpdt()'></div>
					<?=$updateStr?>
				</div>
			</div>
		</td></tr>
	<?}
}

$dost = sdekdriver::getDelivery(true);
if($dost){
	if($dost['ACTIVE'] != 'Y'){?>
	<tr><td colspan='2'>
		<div class="adm-info-message-wrap adm-info-message-red">
		  <div class="adm-info-message">
			<div class="adm-info-message-title"><?=GetMessage('IPOLSDEK_NO_ADOST_HEADER')?></div>
				<?=GetMessage('IPOLSDEK_NO_ADOST_TITLE')?>
			<div class="adm-info-message-icon"></div>
		  </div>
		</div>
	</td></tr>
	<?}
}else{?>
	<tr><td colspan='2'>
		<div class="adm-info-message-wrap adm-info-message-red">
		  <div class="adm-info-message">
			<?if($converted){?>
				<div class="adm-info-message-title"><?=GetMessage('IPOLSDEK_NOT_CRTD_HEADER')?></div>
					<?=GetMessage('IPOLSDEK_NOT_CRTD_TITLE')?>				
			<?}else{?>
				<div class="adm-info-message-title"><?=GetMessage('IPOLSDEK_NO_DOST_HEADER')?></div>
					<?=GetMessage('IPOLSDEK_NO_DOST_TITLE')?>
			<?}?>
			<div class="adm-info-message-icon"></div>
		  </div>
		</div>
	</td></tr>
<?}

foreach(sdekExport::getAllProfiles() as $profile)
	if(!sdekHelper::checkTarifAvail($profile)){?>
		<tr><td colspan='2'>
			<div class="adm-info-message-wrap adm-info-message-red">
			  <div class="adm-info-message">
				<div class="adm-info-message-title"><?=GetMessage("IPOLSDEK_NO_PROFILE_HEADER_$profile")?></div>
					<?=GetMessage('IPOLSDEK_NO_PROFILE_TITLE')?>
				<div class="adm-info-message-icon"></div>
			  </div>
			</div>
		</td></tr>
	<?}
?>

<tr>
	<?
		$basicAuth = sdekHelper::getBasicAuth();
		if(!$basicAuth)
			sdekOption::authConsolidation();
		$basicAuth = ($basicAuth) ? $basicAuth['ACCOUNT'] : GetMessage('IPOLSDEK_LBL_BADAUTH');
	?>
	<td align="center"><?=GetMessage("IPOLSDEK_LBL_YLOGIN")?>: <strong><?=$basicAuth?></strong></td>
	<td align="center"><input type='button' id='IPOLSDEK_logoffer' onclick='IPOLSDEK_setups.base.logOff()' value='<?=GetMessage('IPOLSDEK_LBL_DOLOGOFF')?>'>&nbsp;&nbsp;<input type='button' onclick='IPOLSDEK_setups.base.accounts.show()' value='<?=GetMessage('IPOLSDEK_LBL_ACCOUNTS')?>'></td>
</tr>
<tr><td></td><td align="center"><input type='button' id='IPOLSDEK_cacheKiller' onclick='IPOLSDEK_setups.base.clearCache()' value='<?=GetMessage('IPOLSDEK_LBL_CLRCACHE')?>'></td></tr>

<?//Общие?>
<tr class="heading"><td colspan="2" valign="top" align="center"><?=GetMessage("IPOLSDEK_HDR_common")?></td></tr>
<?ShowParamsHTMLByArray($arAllOptions["common"]);?>

<?//Печать?>
<tr class="heading"><td colspan="2" valign="top" align="center"><?=GetMessage("IPOLSDEK_HDR_print")?></td></tr>
<tr><td style="color:#555;" colspan="2">
	<?sdekOption::placeFAQ('PRINT')?>
</td></tr>
<?ShowParamsHTMLByArray($arAllOptions["print"]);?>

<?//Габариты товаров по умолчанию?>
<tr class="heading"><td colspan="2" valign="top" align="center"><?=GetMessage('IPOLSDEK_HDR_MEASUREMENT_DEF')?></td></tr>
<tr><td style="color:#555;" colspan="2">
	<?sdekOption::placeFAQ('GABS')?>
</td></tr>
<?ShowParamsHTMLByArray($arAllOptions["dimensionsDef"]);?>

<?//НДС?>
<tr class="heading"><td colspan="2" valign="top" align="center"><?=GetMessage('IPOLSDEK_HDR_NDS')?></td></tr>
<?ShowParamsHTMLByArray($arAllOptions["NDS"]);?>

<?//Свойства заказа?>
<tr class="heading"><td colspan="2" valign="top" align="center"><?=GetMessage('IPOLSDEK_HDR_requestProps')?></td></tr>
<tr class="subHeading"><td colspan="2" valign="top" align="center"><?=GetMessage('IPOLSDEK_HDR_orderProps')?></td></tr>
<tr><td style="color:#555;" colspan="2">
	<?sdekOption::placeFAQ('PROPS')?>
</td></tr>
<?showOrderOptions();?>
<tr class="subHeading"><td colspan="2" valign="top" align="center"><?=GetMessage('IPOLSDEK_HDR_itemProps')?></td></tr>
<tr><td style="color:#555;" colspan="2">
	<?sdekOption::placeFAQ('IPROPS')?>
</td></tr>
<?ShowParamsHTMLByArray($arAllOptions["itemProps"]);?>
<?//Статусы заказа?>
<tr class="heading"><td colspan="2" valign="top" align="center"><?=GetMessage("IPOLSDEK_HDR_status")?></td></tr>
<tr><td style="color:#555;" colspan="2">
	<?sdekOption::placeFAQ('STATUSES')?>
</td></tr>
<?
	sdekOption::placeStatuses($arAllOptions["status"]);
?>

<?//Виджет?>
<tr class="heading"><td colspan="2" valign="top" align="center"><?=GetMessage("IPOLSDEK_HDR_vidjet")?></td></tr>
<tr><td style="color:#555;" colspan="2">
	<?sdekOption::placeFAQ('WIDGET')?>
</td></tr>
<?ShowParamsHTMLByArray($arAllOptions["vidjet"]);?>

<?//Оформление заказа?>
<tr class="heading"><td colspan="2" valign="top" align="center"><?=GetMessage("IPOLSDEK_HDR_basket")?></td></tr>
<?ShowParamsHTMLByArray($arAllOptions["basket"]);?>

<?// Доставки?>
<tr class="heading"><td colspan="2" valign="top" align="center"><?=GetMessage("IPOLSDEK_HDR_delivery")?></td></tr>
<tr><td colspan="2"><?=GetMessage("IPOLSDEK_FAQ_DELIVERY")?></td></tr>

<?//Платежные системы?>
<tr class="heading"><td colspan="2" valign="top" align="center"><?=GetMessage("IPOLSDEK_OPT_paySystems")?></td></tr>
<tr><td style="color:#555;" colspan="2">
	<?sdekOption::placeFAQ('PAYSYS')?>
</td></tr>
<tr><td colspan="2" style='text-align:center'><?=$paySysHtml?></td></tr>

<tr class="heading"><td colspan="2" valign="top" align="center"><?=GetMessage("IPOLSDEK_HDR_addingService")?></td></tr>
<tr><td colspan="2" valign="top" align="center"><table>
	<?//Тарифы?>
	<tr><td colspan="4" valign="top" align="center"><strong><?=GetMessage("IPOLSDEK_OPT_tarifs")?></strong> <a href='#' class='PropHint' onclick='return IPOLSDEK_setups.popup("pop-tarifs", this);'></a></td></tr>
	<?$arTarifs = sdekdriver::getExtraTarifs();?>
	<tr><th style="width:20px"></th><th><?=GetMessage("IPOLSDEK_TARIF_TABLE_NAME")?></th><th><?=GetMessage("IPOLSDEK_TARIF_TABLE_SHOW")?></th><th><?=GetMessage("IPOLSDEK_TARIF_TABLE_TURNOFF")?></th><th></th></tr>
	<?
	foreach($arTarifs as $tarifId => $tarifOption){?>
		<tr>
			<td style='text-align:center'><?if($tarifOption['DESC']){?><a href='#' class='PropHint' onclick='return IPOLSDEK_setups.popup("pop-AS<?=$tarifId?>",this);'></a><?}?></td>
			<td><?=$tarifOption['NAME']?></td>
			<td align='center'><input type='checkbox' name='tarifs[<?=$tarifId?>][SHOW]' value='Y' <?=($tarifOption['SHOW']=='Y')?"checked":""?> /></td>
			<td align='center'><input type='checkbox' name='tarifs[<?=$tarifId?>][BLOCK]' value='Y' <?=($tarifOption['BLOCK']=='Y')?"checked":""?> /></td>
			<td>
				<? if($tarifOption['DESC']) {?>
				<div id="pop-AS<?=$tarifId?>" class="b-popup" style="display: none; ">
					<div class="pop-text"><?=$tarifOption['DESC']?></div>
					<div class="close" onclick="$(this).closest('.b-popup').hide();"></div>
				</div>
				<?}?>
			</td>
		</tr>
	<?}?>
	<tr><td colspan='2'><br></td></tr>
</table></td></tr>
	<?//Дополнительные услуги?>
<tr><td colspan="2" valign="top" align="center"><table>
	<tr><td colspan="2" valign="top" align="center"><strong><?=GetMessage("IPOLSDEK_OPT_addingService")?></strong> <a href='#' class='PropHint' onclick='return IPOLSDEK_setups.popup("pop-AS", this);'></a></td></tr>
	<?$arAddService = sdekdriver::getExtraOptions();?>
	<tr><th></th><th><?=GetMessage("IPOLSDEK_AS_TABLE_NAME")?></th><th><?=GetMessage("IPOLSDEK_AS_TABLE_SHOW")?></th><th><?=GetMessage("IPOLSDEK_AS_TABLE_DEF")?></th><th></th></tr>
	<?foreach($arAddService as $asId => $adOption){?>
		<tr>
			<td><a href='#' class='PropHint' onclick='return IPOLSDEK_setups.popup("pop-AS<?=$asId?>",this);'></a></td>
			<td><?=$adOption['NAME']?></td>
			<td align='center'><input type='checkbox' name='addingService[<?=$asId?>][SHOW]' value='Y' <?=($adOption['SHOW']=='Y')?"checked":""?> /></td>
			<td align='center'><input type='checkbox' name='addingService[<?=$asId?>][DEF]' value='Y' <?=($adOption['DEF']=='Y')?"checked":""?> /></td>
			<td>
				<div id="pop-AS<?=$asId?>" class="b-popup" style="display: none; ">
					<div class="pop-text"><?=$adOption['DESC']?></div>
					<div class="close" onclick="$(this).closest('.b-popup').hide();"></div>
				</div>
			</td>
		</tr>
	<?}?>
</table></td></tr>

<?// Отправители?>
<tr class="heading"><td colspan="2" valign="top" align="center"><?=GetMessage("IPOLSDEK_HDR_senders")?></td>
</tr>
<tr>
	<td><label for="allowSenders"><?=GetMessage("IPOLSDEK_OPT_allowSenders")?></label></td>
	<td><input id="allowSenders" onchange='IPOLSDEK_setups.base.senders.onTurnOn($(this))' name="allowSenders" value="Y" <?=(COption::GetOptionString($module_id,'allowSenders','N') == 'Y')?'checked':''?> type="checkbox"></td>
</tr>
<tr><td style="color:#555;" colspan="2">
	<?sdekOption::placeFAQ('SENDERS')?>
</td></tr>
<tr><td colspan="2" valign="top" align="center" id="IPOLSDEK_sendersPlace">
	<?
	$svdCt = sdekOption::senders();
	if(is_array($svdCt) && count($svdCt))
		foreach($svdCt as $key => $val){?>
			<table class="IPOLSDEK_sender">
				<tr><td><?=GetMessage('IPOLSDEK_LBL_SENDER')?></td><td><input name="senders[<?=$key?>][senderName]" value='<?=$val['senderName']?>' type="text"></td></tr>
				<tr><td><?=GetMessage("IPOLSDEK_LBL_COURIERTIME")?></td><td><input type='text' style="width:56px" name='senders[<?=$key?>][courierTimeBeg]' value='<?=$val['courierTimeBeg']?>'> - <input type='text' name='senders[<?=$key?>][courierTimeEnd]' style="width:56px" value='<?=$val['courierTimeEnd']?>'></td></tr>
				<tr><td colspan='2'><div class='IPOLSDEK_errTextCourier'></div></td></tr>
				<tr><td><?=GetMessage('IPOLSDEK_JS_SOD_courierCity')?></td><td><input class="IPOLSDEK_senderCity" value='<?=$senderCities[$val['courierCity']]?>' type="text"><input name="senders[<?=$key?>][courierCity]" value='<?=$val['courierCity']?>' type="hidden"></td></tr>
				<tr><td><?=GetMessage('IPOLSDEK_JS_SOD_courierStreet')?></td><td><input name="senders[<?=$key?>][courierStreet]" value='<?=$val['courierStreet']?>' type="text"></td></tr>
				<tr><td><?=GetMessage('IPOLSDEK_JS_SOD_courierHouse')?></td><td><input name="senders[<?=$key?>][courierHouse]" value='<?=$val['courierHouse']?>' type="text"></td></tr>
				<tr><td><?=GetMessage('IPOLSDEK_JS_SOD_courierFlat')?></td><td><input name="senders[<?=$key?>][courierFlat]" value='<?=$val['courierFlat']?>' type="text"></td></tr>
				<tr><td><?=GetMessage('IPOLSDEK_JS_SOD_courierPhone')?></td><td><input class="IPOLSDEK_phone" name="senders[<?=$key?>][courierPhone]" value='<?=$val['courierPhone']?>' type="text"></td></tr>
				<tr><td><?=GetMessage('IPOLSDEK_JS_SOD_courierName')?></td><td><input name="senders[<?=$key?>][courierName]" value='<?=$val['courierName']?>' type="text"></td></tr>
				<tr><td><?=GetMessage('IPOLSDEK_JS_SOD_courierComment')?></td><td><input name="senders[<?=$key?>][courierComment]" value='<?=$val['courierComment']?>' type="text"></td></tr>
			</table>
	<?}?>
</td></tr>
<tr><td colspan="2" valign="top" align="center"><input type='button' value="<?=GetMessage("IPOLSDEK_LBL_ADDSENDER")?>" onclick='IPOLSDEK_setups.base.senders.add()'></td></tr>

<?// Разбиение на города-отправители?>
<tr class="heading"><td colspan="2" valign="top" align="center"><?=GetMessage("IPOLSDEK_HDR_warhouses")?></td>
</tr>
<tr><td style="color:#555;" colspan="2">
	<?sdekOption::placeFAQ('WARHOUSES')?>
</td></tr>
<?ShowParamsHTMLByArray($arAllOptions["warhouses"]);?>
<tr><td colspan='2'>
<?
	$arFounded = array();
	foreach(GetModuleEvents($module_id, "onBeforeShipment", true) as $arEvent)
		$arFounded[]=$arEvent;
	if(!count($arFounded))
		echo GetMessage('IPOLSDEK_OTHR_noWarhouses');
	else{
		echo GetMessage('IPOLSDEK_OTHR_hasWarhouses')."<br><ul>";
		foreach($arFounded as $arEvent)
			echo '<li>'.$arEvent['CALLBACK'].'</li>';
		echo '</ul>';
	}
?>
</td></tr>

<?// Автоотгрузки?>
<?if(COption::GetOptionString($module_id,'autoloads','N') == 'N'){?>
<tr class="heading"><td colspan="2" valign="top" align="center"><?=GetMessage("IPOLSDEK_HDR_autoUploads")?></td>
</tr>
<tr><td style="color:#555;" colspan="2">
	<?sdekOption::placeFAQ('AUTOUPLOADS')?>
</td></tr>
<tr><td colspan='2' style='text-align:center'>
	<br><input type='button' value='<?=GetMessage('IPOLSDEK_OPT_autoloads')?>' id='IPOLSDEK_AUTOLOADS' onclick='IPOLSDEK_setups.base.autoloads()'/>
</td></tr>
<?}?>

<?// Сервисные свойства?>
<tr class="heading" onclick='IPOLSDEK_setups.base.serverShow()' style='cursor:pointer;text-decoration:underline'>
	<td colspan="2" valign="top" align="center"><?=GetMessage("IPOLSDEK_HDR_service")?></td>
</tr> 
<tr style='display:none' class='IPOLSDEK_service'>
	<td><?=GetMessage('IPOLSDEK_OTHR_schet')?></td>
	<td>
	<?
		$tmpVal=COption::GetOptionString($module_id,'schet',0);
		echo $tmpVal;
		if($tmpVal>0){
	?> <input type='button' value='<?=GetMessage('IPOLSDEK_OTHR_schet_BUTTON')?>' onclick='IPOLSDEK_setups.base.counterReset()'/>
	<?}?>
	</td>
</tr>
<tr style='display:none' class='IPOLSDEK_service'>
	<td><?=GetMessage('IPOLSDEK_OTHR_lastModList')?></td>
	<td>
		<? $ft = filemtime($_SERVER["DOCUMENT_ROOT"]."/bitrix/js/".$module_id."/list.php");?>
		<span id='IPOLSDEK_updtTime'><?=($ft)?date("d.m.Y H:i:s",$ft):GetMessage("IPOLSDEK_OTHR_NOTCOMMITED");?></span>
		<input id='IPOLSDEK_sT_sunc' type='button' value='<?=GetMessage('IPOLSDEK_OTHR_lastModList_BUTTON')?>' onclick='IPOLSDEK_setups.base.syncList()'/>
	</td>
</tr>		
<tr style='display:none' class='IPOLSDEK_service'>
	<td><?=GetMessage('IPOLSDEK_OPT_statCync')?></td>
	<td>
		<?	$optVal = COption::GetOptionString($module_id,'statCync',0);
			if($optVal>0) echo date("d.m.Y H:i:s",$optVal);
			else echo GetMessage('IPOLSDEK_OTHR_NOTCOMMITED');
		?>
		<input type='button' value='<?=GetMessage('IPOLSDEK_OTHR_getOutLst_BUTTON')?>' id='IPOLSDEK_SO' onclick='IPOLSDEK_setups.base.syncOrdrs()'/>
	</td>
</tr>
<tr style='display:none' class='IPOLSDEK_service'>
	<td><?=GetMessage('IPOLSDEK_OPT_dostTimeout')?></td>
	<td>
		<?	
			$optVal = COption::GetOptionString($module_id,'dostTimeout',6);
			if(floatval($optVal)<=0) $optVal=6;
		?>
		<input type='text' value='<?=$optVal?>' name='dostTimeout' size="1"/>
	</td>
</tr>
<tr style='display:none' class='IPOLSDEK_service'>
	<td><?=GetMessage('IPOLSDEK_OPT_timeoutRollback')?></td>
	<td>
		<?
			$optVal = COption::GetOptionString($module_id,'timeoutRollback',15);
			if(floatval($optVal)<=0) $optVal=15;
		?>
		<input type='text' value='<?=$optVal?>' name='timeoutRollback' size="1"/>
	</td>
</tr>
<tr style='display:none' class='IPOLSDEK_service'><td colspan='2' style='text-align:center'>
	<br><input type='button' value='<?=GetMessage('IPOLSDEK_OTHR_importCities_BUTTON')?>' id='IPOLSDEK_IMPORTCITIES' onclick='IPOLSDEK_setups.base.importCities()'/>
</td></tr>