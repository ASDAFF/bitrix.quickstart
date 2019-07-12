<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Часто задаваемые вопросы");

global $IB_FAQ;
$arRes=array();
$dbEl = CIBlockElement::GetList(array('SORT'=>'ASC'), array('IBLOCK_ID'=>$IB_FAQ, 'ACTIVE'=>'Y'), FALSE, FALSE, array('IBLOCK_ID', 'ID', 'NAME', 'PREVIEW_TEXT'));
while ($arEl = $dbEl->GetNext()) {
	##############################################################
	// заменяем ссылки в описании на softeffect.ru для DEMO данных
	// удалить если DEMO данные более не используются
	preg_match_all('|src=\"([^\"]*)\"|', $arEl['PREVIEW_TEXT'], $matches);
	foreach ($matches[1] as $value) {
		if (!file_exists($_SERVER['DOCUMENT_ROOT'].$value)) {
			$arEl['PREVIEW_TEXT'] = str_replace($value, "http://softeffect.ru".$value, $arEl['PREVIEW_TEXT']);
		}
	}
	
	$arRes[]=array(
		'NAME' => $arEl['NAME'],
		'TEXT' => $arEl['PREVIEW_TEXT'],
		'ID'   => $arEl['ID']
	);
}
?>


<div class="content contenttext" id="helpfaq">
	<ul style="padding-bottom: 20px;" class="imgdots noprint">
		<? foreach ($arRes as $key => $value) { ?>
			<li><a href="#faq<?=$value['ID']?>"><?=$value['NAME']?></a></li>
		<? } ?>
	</ul>
	<? foreach ($arRes as $key => $value) { ?>
		<div class="faqentry">
			<a name="faq<?=$value['ID']?>"></a>
			<h2 class="subheader"><span class="qa">В: </span><?=$value['NAME']?></h2>
			<p><span class="qa">О: </span><?=$value['TEXT']?></p>
		</div>
	<? } ?>
</div>
<script type="text/javascript" language="JavaScript">
<!--
$(document).ready(function() {
	// Help FAQ hover handler
	$("div.faqentry").hover(
		function () { $(this).addClass("over");	},
		function () { $(this).removeClass("over"); }
	);
});
-->
</script>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>