<?
Header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
Header("Cache-Control: no-cache, must-revalidate");
Header("Pragma: no-cache"); 
Header("Last-Modified: ".gmdate("D, d M Y H:i:s")."GMT"); 

require($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/prolog_before.php");
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
global $IB_CATALOG;
global $IB_ONECLK;

$elID = CSofteffect::CODEvsID($_REQUEST['ELEMENT'], $IB_CATALOG, TRUE);
$send=false;

$NAME_POLE = htmlspecialchars(trim($_REQUEST['NAME_POLE']), ENT_QUOTES);
$PHONE_POLE = htmlspecialchars(trim($_REQUEST['PHONE_POLE']), ENT_QUOTES);
$NAME_POLE = htmlspecialchars(trim($_REQUEST['NAME_POLE']), ENT_QUOTES);
$TEXT_POLE = htmlspecialchars(trim($_REQUEST['TEXT_POLE']), ENT_QUOTES);

if ($_REQUEST['go1']=='1') {
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
		$PROP['ELEMENT'] = $_REQUEST['ELEMENT'];
		$PROP['PHONE'] = $PHONE_POLE;
		
		$date = date('d.m.Y H:i:s');

		$arLoadProductArray = Array(
		  	"MODIFIED_BY"    	=> $USER->GetID(),
		  	"IBLOCK_SECTION_ID" => false,
		  	"IBLOCK_ID"      	=> $IB_ONECLK, //заказ в 1 кликg
		  	"PROPERTY_VALUES"	=> $PROP,
		  	"NAME"           	=> date('Y-m-d').' ['.$_REQUEST['NAME_POLE'].']',
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
			
			if ($arLoadProductArray['IBLOCK_ID']==$IB_ONECLK)
			CEvent::Send("NEW_PHONE", SITE_ID, $arEventFields,'Y', '#template_1clk#');
			$send=true;
		} else {
			$send=false;
			$err = 'При отправке сообщения произошла ошибка. Повторите попытку позднее.';
		}
	}
} 
?>
<? if (!$send) { 
	$arResult["CAPTCHA_CODE"] = htmlspecialchars($APPLICATION->CaptchaGetCode()); ?>
	<input type="hidden" name="ELEMENT" value="<?=$elID?>" />
	<input type="hidden" name="go1" value="1" />
	<? if (strlen($err)>0) { ?>
		<span style="color: #94002a;"><?=$err?></span><br /><br />
	<? } ?>
	Ваше имя <span class="redstar">*</span><br />
	<input class="loginform" type="text" name="NAME_POLE" value="<?=$NAME_POLE?>" /><br />
	
	Номер телефона <span class="redstar">*</span><br />
	<input class="loginform" type="text" name="PHONE_POLE" value="<?=$PHONE_POLE?>" /><br />
	(Например: 8 (111) 123-45-67)<br /><br /><br />
	
	Продукты для заказа<br />
	<textarea class="ask" name="TEXT_POLE"><?=$TEXT_POLE?></textarea>
	<p>После получения заказа, наши специалисты свяжутся с вами в течении часа. В рабочее врмя с 7 до 23.</p>
	<div id="submit-note">
		<input type="submit" value="Заказать" class="btn">
		<span class="note">
			<span class="redstar">*</span> &mdash; отмеченные этим знаком поля обязательны для заполнения
		</span>
	</div>
<? } else { ?>
	<br /><br /><br />
	<h2>Ваш заказ №  <strong><?=$PRODUCT_ID?></strong> принят в обработку.<br /> </h2>
	<br /><br /><br />
<? } ?>