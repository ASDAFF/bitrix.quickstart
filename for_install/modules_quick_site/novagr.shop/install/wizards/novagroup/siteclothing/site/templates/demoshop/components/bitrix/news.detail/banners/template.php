<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

//deb($arResult);
?>
<div class="home-left">
<?php
		for ($i=1; $i<7; $i++) {
			
			if (!empty($arResult["PROPERTIES"]["BANNER" . $i]["VALUE"])) {
				?>
				<div><a href="<?=$arResult["PROPERTIES"]["LINK_BANNER" . $i]["VALUE"]?>"><img width="247" height="247" alt="<?=GetMessage('ALT_DEFAULT');?>" title="<?=GetMessage('ALT_DEFAULT');?>" src="<?=CFile::GetPath($arResult["PROPERTIES"]["BANNER" . $i]["VALUE"])?>"></a></div>
				<?php 
			}
			
		}
		?>
	</div>
	<div class="home-right">
		<?php 
		
		for ($i=7; $i<13; $i++) {
			
			if (!empty($arResult["PROPERTIES"]["BANNER" . $i]["VALUE"])) {
				?>
				<div><a href="<?=$arResult["PROPERTIES"]["LINK_BANNER" . $i]["VALUE"]?>"><img width="247" height="247" alt="<?=GetMessage('ALT_DEFAULT');?>" title="<?=GetMessage('ALT_DEFAULT');?>" src="<?=CFile::GetPath($arResult["PROPERTIES"]["BANNER" . $i]["VALUE"])?>"></a></div>
				<?php 
			}
		}
		?>
	</div>
	<div class="clear"></div>
<?php 
