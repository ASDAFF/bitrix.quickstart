<?
#################################################
#        Company developer: IPOL
#        Developer: Egorov Nikita
#        Site: http://www.ipolh.com
#        E-mail: om-sv2@mail.ru
#        Copyright (c) 2006-2012 IPOL
#################################################
?>
<?
CJSCore::Init(array("jquery"));
IncludeModuleLangFile(__FILE__);
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/options.php");

$module_id = "ipol.mailorder";
CModule::IncludeModule($module_id);
CModule::IncludeModule('sale');

$strWarning = "";

$arAllOptions = array(
	"main" => Array(
		Array("IPOLMO_OPT_WORKMODE",  GetMessage("IPOLMO_OPT_WORKMODE"),  "1", Array("text")),
		Array("IPOLMO_OPT_TEXTMODE",  GetMessage("IPOLMO_OPT_TEXTMODE"),  "1", Array("text")),
		Array("IPOLMO_OPT_PROPS",     GetMessage("IPOLMO_OPT_PROPS"),     "",  Array("text")),
		Array("IPOLMO_OPT_EVENTS",    GetMessage("IPOLMO_OPT_EVENTS"),    "",  Array("text")),
		Array("IPOLMO_OPT_ADDEVENTS", GetMessage("IPOLMO_OPT_ADDEVENTS"), "",  Array("text")),
	),
	"additional" => array(
		Array("IPOLMO_OPT_LOCATIONSEPARATOR",  GetMessage("IPOLMO_OPT_LOCATIONSEPARATOR"),  ", ", Array("text")),
	),
	"special" => array(
		Array("IPOLMO_OPT_LOCATIONDETAILS",  GetMessage("IPOLMO_OPT_LOCATIONDETAILS"),  "", Array("text")),
	)
);
$aTabs = array(
	array("DIV" => "edit1", "TAB" => GetMessage("IPOLMO_FAQ"), "TITLE" => GetMessage("IPOLMO_FAQ_TITLE")),
	array("DIV" => "edit2", "TAB" => GetMessage("MAIN_TAB_SET"), "TITLE" => GetMessage("MAIN_TAB_TITLE_SET")),
	array("DIV" => "edit3", "TAB" => GetMessage("IPOLMO_ADDSETUPS"), "TITLE" => GetMessage("IPOLMO_ADDSETUPS_TITLE")),
);

if ($USER->IsAdmin() && $_SERVER["REQUEST_METHOD"]=="GET" && strlen($RestoreDefaults)>0 && check_bitrix_sessid())
    COption::RemoveOption($module_id);

$tabControl = new CAdminTabControl("tabControl", $aTabs);

function ShowParamsHTMLByArray($arParams)
{
	global $module_id;
	foreach($arParams as $Option)
	{
		switch($Option[0])
		{	
			case 'IPOLMO_OPT_WORKMODE': //режим работы
				$checked=array('','','');
				$checked[COption::GetOptionString($module_id,'IPOLMO_OPT_WORKMODE','1')]='checked';
				echo '<tr>
						<td width="50%" class="adm-detail-content-cell-l">'.$Option[1].' <a href="javascript:void(0)" class="PropHint" onclick="return module_popup_virt(\'IPOLMO_EXP_WORKMODE\', this);"></a> :</td>
						<td width="50%" class="adm-detail-content-cell-r">
							<input type="radio" value="1" name="IPOLMO_OPT_WORKMODE" id="IPOLMO_OPT_WORKMODE_1" '.$checked[1].'><label for="IPOLMO_OPT_WORKMODE_1">'.GetMessage("IPOLMO_OPT_WORKMODE_1").'</label><br>
							<input type="radio" value="2" name="IPOLMO_OPT_WORKMODE" id="IPOLMO_OPT_WORKMODE_2" '.$checked[2].'><label for="IPOLMO_OPT_WORKMODE_2">'.GetMessage("IPOLMO_OPT_WORKMODE_2").'</label>
						</td>
					</tr>';
					break;			
			case 'IPOLMO_OPT_TEXTMODE': //text/html
				$checked=array('','','');
				$checked[COption::GetOptionString($module_id,'IPOLMO_OPT_TEXTMODE','1')]='checked';
				echo '<tr>
						<td width="50%" class="adm-detail-content-cell-l">'.$Option[1].'</td>
						<td width="50%" class="adm-detail-content-cell-r">
							<input type="radio" value="1" name="IPOLMO_OPT_TEXTMODE" id="IPOLMO_OPT_TEXTMODE_1" '.$checked[1].'><label for="IPOLMO_OPT_TEXTMODE_1">'.GetMessage("IPOLMO_OPT_TEXTMODE_1").'</label><br>
							<input type="radio" value="2" name="IPOLMO_OPT_TEXTMODE" id="IPOLMO_OPT_TEXTMODE_2" '.$checked[2].'><label for="IPOLMO_OPT_TEXTMODE_2">HTML</label>
						</td>
					</tr>';
					break;		
			case 'IPOLMO_OPT_PROPS': //таблица со свойствами
				$savedPropsTmp=explode('|',COption::GetOptionString($module_id,'IPOLMO_OPT_PROPS',''));
				$savedProps=array();
				foreach($savedPropsTmp as $propGroup)
					if($propGroup)
						$savedProps[substr($propGroup,0,strpos($propGroup,'{'))]=','.substr($propGroup,strpos($propGroup,'{')+1,strpos($propGroup,'}')-strpos($propGroup,'{')-1);
				$orderProps=array();
				$arSpecNames=array('PERSON_TYPES'=>array(),'PROPS_GROUPS'=>array());
				$allProps=CSaleOrderProps::GetList();
				while($prop=$allProps->Fetch())
				{
					if(!array_key_exists($prop['PERSON_TYPE_ID'],$arSpecNames['PERSON_TYPES']))
						$arSpecNames['PERSON_TYPES'][$prop['PERSON_TYPE_ID']]=CSalePersonType::GetByID($prop['PERSON_TYPE_ID']);
					if(!array_key_exists($prop['PROPS_GROUP_ID'],$arSpecNames['PROPS_GROUPS']))
						$arSpecNames['PROPS_GROUPS'][$prop['PROPS_GROUP_ID']]=CSaleOrderPropsGroup::GetByID($prop['PROPS_GROUP_ID']);
					$orderProps[$prop['PERSON_TYPE_ID']][$prop['PROPS_GROUP_ID']][$prop['ID']]=array('NAME'=>$prop['NAME'],'CODE'=>$prop['CODE']);
				}
				$tableHead="<tr><td style='text-align:center;'>".GetMessage('IPOLMO_OPT_PROPS_TABLE_NAME')."</td><td style='text-align:center;'>".GetMessage('IPOLMO_OPT_PROPS_TABLE_CODE')."</td></tr>";
				echo '<tr class="heading">
						<td colspan="2" valign="top" align="center">'.GetMessage('IPOLMO_OPT_PROPS').' <a href="javascript:void(0)" class="PropHint" onclick="return module_popup_virt(\'IPOLMO_EXP_MARKPROPS\', this);"></a></td>
					</tr>
					';
				//доставка
				echo '<tr class="propsPayer" id="IPOLMO_payer_0" onclick="IPOLMO_payerClick(0)">
						<td colspan="2" valign="top" align="center" >'.GetMessage('IPOLMO_OPT_PROPS_COMMON').'</td>
					</tr>
					<tr><td colspan="2"><table style="width:100%" id="payer_0">'.$tableHead;
					
				foreach(array(array('CODE'=>'IMOPRICE','NAME'=>GetMessage('IPOLMO_OPT_PROPS_IMOPRICE')),array('CODE'=>'IMODELIVERY','NAME'=>GetMessage('IPOLMO_OPT_PROPS_DELIVERY')),array('CODE'=>'IMODELIVERYPRICE','NAME'=>GetMessage('IPOLMO_OPT_PROPS_DELIVERYPRC')),array('CODE' => 'IMOTRACKING','NAME'=>GetMessage('IPOLMO_OPT_PROPS_TRACKING')),array('CODE'=>'IMOPAYSYSTEM','NAME'=>GetMessage('IPOLMO_OPT_PROPS_PAYSYSTEM')),array('CODE'=>'IMOPAYED','NAME'=>GetMessage('IPOLMO_OPT_PROPS_IMOPAYED')),array('CODE'=>'IMOCOMMENT','NAME'=>GetMessage('IPOLMO_OPT_PROPS_IMOCOMMENT'))) as $prop)
				{
					$marked='';
					if(strpos($savedProps[0],','.$prop['CODE'].' (')!==false) $marked='chosenTr';
					echo '<tr class="propsTable '.$marked.'" onclick="IPOLMO_trClick($(this))"><td>'.$prop['NAME'].'</td><td class="codeIsHere">'.$prop['CODE'].' ( #IPOLMO_'.$prop['CODE'].'# )</td></tr>';
				}
				echo '</table></td></tr><tr><td colspan="2">&nbsp;</td></tr>';
				foreach($orderProps as $payerId => $payerProps)
				{
					echo '<tr class="propsPayer" id="IPOLMO_payer_'.$payerId.'" onclick="IPOLMO_payerClick('.$payerId.')">
						<td colspan="2" valign="top" align="center" >'.$arSpecNames['PERSON_TYPES'][$payerId]['NAME'].'</td>
					</tr>';
					echo '<tr><td colspan="2"><table style="width:100%" id="payer_'.$payerId.'">';
						foreach($payerProps as $groupId => $gropupProps)
						{
							echo '<tr class="propsGroup" onclick="IPOLMO_groupClick('.$groupId.','.$payerId.')">
								<td colspan="2" valign="top" align="center">'.$arSpecNames['PROPS_GROUPS'][$groupId]['NAME'].'</td>
							</tr><tr><td colspan="2"><table style="width:100%" id="group_'.$groupId.'">'.$tableHead;
							foreach($gropupProps as $propId => $prop)
							{
								$marked='';
								if(strpos($savedProps[$payerId],','.$prop['CODE'].' (')!==false) $marked='chosenTr';
								echo '<tr class="propsTable '.$marked.'" onclick="IPOLMO_trClick($(this))"><td>'.$prop['NAME'].'</td><td class="codeIsHere">'.$prop['CODE'].' ( #IPOLMO_'.$prop['CODE'].'# )</td></tr>';
							}
							echo '</table></td></tr><tr><td colspan="2">&nbsp;</td></tr>';
						}
					echo '</table></td></tr><tr><td colspan="2">&nbsp;</td></tr>';
				}
				echo "<input type='hidden' name='IPOLMO_OPT_PROPS' value='".COption::GetOptionString($module_id,'IPOLMO_OPT_PROPS','')."'>";
				break;
			case "IPOLMO_OPT_EVENTS":
				echo "<tr class='heading'><td colspan='2' valign='top' align='center'>".GetMessage('IPOLMO_OPT_EVENTS')."</td></tr>";
				$arEvents = array(
					"SALE_NEW_ORDER",
					"SALE_NEW_ORDER_RECURRING",
					"SALE_ORDER_CANCEL",
					"SALE_ORDER_DELIVERY",
					"SALE_ORDER_PAID",
					"SALE_ORDER_REMIND_PAYMENT",
					"SALE_RECURRING_CANCEL",
					"SALE_STATUS_CHANGED",
					"SALE_SUBSCRIBE_PRODUCT"
				);
				$checkedAr = explode(',',COption::GetOptionString($module_id,"IPOLMO_OPT_EVENTS","SALE_NEW_ORDER"));
				foreach($arEvents as $event){
					$checked='';
					if(in_array($event,$checkedAr))
						$checked='checked';
					if($event == 'SALE_STATUS_CHANGED')
						$link = "<a target='_blank' href='/bitrix/admin/type_admin.php?PAGEN_1=1&SIZEN_1=75&lang=ru&set_filter=Y&find=SALE_STATUS_CHANGED&find_type=event_name&by=event_name&order=asc'>".GetMessage("IPOLMO_OPT_EVENTS_TEMPLATE")."</a>";
					else
						$link = "<a target='_blank' href='/bitrix/admin/type_edit.php?EVENT_NAME=".$event."'>".GetMessage("IPOLMO_OPT_EVENTS_TEMPLATE")."</a>";

					echo "<tr><td><label for='IPOLMO_CHECK_$event'>".GetMessage("IPOLMO_OPT_EVENTS_".$event)."</td><td><input id='IPOLMO_CHECK_$event' type='checkbox' $checked name='IPOLMO_OPT_EVENTS[]' value='$event'>&nbsp;".$link."</td></tr>";
				}
				break;
			case "IPOLMO_OPT_ADDEVENTS":
				echo "<tr class='heading'><td colspan='2' valign='top' align='center'>".GetMessage('IPOLMO_OPT_ADDEVENTS')."</td></tr>";
				$svd = unserialize(COption::GetOptionString($module_id,"IPOLMO_OPT_ADDEVENTS","a:{}"));
				foreach($svd as $rifle)
					echo "<tr><td colspan='2' style='text-align:center;'><input type='text' name='IPOLMO_OPT_ADDEVENTS[]' value='".$rifle."' size='50'></td></tr>";
				echo "<tr><td colspan='2' style='text-align:center;'><input type='text' name='IPOLMO_OPT_ADDEVENTS[]' value='' size='50'></td></tr>";
				echo "<tr><td colspan='2' style='text-align:center;padding-top:5px;'><input type='button' value='+' size='50' onclick='IPOLMO_addRowAE()'></td></tr>";
			break;
			default: __AdmSettingsDrawRow($module_id, $Option);break;
		}
			
	}
}

//Save options
if($REQUEST_METHOD=="POST" && strlen($Update.$Apply.$RestoreDefaults)>0 && check_bitrix_sessid()){
	if(strlen($RestoreDefaults)>0)
		COption::RemoveOption($module_id);
	else
	{
		if(array_key_exists('IPOLMO_OPT_LOCATIONDETAILS',$_REQUEST))
			$_REQUEST['IPOLMO_OPT_LOCATIONDETAILS'] = serialize($_REQUEST['IPOLMO_OPT_LOCATIONDETAILS']);
		foreach($_REQUEST['IPOLMO_OPT_ADDEVENTS'] as $key => $val)
			if(!$val)
				unset($_REQUEST['IPOLMO_OPT_ADDEVENTS'][$key]);
		$_REQUEST['IPOLMO_OPT_ADDEVENTS'] = serialize($_REQUEST['IPOLMO_OPT_ADDEVENTS']);
		if(!array_key_exists('IPOLMO_OPT_EVENTS',$_REQUEST))
			$_REQUEST['IPOLMO_OPT_EVENTS'] = '';
		else
			$_REQUEST['IPOLMO_OPT_EVENTS'] = implode(',',$_REQUEST['IPOLMO_OPT_EVENTS']);
		foreach($arAllOptions as $aOptGroup)
			foreach($aOptGroup as $option){
				switch($option[0])
				{
					case 'IPOLMO_OPT_WORKMODE': 
						if($_POST['IPOLMO_OPT_WORKMODE']) COption::SetOptionString($module_id,'IPOLMO_OPT_WORKMODE',$_POST['IPOLMO_OPT_WORKMODE']);break;
					default: __AdmSettingsSaveOption($module_id, $option);break;
				}
			}
	}
	if($_REQUEST["back_url_settings"] <> "" && $_REQUEST["Apply"] == "")
		   echo '<script type="text/javascript">window.location="'.CUtil::addslashes($_REQUEST["back_url_settings"]).'";</script>';		
}

if(!preg_match('/\{([^\}]+)\}/',COption::GetOptionString($module_id,"IPOLMO_OPT_PROPS")))
	$exceptText = "<br>".GetMessage('IPOLMO_FNDD_ERR_NOPROPS');
if(strlen(COption::GetOptionString($module_id,"IPOLMO_OPT_EVENTS","SALE_NEW_ORDER"))<1)
	$exceptText .= "<br>".GetMessage('IPOLMO_FNDD_ERR_NOEVENT');

if($exceptText){?>
		<div class="adm-info-message-wrap adm-info-message-red">
		  <div class="adm-info-message">
			<div class="adm-info-message-title"><?=GetMessage('IPOLIML_FNDD_ERR_HEADER')?></div>
				<?=$exceptText?>
				<br><br><strong><?=GetMessage('IPOL_FNDD_ERR_FOOTER')?></strong>
			<div class="adm-info-message-icon"></div>
		  </div>
		</div>
	<?}
?>

<style>
#moduleTable td {padding: 5px 10px; border-bottom: 1px solid #aaa;}
#moduleTable {border-collapse: collapse; max-width: 1000px;}
#moduleTable thead td {background-color: #E2E6D4; color: #555; }
#moduleTable tbody tr:hover {background-color: #FEFEFE !important; }
#moduleTable tbody td {background-color: transparent; !important }

.PropHint { 
	background: url("/bitrix/js/main/core/images/hint.gif") no-repeat transparent;
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
	z-index: 12;
}
.b-popup .pop-text { 
	margin-bottom: 10px;
	color:#000;
}
.pop-text i {color:#AC12B1;}
.b-popup .close { 
	background: url("/bitrix/images/<?=$module_id?>/popup_close.gif") no-repeat transparent;
	cursor: pointer;
	height: 10px;
	position: absolute;
	right: 4px;
	top: 4px;
	width: 10px;
}

.moduleHeader {
	font-size: 16px;
	cursor: pointer;
	display:block;
	color:#2E569C;
}

.moduleInst {
display:none; margin-left:10px;margin-top:10px;
}

.moduleInst p { font-size: 100%; }

.propsPayer td{
	font-size: 14px;
	font-weight: 700;
	text-align: center;
	padding: 5px;
	color: #9F5959;
	cursor: pointer;
}
.propsGroup td{
	border-bottom: 1px dashed black;
	text-align: center;
	padding: 5px;
	color: #9F5959;
	cursor: pointer;
}
.propsTable td{
	border: 1px dashed black;
	text-align: center;
	padding: 5px;
	color: #9F5959;
	cursor: pointer;
}			
.propsTable:hover,.propsPayer:hover,.propsGroup:hover{background-color: #D4D4E3;}
.chosenTr{background-color: #D1FAD1;}
.IPOLMO_detailTable{
	margin:auto !important;
	border-collapse:collapse;
}
.IPOLMO_detailTable td,.IPOLMO_detailTable th{
	padding: 2px 8px;
}
[name="IPOLMO_OPT_LOCATIONSEPARATOR"]{
	width: 10px;
}
</style>
<script>
function module_popup_virt(code, info){ // Вспл. подсказки 
	var offset = $(info).position().top;
	var LEFT = $(info).offset().left;		
	
	var obj;
	if(code == 'next') 	obj = $(info).next();
	else  				obj = $('#'+code);
	
	LEFT -= parseInt( parseInt(obj.css('width'))/2 );
	
	obj.css({
		top: (offset+15)+'px',
		left: LEFT,
		display: 'block'
	});	
	return false;
}

function IPOLMO_trClick(wat,mode)
{
	if(wat.hasClass('chosenTr') && typeof mode === 'undefined')
		wat.removeClass('chosenTr');
	else
		wat.addClass('chosenTr');
		
	IPOLMO_checkTr();
}

function IPOLMO_checkTr()
{
	var strToWrite='';
	$('[id^="IPOLMO_payer_"]').each(function(){
		var selfId=$(this).attr('id').substr(13);
		strToWrite+=selfId+'{';
		$('#payer_'+selfId).find('.chosenTr').each(function(){
			strToWrite+=$(this).children('.codeIsHere').html()+',';
		});
		strToWrite+="}|";
	});
	$('[name="IPOLMO_OPT_PROPS"]').val(strToWrite);
	$('#test').val(strToWrite);
}

function IPOLMO_payerClick(wat)
{
	$('#payer_'+wat).find('.propsTable').each(function(){IPOLMO_trClick($(this),1)});
}

function IPOLMO_groupClick(wat,where)
{
	$('#payer_'+where+' #group_'+wat).find('.propsTable').each(function(){IPOLMO_trClick($(this),1)});
}

function IPOLMO_addRowAE(){
	$('[name="IPOLMO_OPT_ADDEVENTS[]"]:last').closest('tr').after("<tr><td colspan='2' style='text-align:center;'><input type='text' name='IPOLMO_OPT_ADDEVENTS[]' value='' size='50'></td></tr>");
}
</script>

<?foreach(array('IPOLMO_EXP_MARKPROPS','IPOLMO_EXP_WORKMODE','IPOLMO_EXP_LOCATIONDETAILS') as $popup){?>
	<div id="<?=$popup?>" class="b-popup" style="display: none; ">
		<div class="pop-text"><?=GetMessage($popup)?></div>
		<div class="close" onclick="$(this).closest('.b-popup').hide();"></div>
	</div>
<?}?>


<form method="post" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=htmlspecialchars($mid)?>&amp;lang=<?echo LANG?>">
<?
$tabControl->Begin();
$tabControl->BeginNextTab();
?>
<tr><td style="color:#555; " colspan="2" >
	<a class="moduleHeader" onclick="$(this).next().toggle(); return false;"><?=GetMessage('IPOLMO_FAQ_WAT_TITLE')?></a>
	<div class="moduleInst" ><?=GetMessage('IPOLMO_FAQ_WAT_DESCR')?></div>					
</td></tr>
<tr><td style="color:#555; " colspan="2" >
	<a class="moduleHeader" onclick="$(this).next().toggle(); return false;"><?=GetMessage('IPOLMO_FAQ_WORK_TITLE')?></a>
	<div class="moduleInst" ><?=GetMessage('IPOLMO_FAQ_WORK_DESCR')?></div>					
</td></tr>
<?
$tabControl->BeginNextTab();

ShowParamsHTMLByArray($arAllOptions["main"]);
$tabControl->BeginNextTab();
?>
<tr class="heading">
	<td colspan="2" valign="top" align="center"><?=GetMessage('IPOLMO_OPT_LOCATIONDETAILS')?> <a href="javascript:void(0)" class="PropHint" onclick="return module_popup_virt('IPOLMO_EXP_LOCATIONDETAILS', this);"></a></td>
</tr>
<tr><td style="color:#555; " colspan="2" >
	<a class="moduleHeader" onclick="$(this).next().toggle(); return false;"><?=GetMessage('IPOLMO_FAQ_LOCATIONDETAILS_TITLE')?></a>
	<div class="moduleInst" ><?=GetMessage('IPOLMO_FAQ_LOCATIONDETAILS_DESCR')?></div>					
</td></tr>
<?
	$opt = unserialize(COption::GetOptionString($module_id,'IPOLMO_OPT_LOCATIONDETAILS',mailorderdriver::getDefLocationTypes()));
	$gotted = mailorderdriver::getLocationTypes();
	if(is_array($gotted)){?>
		<tr><td colspan='2'><table class='IPOLMO_detailTable'><tr><th><?=GetMessage('IPOLMO_LBL_TYPENAME')?></th><th><?=GetMessage('IPOLMO_LBL_DOSHOW')?></th></tr>
		<?foreach($gotted as $id => $name){?>
			<tr><td><?=$name?></td><td style='text-align:center;'><input type='checkbox' name='IPOLMO_OPT_LOCATIONDETAILS[]' value='<?=$id?>' <?=(in_array($id,$opt)) ? 'checked' : ''?>></td></tr>
		<?}?>
		</table></td></tr>
	<?}
	ShowParamsHTMLByArray($arAllOptions["additional"]);
?>
<?
$tabControl->Buttons();
?>
<script language="JavaScript">
function RestoreDefaults()
{
	if(confirm('<?echo AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING"))?>'))
		window.location = "<?echo $APPLICATION->GetCurPage()?>?RestoreDefaults=Y&lang=<?echo LANG?>&mid=<?echo urlencode($mid)?>&<?=bitrix_sessid_get()?>";
}
</script>
<div align="left">
	<input type="hidden" name="Update" value="Y">
	<input type="submit" <?if(!$USER->IsAdmin())echo " disabled ";?> name="Update" value="<?echo GetMessage("MAIN_SAVE")?>">
</div>
<?$tabControl->End();?>
<?=bitrix_sessid_post();?>
</form>