<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die(); ?>

<?if(function_exists('yenisite_GetCompositeLoader')){global $MESS;$MESS ['COMPOSITE_LOADING'] = yenisite_GetCompositeLoader();}?>

<?if(method_exists($this, 'createFrame')) $frame = $this->createFrame()->begin(GetMessage('COMPOSITE_LOADING'));?>

<script src="http://api.torg.mail.ru/static/js/widget.min.js"></script>  
<script type="text/javascript"> 
	TORG.init({ClientId: <?=$arParams['ClientId'];?>});  
	TORG.Widgets.Reviews("torg_reviews", {
		<?if($arParams["param"]):?>param:<?=$arParams['param'];?>,<?endif;?>
		<?if($arParams["onpage"]):?>onpage:<?=$arParams['onpage'];?>,<?endif;?>
		<?if($arParams["pager"]):?>pager:<?=$arParams['pager'];?>,<?endif;?>
		<?if($arParams["font_color"]):?>font_color:"<?=$arParams['font_color'];?>",<?endif;?>
		<?if($arParams["background_color"]):?>background_color:"<?=$arParams['background_color'];?>",<?endif;?>
		<?if($arParams["ModelId"]):?>ModelId:<?=$arParams['ModelId'];?>,<?endif;?>
	});  
</script>  
<div id="torg_reviews">
	<span style="display: none">
		<a href="http://torg.mail.ru"><?=GetMessage("TORGMAILRU");?></a>
	</span> 
</div>
         
<?if(method_exists($this, 'createFrame')) $frame->end();?>