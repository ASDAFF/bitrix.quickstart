<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<link type="text/css" href="/js/css/prettyPhoto.css" rel="stylesheet" />
<script src="/js/jquery-1.7.1.min.js" type="text/javascript"></script>
<script src="/js/jquery.prettyPhoto.js" type="text/javascript"></script>
<script type="text/javascript">
<!--
$(function(){
  $("a[rel^='prettyPhoto']").prettyPhoto({
  	theme: 'dark_rounded'
  });
});
-->
</script>
<div class="catalog-element">

	<?if($arResult["DETAIL_TEXT"]):?>
		<?=$arResult["DETAIL_TEXT"]?><br />
	<?elseif($arResult["PREVIEW_TEXT"]):?>
		<?=$arResult["PREVIEW_TEXT"]?><br />
	<?endif;?>
	
	<?$koof=150;?>
<?if (count($arResult["PROPERTIES"]["AVARIA"]["SRC"])>0){?>
<?
$kool=count($arResult["PROPERTIES"]["AVARIA"]["SRC"]);
if ($kool==1) $otstup=floor($koof/0.67);
if ($kool==2) $otstup=floor($koof/1.5);
if ($kool>=3) $otstup=floor($koof/5);
?>
	<div class="zag"><?=$arResult["PROPERTIES"]["AVARIA"]["NAME"]?></div>
	<div class="kuzovFoto">
		<?foreach($arResult["PROPERTIES"]["AVARIA"]["SRC"] as $PHOTO){?>
			<a href="<?=$PHOTO?>" rel="PrettyPhoto" style="margin-left:<?=$otstup?>px;margin-right:0;"><img src="<?=$PHOTO?>" height="75"></a>
		<?}?>
	</div>
<?}?>
<?if (count($arResult["PROPERTIES"]["REMONT"]["SRC"])>0){?>
<?
$kool=count($arResult["PROPERTIES"]["REMONT"]["SRC"]);
if ($kool==1) $otstup=floor($koof/0.67);
if ($kool==2) $otstup=floor($koof/1.5);
if ($kool>=3) $otstup=floor($koof/5);
?>
	<div class="zag"><?=$arResult["PROPERTIES"]["REMONT"]["NAME"]?></div>
	<div class="kuzovFoto">
		<?foreach($arResult["PROPERTIES"]["REMONT"]["SRC"] as $PHOTO){?>
			<a href="<?=$PHOTO?>" rel="PrettyPhoto" style="margin-left:<?=$otstup?>px;margin-right:0;"><img src="<?=$PHOTO?>" height="75"></a>
		<?}?>
	</div>
<?}?>
<?if (count($arResult["PROPERTIES"]["POSLEREMONTA"]["SRC"])>0){?>
<?
$kool=count($arResult["PROPERTIES"]["POSLEREMONTA"]["SRC"]);
if ($kool==1) $otstup=floor($koof/0.67);
if ($kool==2) $otstup=floor($koof/1.5);
if ($kool>=3) $otstup=floor($koof/5);
?>
	<div class="zag"><?=$arResult["PROPERTIES"]["POSLEREMONTA"]["NAME"]?></div>
	<div class="kuzovFoto">
		<?foreach($arResult["PROPERTIES"]["POSLEREMONTA"]["SRC"] as $PHOTO){?>
			<a href="<?=$PHOTO?>" rel="PrettyPhoto" style="margin-left:<?=$otstup?>px;margin-right:0;"><img src="<?=$PHOTO?>" height="75"></a>
		<?}?>
	</div>
<?}?>
	
	
	<?
	// additional photos
	$LINE_ELEMENT_COUNT = 2; // number of elements in a row
	if(count($arResult["MORE_PHOTO"])>0):?>
		<a name="more_photo"></a>
		<?foreach($arResult["MORE_PHOTO"] as $PHOTO):?>
			<img border="0" src="<?=$PHOTO["SRC"]?>" width="<?=$PHOTO["WIDTH"]?>" height="<?=$PHOTO["HEIGHT"]?>" alt="<?=$arResult["NAME"]?>" title="<?=$arResult["NAME"]?>" /><br />
		<?endforeach?>
	<?endif?>
	<?if(is_array($arResult["SECTION"])):?>
		<br /><a href="<?=$arResult["SECTION"]["SECTION_PAGE_URL"]?>"><?=GetMessage("CATALOG_BACK")?></a>
	<?else:?>
		<br /><a href="<?=$_SERVER['PHP_SELF']?>"><?=GetMessage("CATALOG_BACK")?></a>
	<?endif?>

</div>
