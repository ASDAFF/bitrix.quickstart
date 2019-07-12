<?
Header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
Header("Cache-Control: no-cache, must-revalidate");
Header("Pragma: no-cache"); 
Header("Last-Modified: ".gmdate("D, d M Y H:i:s")."GMT"); 

require($DOCUMENT_ROOT."/bitrix/modules/main/include/prolog_before.php");
require($_SERVER['DOCUMENT_ROOT'].'#SITE_DIR#admin-config/config.php');

CModule::AddAutoloadClasses( 
	'',
	array(
		'CSofteffect' => '#SITE_DIR#admin-config/functions.php', 
	) 
);

CModule::IncludeModule('iblock');
CAjax::Init();

$err='';
global $IB_CATALOG; // переменная задается в bitrix/phpintarface/php.ini
global $IB_BACKCALL;

$IBLOCK_ID = $IB_CATALOG;
$ELEMENT = htmlspecialchars(trim($_REQUEST['ELEMENT']), ENT_QUOTES);

$elID = CSofteffect::CODEvsID($ELEMENT, $IB_CATALOG, TRUE);
$send=false;

$NAME_POLE = htmlspecialchars(trim($_REQUEST['NAME_POLE']), ENT_QUOTES);
$PHONE_POLE = htmlspecialchars(trim($_REQUEST['PHONE_POLE']), ENT_QUOTES);
$NAME_POLE = htmlspecialchars(trim($_REQUEST['NAME_POLE']), ENT_QUOTES);
$TEXT_POLE = htmlspecialchars(trim($_REQUEST['TEXT_POLE']), ENT_QUOTES);

if ($_REQUEST['go']=='1') {
	if ($PHONE_POLE=='') {
		$err .= '<b>Вы забыли указать Телефон </b>';
		$send=FALSE;
	} 
	if ($NAME_POLE=='') {
		$err .= '<b>Вы забыли указать Имя </b>';
		$send=FALSE;
	} 

	if (strlen($err)<=0) {
		$el = new CIBlockElement;

		$PROP = array();
		$PROP['ELEMENT'] = $ELEMENT;
		$PROP['PHONE'] = $PHONE_POLE;
		
		$date = date('d.m.Y H:i:s');

		$arLoadProductArray = Array(
		  	"MODIFIED_BY"    	=> $USER->GetID(),
		  	"IBLOCK_SECTION_ID" => false,
		  	"IBLOCK_ID"      	=> $IB_BACKCALL,
		  	"PROPERTY_VALUES"	=> $PROP,
		  	"NAME"           	=> date('Y-m-d').' ['.$NAME_POLE.']',
		  	"ACTIVE"         	=> "Y",
			"PREVIEW_TEXT"		=> $TEXT_POLE,
			"DATE_ACTIVE_FROM"	=> $date
		);

		if($PRODUCT_ID = $el->Add($arLoadProductArray)) {
			$arEventFields = array(
    			"ID" => $PRODUCT_ID,
				"DATE" => $date,
				"NAME" => $NAME_POLE,
				"PHONE" => $PHONE_POLE,
				"TEXT" => $TEXT_POLE
    		);
			
			CEvent::Send("NEW_PHONE", SITE_ID, $arEventFields, 'Y', '#template_callme#');
			$send=true;
		} else {
			$send=false;
			$err = 'При отправке сообщения произошла ошибка. Повторите попытку позднее.';
		}
	}
} 
?>
<? if (!$send) { ?>
	<input type="hidden" name="ELEMENT" value="<?=$elID?>" />
	<input type="hidden" name="go" value="1" />
	<? if (strlen($err)>0) { ?>
		<span style="color: #94002a;"><?=$err?></span><br /><br />
	<? } ?>
	<b>Ваше имя</b> <br />
	<input class="loginform" type="text" name="NAME_POLE" value="<?=$NAME_POLE?>" />
	<br /><br />
	
	<b>Номер телефона </b><br />
	<input class="loginform" type="text" name="PHONE_POLE" value="<?=$PHONE_POLE?>" /><br />
	(Например: 8 (495) 123-45-67<br /><br /><br />
	
	Комментарий<br />
	<textarea class="ask" name="TEXT_POLE"><?=$TEXT_POLE?></textarea>
	<p>	</p>
	<div id="submit-note">
		<input type="submit" value="Отправить заявку" class="btn">
		<span class="note">
			Наши специалисты свяжутся с вами в течении 2 часов.
		</span>
	</div>
<? } else { ?>
	<br /><br /><br />
	<h2>Ваш запрос на обратный звонок успешно отправлен.<br />ID Вашей заявки: <strong><?=$PRODUCT_ID?></strong>.</h2>
	<br /><br /><br />
<? } ?>