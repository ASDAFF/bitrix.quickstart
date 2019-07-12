<?
    if (!defined("ADMIN_SECTION") || ADMIN_SECTION !== true ) return;
	
	$arParams['PRESET']['TIMEOUT_LIMIT'] = COption::GetOptionString("cacheMaker", "setTimout");
	if((int)$arParams['PRESET']['TIMEOUT_LIMIT'] == 0)
		$arParams['PRESET']['TIMEOUT_LIMIT'] = 5;
	$arParams['PRESET']['IBLOCK_CODE_PRODUCTS'] = COption::GetOptionString("cacheMaker", "setProductsCode");
	if(empty($arParams['PRESET']['IBLOCK_CODE_PRODUCTS']))
		$arParams['PRESET']['IBLOCK_CODE_PRODUCTS'] = "products";
	$arParams['PRESET']['IBLOCK_CODE_COMPLECT'] = COption::GetOptionString("cacheMaker", "setComplectCode");
	if(empty($arParams['PRESET']['IBLOCK_CODE_COMPLECT']))
		$arParams['PRESET']['IBLOCK_CODE_COMPLECT'] = "products_offers";
	$arParams['PRESET']['RELPATH_PRODUCTS'] = COption::GetOptionString("cacheMaker", "srcProductsList");
	if(empty($arParams['PRESET']['RELPATH_PRODUCTS']))
		$arParams['PRESET']['RELPATH_PRODUCTS'] = "/catalog/";
	
	$arParams['PRESET']['RELPATH_COMPLECT'] = COption::GetOptionString("cacheMaker", "srcComplectList");
	if(empty($arParams['PRESET']['RELPATH_COMPLECT']))
		$arParams['PRESET']['RELPATH_COMPLECT'] = "/complects/";
	
	
    $aTabs = array(
        array(
			"DIV"	=> "edit0",
			"TAB"	=> GetMessage('TAB_1_NAME'),
			"ICON"	=> "main_user_edit",
			"TITLE"	=> GetMessage('TAB_1_NAME')
		),
		array(
			"DIV"	=> "edit1",
			"TAB"	=> GetMessage('TAB_2_NAME'),
			"ICON"	=> "main_user_edit",
			"TITLE"	=> GetMessage('TAB_2_NAME')
		),
		array(
			"DIV"	=> "edit2",
			"TAB"	=> GetMessage('TAB_3_NAME'),
			"ICON"	=> "main_user_edit",
			"TITLE"	=> GetMessage('TAB_3_NAME')
		)
    );
	
	CModule::IncludeModule( "iblock" );
	
	$rsIBlock = CIBlock::GetList(
		array(),
		array(
			'ACTIVE'	=> "Y"
		),
		false
	);
	$arParams['IBLOCK'] = array();
	while($arIBlock = $rsIBlock -> Fetch())
		$arParams['IBLOCK'][] = $arIBlock;
?>
	<div id="adminNote" style="display:none;"><?=CAdminMessage::ShowNote(" ");?></div>
	<div id="adminMess" style="display:none;"><?=CAdminMessage::ShowMessage(" ");?></div>
<?	
    $tabControl = new CAdminTabControl("tabControl", $aTabs, true, true);
?>

<?=BeginNote();?>
<pre>
<?=GetMessage('RESPONSE_DEFAULT');?>: <span id="responses"></span>
<?=GetMessage('COMPLETE');?>: <span id="progress">0</span>%
<?=GetMessage('TIME_ELAPSED');?>: <span id="timer"></span>
<?=GetMessage('TIME_REMAINING');?>: <span id="time"></span>
</pre>
<?=EndNote();?>

<iframe id="iframe" src="#" width="0" height="0" frameborder="0"></iframe>

<form method="POST" action="<?echo $APPLICATION->GetCurPage()?>?lang=<?echo htmlspecialcharsbx(LANG)?>" name="fs1">
<?
	$tabControl->Begin();
	$tabControl->BeginNextTab();
?>
	<tr>
	    <td width="300"><?=GetMessage('TIMEOUT_LIMIT');?>:</td>
		<td>
			<div><input type="text" id="setTimout" name="setTimout" class="cacheMakerItem" value="<?=$arParams['PRESET']['TIMEOUT_LIMIT'];?>" /></div>
		</td>
	</tr>
	<tr>
		<td colspan="2"><?=BeginNote();?>
			<?=GetMessage('TIMEOUT_NOTE');?>
<?=EndNote();?></td>
	</tr>
	
	<tr>
	    <td><?=GetMessage('IBLOCK_CODE_PRODUCTS');?>:</td>
		<td>
			<div>
				<select name="setProductsCode" class="cacheMakerItem">
<?
		foreach($arParams['IBLOCK'] as $val)
		{
?>
					<option<? if($arParams['PRESET']['IBLOCK_CODE_PRODUCTS'] == $val['CODE'])echo' selected';?> value="<?=$val['CODE'];?>"><?=$val['NAME'];?> [<?=$val['CODE'];?>]</option>
<?
		}
?>
				</select>
			</div>
		</td>
    </tr>
	<tr>
	    <td><?=GetMessage('IBLOCK_CODE_COMPLECT');?>:</td>
		<td>
			<div>
				<select name="setComplectCode" class="cacheMakerItem">
<?
		foreach($arParams['IBLOCK'] as $val)
		{
?>
					<option<? if($arParams['PRESET']['IBLOCK_CODE_COMPLECT'] == $val['CODE'])echo' selected';?> value="<?=$val['CODE'];?>"><?=$val['NAME'];?> [<?=$val['CODE'];?>]</option>
<?
		}
?>
				</select>
			</div>
		</td>
    </tr>
	
	<tr>
	    <td><?=GetMessage('RELPATH_PRODUCTS');?>:</td>
		<td><div><input type="text" name="srcProductsList" class="cacheMakerItem" value="<?=$arParams['PRESET']['RELPATH_PRODUCTS'];?>" /></div></td>
    </tr>
	<tr>
	    <td><?=GetMessage('RELPATH_COMPLECT');?>:</td>
		<td><div><input type="text" name="srcComplectList" class="cacheMakerItem" value="<?=$arParams['PRESET']['RELPATH_COMPLECT'];?>" /></div></td>
    </tr>
<?
	$tabControl->BeginNextTab();
?>
	<tr>
	    <td width="300"><?=GetMessage('WARM_UP_PRODUCTS_LIST');?></td>
		<td><div><input type="checkbox" name="setProductsList" class="cacheMakerItem" value="Y" /></div></td>
    </tr>

	<tr>
	    <td><?=GetMessage('WARM_UP_COMPLECT_LIST');?></td>
		<td><div><input type="checkbox" name="setComplectList" class="cacheMakerItem" value="Y" /></div></td>
    </tr>
<?
	 $tabControl->BeginNextTab();   
?>
	<tr>
	    <td width="300"><?=GetMessage('WARM_UP_PRODUCTS_DETAIL');?></td>
		<td><div><input type="checkbox" name="setProductsDetail" class="cacheMakerItem" value="Y" /></div></td>
    </tr>
	<tr>
	    <td width="300"><?=GetMessage('WARM_UP_COMPLECT_DETAIL');?></td>
		<td><div><input type="checkbox" name="setComplectDetail" class="cacheMakerItem" value="Y" /></div></td>
    </tr>
	<tr>
	    <td><?=GetMessage('ID_RANGE_PRODUCTS');?></td>
		<td>
            <div><pre><?=GetMessage('ID_RANGE_PRODUCTS_FROM');?> <input name="idProductsFrom" class="cacheMakerItem" type="text" /></pre></div>
			<div><pre><?=GetMessage('ID_RANGE_PRODUCTS_TO');?> <input name="idProductsTo" class="cacheMakerItem" type="text" /></pre></div>
        </td>
    </tr>
	<tr>
	    <td><?=GetMessage('ID_RANGE_COMPLECT');?></td>
		<td>
            <div><pre><?=GetMessage('ID_RANGE_COMPLECT_FROM');?> <input name="idComplectFrom" class="cacheMakerItem" type="text" /></pre></div>
			<div><pre><?=GetMessage('ID_RANGE_COMPLECT_TO');?> <input name="idComplectTo" class="cacheMakerItem" type="text" /></pre></div>
        </td>
    </tr>
<?
	$tabControl->Buttons();
?>
    <input id="btnStart" class="cacheMakerItem" type="button" value="<?=GetMessage('START');?>" />
<?
	$tabControl->End();
?>
</form>

<?
	$APPLICATION->AddHeadScript("/bitrix/js/main/jquery/jquery-1.8.3.min.js");
/*
<script type="text/javascript" src="/bitrix/js/main/jquery/jquery-1.8.3.min.js"></script>
*/
?>
<script>
	var h = 0;
	var m = 0;
	var s = 0;
	var arParams = {};
	
	function timer()
	{
		var ss = "00";
		var mm = "00";
		var hh = "00";
		
		s = s - 0 + 1;
		if (s > 59) { s = 0; m = m - 0 + 1;}
		if (m > 59) { m = 0; h = h - 0 + 1; }
		
		if (s < 10) ss = "0" + s; else ss = s;
		if (m < 10) mm = "0" + m; else mm = m;
		if (h < 10) hh = "0" + h; else hh = h;
		
		document.getElementById("timer").innerHTML = ''+ hh + ":" + mm + ":" + ss +'</span>';
		if ( $('#timer').attr('data-run') == 1 ) setTimeout(timer, 1000);
	}

	
	$(document).ready(function(e) {
		$('#btnStart').on('click', function(){
			
			$('#adminMess').hide();
			$('#adminNote').hide();
			
			h = 0; m = 0; s = 0;
			
			$('#timer').attr('data-run', 1);
			
			$('.cacheMakerItem').each(function(index, element) {
				if(
					(
						($(element).attr('type') == "checkbox")
						||
						($(element).attr('type') == "radio")
					)&&( $(element).prop('checked') )
					
				)
					arParams[ $(element).attr('name') ] = $(element).attr('value');
				
				if( $(element).attr('type') == "text" )
					arParams[ $(element).attr('name') ] = $(element).attr('value');
			});
			
			$('.cacheMakerItem option:selected').each(function(index, element) {
				arParams[ $(element).parent().attr('name') ] = $(element).attr('value');
			});
			
			$('.cacheMakerItem').attr('disabled', true);
			
			timer();
			
			progress();
			
			return false;
		});
		
		function progress(){
			$.ajax({
				dataType	: "json",
				type		: "POST",
				url			: "/local/php_interface/novagroup/admin/novagr.shop/cacheMaker.ajax.php",
				data		: {
					arParams	: arParams
				},
				success			: function(data) {
					//$('#progress').css('width', data.progress + "%");
					if(data.error != "")
					{
						$('#adminMess').find('.adm-info-message-title').html(data.error);
						$('#adminNote').hide();
						$('#adminMess').show();
						$('.cacheMakerItem').attr('disabled', false);
						$('#timer').attr('data-run', 0);
					}else{
						if(data.progress >= 100)
						{
							$('#iframe').attr('src', data.URL);
							setTimeout(
								function(){
									$('#adminNote').find('.adm-info-message-title').html("Кеш успешно прогрет!");
									$('#responses').html(data.message);
									$('#progress').html(data.progress);
									$('#adminMess').hide();
									$('#adminNote').show();
									$('.cacheMakerItem').attr('disabled', false);
									$('#timer').attr('data-run', 0);

								},
								$('#setTimout').attr('value')*1000
							);
						}else{
							$('#responses').html(data.message);
							$('#progress').html(data.progress);
							$('#time').html(data.time);
							$('#iframe').attr('src', data.URL);
							setTimeout(progress, $('#setTimout').attr('value')*1000);
							//progress();
						}
					}
				}
			});
		}
		//$('#cacheUpdForm').ajaxForm(options);
	});
</script>