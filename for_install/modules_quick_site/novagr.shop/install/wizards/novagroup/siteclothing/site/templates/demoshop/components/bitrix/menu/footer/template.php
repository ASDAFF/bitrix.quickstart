<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();


//deb($arResult);

if (count($arResult) < 1)
	return;

//$bManyIblock = array_key_exists("IBLOCK_ROOT_ITEM", $arResult[0]["PARAMS"]);

//echo "<ul >\n";


$i = 0;
$j = 0;
foreach($arResult as $key => $arItem) {

		if (is_array($arItem) && array_key_exists("ITEMS", $arItem) && count($arItem["ITEMS"]) > 0):
		
						$sub_counter = 1;
						$previousLevel = 2;
						$bFirst = true;
						foreach ($arItem["ITEMS"] as $key => $arSubItem) {

							if ($arSubItem["DEPTH_LEVEL"] == 2) {
 								
								//deb($arSubItem);
								if (!$bFirst) {
									?>
	 								</ul><ul>
	 								<?php 
								} else {
									?>
	 								<ul>
	 								<?php 
								}
							
								?>
								<li ><strong><a href="<?=$arSubItem["LINK"]?>" ><?=$arSubItem["TEXT"]?></a></strong></li>
								<?php

								

							} elseif ($arSubItem["DEPTH_LEVEL"] == 3) {

								?>								
								<li><a href="<?=$arSubItem["LINK"]?>"><?=$arSubItem["TEXT"]?></a></li>
								<?php
								//continue;
							} elseif ($arSubItem["DEPTH_LEVEL"] == 4) {
								continue;	
								/*?>
								<li><a href="<?=$arSubItem["LINK"]?>"><?=$arSubItem["TEXT"]?></a></li>
								<?php */
							}
							
							$previousLevel = $arSubItem["DEPTH_LEVEL"];
							$sub_counter++;
							$bFirst = false;

						} // foreach ($arItem["ITEMS"] as $key => $arSubItem) {						
									
		endif;
}
?>
</ul>	
<?php
if(is_array($arResult["END"])) {
foreach ($arResult["END"] as $key => $arSubItem) {
	?>
	<ul>
	<li><strong>
	<a href="<?=$arSubItem["LINK"]?>"><?=$arSubItem["TEXT"]?></a>
	</strong>
	</li>
	</ul>
	<?php
} // </ul>
}
?>

