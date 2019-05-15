<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div id="price-generation">
<form method='POST' action=''>
	<span class="slobel_msg">
		<?if($arResult['RESULT']!='Y'):?>
			<?=GetMessage('MS_GEN')?>
		<?else:?>
			<?=GetMessage('RESULT_GEN')?> 
			<a class="slobel_a" href="<?=$arResult['PATH'].$arResult['FILE_NAME'].'.'.$arResult['FILE_EXPANSION']?>"><?=GetMessage('DOWNLOAD_GEN')?></a>
		<?endif;?>
	</span>
		<input type="hidden" name="gen" value="Y"/>
		<button type="submit" class="slobel_btn"><?=GetMessage('START_GEN')?></button>
	</form>
</div>