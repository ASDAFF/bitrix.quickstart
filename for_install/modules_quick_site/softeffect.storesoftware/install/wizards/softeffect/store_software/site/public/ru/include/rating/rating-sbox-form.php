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
global $IB_REVIEWS_GOODS;
$IBLOCK_ID = $IB_CATALOG; // каталог товаров

$send=false;

$NAME_POLE_1 = htmlspecialchars(trim($_REQUEST['NAME_POLE_1']), ENT_QUOTES);
$CITY = htmlspecialchars(trim($_REQUEST['CITY']), ENT_QUOTES);
$TEXT_POLE_1 = htmlspecialchars(trim($_REQUEST['TEXT_POLE_1']), ENT_QUOTES);
$ELEMENT = htmlspecialchars(trim($_REQUEST['ELEMENT']), ENT_QUOTES);
$RATING = htmlspecialchars(trim($_REQUEST['RATING']), ENT_QUOTES);

if ($_REQUEST['go3']=='1') {
	
	if ($NAME_POLE_1=='') {
		$err .= '<b>Вы забыли указать Имя </b><br/>';
		$send=FALSE;
	} 
	
	if ($CITY=='') {
		$err .= '<b>Вы забыли указать Город </b><br/>';
		$send=FALSE;
	} 
	
		
	if ($TEXT_POLE_1=='') {
		$err .= '<b>Вы забыли указать текст отзыва </b><br/>';
		$send=FALSE;
	} 

	if (strlen($err)<=0) {
		$el = new CIBlockElement;

		$PROP = array();
		$PROP['AUTOR'] = $NAME_POLE_1;
		$PROP['CITY'] = $CITY;
		$PROP['ELEMENT'] = array($ELEMENT);	
		$PROP['RATING'] = array("VALUE"=>$RATING);
		$PROP['DATE'] = date('d.m.Y H:i:s');
		$PROP['USER'] = $USER->GetID();
		$date = date('d.m.Y H:i:s');

		$arLoadProductArray = Array(
		  	"MODIFIED_BY"    	=> $USER->GetID(),
		  	"IBLOCK_SECTION_ID" => false,
		  	"IBLOCK_ID"      	=> $IB_REVIEWS_GOODS, //отзывы
		  	"PROPERTY_VALUES"	=> $PROP,
		  	"NAME"           	=> date('Y-m-d').' ['.$NAME_POLE_1.']'.' ['.$CITY.']',
		  	"ACTIVE"         	=> "N",
			"PREVIEW_TEXT"		=> $TEXT_POLE_1,
			"DATE_ACTIVE_FROM"	=> $date
		);
		
	
		if($PRODUCT_ID = $el->Add($arLoadProductArray)) {
			$arEventFields = array(
    			"ID" => $PRODUCT_ID,
				"DATE" => $date,
				"NAME" => $NAME_POLE_1,
				"CITY" => $CITY,
				"TEXT" => $TEXT_POLE_1
    		);
			
			if ($arLoadProductArray['IBLOCK_ID']==$IB_REVIEWS_GOODS)
				CEvent::Send("NEW_PHONE", SITE_ID, $arEventFields, 'Y', '#template_review#');//отправка почтового шаблона Добавлен отзыв
				
			$send=true;
		} else {
			$send=false;
			$err = 'При отправке сообщения произошла ошибка. Повторите попытку позднее.';
		}
	}
} 


?>
<? if (!$send) {
	if ($NAME_POLE_1==""){
		$NAME_POLE_1=$USER->GetFullName();
	} ?>
	
	<input type="hidden" name="ELEMENT" value="<?=$ELEMENT?>" />
	<input type="hidden" name="go3" value="1" />
	<? if (strlen($err)>0) { ?>
		<span style="color: #94002a;"><?=$err?></span><br /><br />
	<? } ?>
	<b>Ваше имя</b> <br />
	<input class="loginform" type="text" name="NAME_POLE_1" value="<?=$NAME_POLE_1?>" />
	<br />
	Город<br />
	<input class="loginform" type="text" name="CITY" value="<?=$CITY?>" /><br />
	(Например: Москва)<br />
	<br />
	<p>
		<? $dbPropLike = CIBlockPropertyEnum::GetList(array('SORT'=>'ASC'), array('IBLOCK_ID'=>$IB_REVIEWS_GOODS, 'CODE'=>'RATING'));
		while ($arPropLike = $dbPropLike->GetNext()) { ?>
			<input type="radio" name="RATING" value="<?=$arPropLike['ID']?>"<? if ($arPropLike['ID']==$RATING) { ?> checked="checked"<? } ?>><?=$arPropLike['VALUE']?><br />
		<? } ?>
  	</p>
  	<br />
	Отзыв<br />
	<textarea class="ask" name="TEXT_POLE_1"><?=$TEXT_POLE_1?></textarea>
	<p>
		Ваш отзыв будет добавлен в течении 24 часов после добавления, после предварительной модерации. 
	</p>
	<div id="submit-note">
		<input type="submit" value="Оставить отзыв" class="btn">
		<span class="note">
			<span class="redstar">*</span> &mdash; отзывы не соответствующие правилам модерации могут быть не опубликованы.
		</span>
	</div>
<? } else { ?>
	<br /><br /><br />
	<h2>Спасибо! Ваш отзыв № <strong><?=$PRODUCT_ID?></strong> отправлен на модерацию.<br /> </h2>
	<br /><br /><br />
<? } ?>