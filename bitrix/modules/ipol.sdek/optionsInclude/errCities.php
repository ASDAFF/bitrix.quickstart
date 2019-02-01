<?
	$countryModul = sdekOption::getCountries(true);
	$countries = CSaleLocation::GetCountryList();
	$svdCountries = sdekOption::zaDEjsonit(json_decode(COption::GetOptionString($module_id,'countries','{"rus":{"act":"Y"}}'),true));

	$arCountry = array();
	$arCountryList = array();
	while($country=$countries->Fetch())
		$arCountry[$country['ID']] = $country['NAME'];

	$firstActive = false;
	foreach($countryModul as $countryCode => $countryDescr){
		foreach($countryDescr['NAME'] as $possibleName){
			$fnded = in_array($possibleName,$arCountry);
			if($fnded)
				break;
		}
		if(!$fnded)
			unset($countryModul[$countryCode]);
		elseif(!$firstActive)
			$firstActive = $countryCode;

		$arCountryList[] = array(
			'EXISTS'  => ($fnded || $countryCode == 'rus'),
			'CODE'    => $countryCode,
			'NAME'    => $countryDescr['LABEL'],
			'CHECKED' => (array_key_exists($countryCode,$svdCountries) && $svdCountries[$countryCode]['act'] == 'Y')
		);
	}

	$accounts = sqlSdekLogs::getAccountsList(true);

	foreach($accounts as $akkId => $vals)
		$accounts[$akkId] = ($vals['LABEL']) ? $vals['LABEL'] : $vals['ACCOUNT'];

	$accounts = array(0=>GetMessage('IPOLSDEK_TC_DEFAULT')) + $accounts;

	if(cmodule::includeModule('currency')){
		$arCurrencies = array(0=>GetMessage('IPOLSDEK_TC_DEFAULT')." (".CCurrency::GetBaseCurrency().")");
		$dbCurrencies = CCurrency::GetList($by="name",$order="asc");
		while($arCurrency=$dbCurrencies->Fetch())
			$arCurrencies[$arCurrency['CURRENCY']] = $arCurrency['FULL_NAME']." [".$arCurrency['CURRENCY']."]";
	}else
		$arCurrencies = array(0=>GetMessage('IPOLSDEK_TC_DEFAULT'));
?>

<style>
	.ipol_header {
		font-size: 16px;
		cursor: pointer;
		display:block;
		color:#2E569C;
	}

	.ipol_inst {
		display:none; 
		margin-left:10px;
		margin-top:10px;
	}

	.IPOLSDEK_countryButton{
		float: left;
		margin: 5px;
	}
	.IPOLSDEK_countryButton .active{
		color: green !important;
	}

	.IPOLSDEK_bigAjax{
		margin: auto;
		display: block;
		border: none;
	}

	.IPOLSDEK_hiddenTable{
		display:none;
	}

	.IPOLSDEK_city_header{
		background-color: #e0e8ea;
		border-top: 11px solid #F5F9F9;
		border-bottom: 11px solid #F5F9F9;
		color: #4b6267;
		font-size: 14px;
		text-align: center !important;
		text-shadow: 0 1px #fff;
		padding: 8px 4px 10px !important;
		cursor: pointer;
		text-decoration: underline;
		text-align: center;
	}

	#IPOLSDEK_countryContent table{
		width: 100%;
	}

	#IPOLSDEK_countryContent .adm-list-table-header td{
		text-align: center;
	}

	.IPOLSDEK_syncInfoCities{
		margin-bottom: 10px;
	}

	#IPOLSDEK_countries{
		margin: auto !important;
		text-align: center;
	}
	#IPOLSDEK_countries td{
		padding: 5px;
	}

	.IPOLSDEK_countryWarn{
		background: url('/bitrix/images/<?=$module_id?>/trouble.png') no-repeat transparent;
		background-size: contain;
		display: inline-block;
		height: 12px;
		width: 12px;
		cursor: pointer;
	}
</style>

<script>
	IPOLSDEK_setups.cities = {
		country: '<?=($firstActive)?$firstActive:'rus'?>',

		countrySetups: <?=CUtil::PhpToJSObject($svdCountries)?>,

		defaultAcc: '<?=COption::GetOptionString($module_id,'logged',false)?>',

		defaultCur: '<?=(cmodule::includeModule('currency'))?CCurrency::GetBaseCurrency():false?>',

		existed: [],

		makeAjax: function(where){
			$(where).html('<img src="/bitrix/images/<?=$module_id?>/bigAjax.gif" class="IPOLSDEK_bigAjax">');
		},

		checkCountrySetups: function(){
			$('.IPOLSDEK_countryWarn').css('display','none');
			var checks = {'cur':{},'acc':{}};
			var countries = {};
			for(var i in IPOLSDEK_setups.cities.countrySetups){
				if(IPOLSDEK_setups.cities.countrySetups[i]['act'] == 'Y'){
					countries[i] = true;

					var curAcc = $('[id="countries['+i+'][acc]"').val();
					if(typeof(curAcc) == 'undefined' || curAcc == '0')
						curAcc = IPOLSDEK_setups.cities.defaultAcc;
					var curCurrency = $('[id="countries['+i+'][cur]"').val();
					if(typeof(curCurrency) =='undefined' || curCurrency == '0')
						curCurrency = IPOLSDEK_setups.cities.defaultCur;

					if(typeof(checks.acc[curAcc]) != 'undefined'){
						if(checks.acc[curAcc] != curCurrency){
							countries[i] = false;
							for(var j in IPOLSDEK_setups.cities.countrySetups)
								if(IPOLSDEK_setups.cities.countrySetups[j]['act'] == 'Y'){
									var semiAcc = $('#IPOLSDEK_countrySetups_'+j+' .IPOLSDEK_countriesAccPlace select').val();
									if(typeof(semiAcc) == 'undefined' || !semiAcc)
										semiAcc = IPOLSDEK_setups.cities.defaultAcc;
									if(semiAcc == curAcc)
										countries[j] = false;

									if(j == i)
										break;
							}
						}
					}else
						checks.acc[curAcc] = curCurrency;

					if(typeof(checks.cur[curCurrency]) != 'undefined'){
						if(checks.cur[curCurrency] != curAcc){
							countries[i] = false;
							for(var j in IPOLSDEK_setups.cities.countrySetups)
								if(IPOLSDEK_setups.cities.countrySetups[j]['act'] == 'Y'){
									var semiCur = $('#IPOLSDEK_countrySetups_'+j+' select[name*="[cur]"]').val();
									if(typeof(semiCur) == 'undefined' || !semiCur)
										semiCur = IPOLSDEK_setups.cities.defaultCur;
									if(semiCur == curCurrency)
										countries[j] = false;

									if(j == i)
										break;
								}
						}
					}else
						checks.cur[curCurrency] = curAcc;
				}
			}
			for(var i in countries)
				if(!countries[i])
					$('#IPOLSDEK_countrySetups_'+i).find('.IPOLSDEK_countryWarn').css('display','');
		},

		getAccountSelect: function(){
			for(var i in IPOLSDEK_setups.cities.countrySetups){
				IPOLSDEK_setups.ajax({
					data:{
						isdek_action  : 'getAccountSelect',
						default : IPOLSDEK_setups.cities.countrySetups[i]['acc'],
						country : i
					},
					success: function(data){
						var handler = data.substr(0,data.indexOf("<-%->"));
						$('#IPOLSDEK_countrySetups_'+handler+' .IPOLSDEK_countriesAccPlace').html(data.substr(data.indexOf("<-%->")+5).trim());
						$('#IPOLSDEK_countrySetups_'+handler+' .IPOLSDEK_countriesAccPlace select').on('change',IPOLSDEK_setups.cities.checkCountrySetups);
					}
				});
			}
		},

		switchCountry: function(country){
			IPOLSDEK_setups.cities.existed = [];
			IPOLSDEK_setups.cities.country = country;
			$('.IPOLSDEK_countryButton .active').removeClass('active');
			$('#IPOLSDEK_cB_'+country).addClass('active');
			IPOLSDEK_setups.cities.callCountry();
		},

		callCountry: function(){
			IPOLSDEK_setups.cities.makeAjax('#IPOLSDEK_countryContent');
			IPOLSDEK_setups.ajax({
				data:{
					isdek_action  : 'getCountryHeaderCities',
					country : IPOLSDEK_setups.cities.country,
				},
				success : function(data){$('#IPOLSDEK_countryContent').html(data);}
			});
		},

		callCities: function(mode){
			if(IPOLSDEK_setups.inArray(mode,IPOLSDEK_setups.cities.existed))
				IPOLSDEK_setups.cities.hiddenShow('IPOLSDEK_city_'+mode);
			else{
				IPOLSDEK_setups.cities.makeAjax('#IPOLSDEK_city_'+mode);
				IPOLSDEK_setups.ajax({
					data:{
						isdek_action  : 'getCountryDetailCities',
						mode    : mode,
						country : IPOLSDEK_setups.cities.country
					},
					success: function(data){
						if(data.indexOf("%"+IPOLSDEK_setups.cities.country+"%") == -1)
							return;
						IPOLSDEK_setups.cities.existed.push(mode);
						$('#IPOLSDEK_city_'+mode).html(data.substr(IPOLSDEK_setups.cities.country.length+2));
					}
				});
			}
		},

		hiddenShow: function(wat){
			var hndl = $('#'+wat);
			if(hndl.hasClass("IPOLSDEK_hiddenTable"))
				hndl.removeClass("IPOLSDEK_hiddenTable");
			else
				hndl.addClass("IPOLSDEK_hiddenTable");
		},

		sunc: function(data){
			IPOLSDEK_setups.cities.controlSunc();
			if($('#IPOLSDEK_syncInfoCities').length == 0)
					$("#IPOLSDEK_suncLog").append('<div id="IPOLSDEK_syncInfoCities" class="IPOLSDEK_syncInfoCities"></div>');
			$('#IPOLSDEK_syncInfoCities').append("<?=GetMessage("IPOLSDEK_OTHR_lastModList_STARTCITY")?><br>");

			IPOLSDEK_setups.cities.suncProceed();
		},

		rewrite: function(){
			if(confirm("<?=GetMessage('IPOLSDEK_LBL_SURETOREWRITE')?>")){
				IPOLSDEK_setups.cities.controlSunc();
				IPOLSDEK_setups.ajax({
					data:{isdek_action: 'goSlaughterCities',mode:'json'},
					dataType: 'json',
					success: IPOLSDEK_setups.cities.suncProceed
				});
			}
		},

		suncProceed: function(data){
			if(!arguments.length)
				var data = {text:false,result:false};

			if(data.text){
				$('#IPOLSDEK_syncInfoCities').append(data.text+"<br>");
				if(data.result == 'error'){
					$('#IPOLSDEK_syncInfoCities').css('color','red');
					$('#IPOLSDEK_syncInfoCities').removeAttr("id");
				}
			}

			if(data.result == 'end'){
				$('#IPOLSDEK_syncInfoCities').css('color','green');
				$('#IPOLSDEK_syncInfoCities').removeAttr("id");
				IPOLSDEK_setups.cities.controlSunc(true);
				IPOLSDEK_setups.cities.callCountry();
			}else
				if(data.result != 'error' && data.result != 'done')
					IPOLSDEK_setups.ajax({
						data:{isdek_action: 'callUpdateList'},
						dataType: 'json',
						success: IPOLSDEK_setups.cities.suncProceed
					});
		},

		controlSunc: function(isEn){
			if(!arguments.length){
				$('#IPOLSDEK_cT_sunc').attr('disabled','disabled');
				$('#IPOLSDEK_cT_rewr').attr('disabled','disabled');
				$('#IPOLSDEK_sT_sunc').attr('disabled','disabled');
				$('#IPOLSDEK_sT_rewr').attr('disabled','disabled');
			}else{
				$('#IPOLSDEK_cT_sunc').removeAttr('disabled');
				$('#IPOLSDEK_cT_rewr').removeAttr('disabled');
				$('#IPOLSDEK_sT_sunc').removeAttr('disabled');
				$('#IPOLSDEK_sT_rewr').removeAttr('disabled');				
			}
		},

		ready: function(){
			IPOLSDEK_setups.cities.switchCountry(IPOLSDEK_setups.cities.country);
			IPOLSDEK_setups.cities.checkCountrySetups();
		},
	};
</script>

<tr class="heading"><td colspan="2" valign="top" align="center"><?=GetMessage('IPOLSDEK_HDR_countries')?></td></tr>
<tr><td style="color:#555;" colspan="2">
	<?sdekOption::placeFAQ('ADDCOUNTRY')?>
</td></tr>

<tr><td	colspan="2"><table id='IPOLSDEK_countries'>
<tr>
	<th><?=GetMessage('IPOLSDEK_TC_WORKOUT')?></th>
	<th><?=GetMessage('IPOLSDEK_TC_NAME')?></th>
	<th><a onclick='IPOLSDEK_setups.cities.getAccountSelect()' style='cursor:pointer' title='<?=GetMessage('IPOLSDEK_TC_ACCOUNT_HINT')?>'><?=GetMessage('IPOLSDEK_TC_ACCOUNT')?></a></th>
	<th><?=GetMessage('IPOLSDEK_TC_CURRENCY')?></th>
</tr>
<?
$soloAccount = (count($accounts) <= 2);
foreach($arCountryList as $country){?>
	<tr id='IPOLSDEK_countrySetups_<?=$country['CODE']?>'>
		<td>
			<?if($country['EXISTS']){?>
				<input type='checkbox' name='countries[<?=$country['CODE']?>][act]' value='Y' <?=($country['CHECKED']) ? 'checked' : ''?>>
			<?}else
				echo GetMessage('IPOLSDEK_SYNCTY_ERR_NOCOUNTRY').GetMessage("IPOLSDEK_SYNCTY_".$country['CODE']);
			?>
		</td>
		<td><?=$country['NAME']?></td>
		<td class='IPOLSDEK_countriesAccPlace'><?=($soloAccount)?GetMessage('IPOLSDEK_TC_DEFAULT'):sdekOption::makeSelect('countries['.$country['CODE'].'][acc]',$accounts,(array_key_exists($country['CODE'],$svdCountries)?$svdCountries[$country['CODE']]['acc']:false),'onchange = IPOLSDEK_setups.cities.checkCountrySetups()')?></td>
		<td><?=sdekOption::makeSelect('countries['.$country['CODE'].'][cur]',$arCurrencies,(array_key_exists($country['CODE'],$svdCountries)?$svdCountries[$country['CODE']]['cur']:false),'onchange = IPOLSDEK_setups.cities.checkCountrySetups()')?></td>
		<td><div class='IPOLSDEK_countryWarn' onclick='IPOLSDEK_setups.popup("pop-BadLink",$(this));'></div></td>
	</tr>
<?}?>
<tr><td><?=sdekOption::placeHint('BadLink')?></td></tr>
</table></td></tr>
<tr><td><?=GetMessage('IPOLSDEK_OPT_noteOrderDateCC')?></td><td><input name="noteOrderDateCC" value="Y" <?=(COption::GetOptionString($module_id,'noteOrderDateCC','N') == 'Y')?'checked':''?> type="checkbox"></td></tr>

<tr class="heading"><td colspan="2" valign="top" align="center"><?=GetMessage('IPOLSDEK_HDR_suncs')?></td></tr>
<tr>
	<td valign="top">
		<input id='IPOLSDEK_cT_sunc' type='button' value='<?=GetMessage('IPOLSDEK_OTHR_suncCities_BUTTON')?>' onclick='IPOLSDEK_setups.cities.sunc()'/><br><br>
		<input id='IPOLSDEK_cT_rewr' type='button' value='<?=GetMessage('IPOLSDEK_OTHR_rewriteCities_BUTTON')?>' onclick='IPOLSDEK_setups.cities.rewrite()'/>
	</td>
	<td style='width:50%' id='IPOLSDEK_suncLog' valign='top'></td>
</tr>

<tr class="heading"><td colspan="2" valign="top" align="center"><?=GetMessage('IPOLSDEK_TAB_CITIES_LOGIN')?></td></tr>
<tr><td style="color:#555;" colspan="2">
	<?sdekOption::placeFAQ('CITYHINT')?>
</td></tr>
<tr><td colspan="2">
	<?
	foreach($arCountryList as $country)
		if($country['EXISTS'] && $country['CHECKED']){
		?>
			<div class='IPOLSDEK_countryButton'>
				<input id='IPOLSDEK_cB_<?=$country['CODE']?>' onclick="IPOLSDEK_setups.cities.switchCountry('<?=$country['CODE']?>')" value="<?=GetMessage('IPOLSDEK_SYNCTY_'.$country['CODE'])?>" type="button">
			</div>
		<?}?>
	<div style='clear:both'></div>
</td></tr>

<tr><td colspan="2" id='IPOLSDEK_countryContent'></td></tr>
<?sdekOption::placeHint('noteOrderDateCC');?>