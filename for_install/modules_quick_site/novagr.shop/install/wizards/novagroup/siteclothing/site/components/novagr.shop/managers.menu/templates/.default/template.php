<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (!empty($_REQUEST["section_code"]))	$expandSectionCode = $_REQUEST["section_code"];
else $expandSectionCode = '';
//deb($arResult["SECTIONS"]);
?>
<div class="tree" >
	<ul role="tree">
		<li role="treeitem" class="parent_li" data-level="0">
			<ul role="group">
<?php
function dealerPrintSection($arSection, &$arSECTIONS, $expandSectionCode="", $printCountElems = false, $useLinks = false, $markOpenSection = false) {
	//deb($arSection);
	if (count($arSection["CHILDS_IDS"])>0) {
		$childFlag = true;
	} else {
		$childFlag = false;
	}
		
	
	if ($useLinks == true) $linkSection = "/managers-cabinet/".$arSection["CODE"]."/";
	else $linkSection = $linkSection = "#";
	?>
	<li <?=( $arSection["CODE"] == $expandSectionCode && $markOpenSection == true ? 'id="open-section"' : "")?> data-level="<?=$arSection["DEPTH_LEVEL"]?>" <?=( $childFlag == true ? 'class="parent_li"' : "")?> <?=( $arSection["DEPTH_LEVEL"] >1 ? 'style="display: none;"' : "")?> >
		<span class="level-<?=$arSection["DEPTH_LEVEL"]?><?=( $childFlag == true ? '' : " no-levels")?>">
		<?=( $childFlag == true ? '<span class="collapse"><i class="icon-plus-sign"></i></span>' : "")?> <a data-section-id="<?=$arSection["ID"]?>" href="<?=$linkSection?>" ><?=$arSection["NAME"]?></a> <?=( $printCountElems == true ? '<span class="number">('.$arSection["COUNT"].')</span>' : '')?></span>
		<?php 
		if ($childFlag == true &&  $arSection["DEPTH_LEVEL"] < 7) {
			?>
			<ul>
			<?php 
			foreach ($arSection["CHILDS_IDS"] as $item) {
				$curSection = $arSECTIONS[$item];
				dealerPrintSection($curSection, $arSECTIONS, $expandSectionCode, $printCountElems, $useLinks, $markOpenSection);
			}
			?>
			</ul>
			<?php 
		}	
		?>
	</li>	
	<?php		
} 
	
foreach($arResult["SECTIONS"] as $arSection) {
	//deb($arSection);
	    
    if ($arSection["DEPTH_LEVEL"] == 1) {
		$linkSection = $link.$arSection["CODE"]."/";
		dealerPrintSection($arSection, $arResult["SECTIONS"], $expandSectionCode, true, true, true);
	}
}		
?>	
		</ul>
	</li>
</ul>
</div>

<div aria-hidden="false" role="dialog" tabindex="-1" class="modal hide fade office" id="ListTree">
	<div class="modal-header">
	<button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
	<h2 ><?=GetMessage("CHOOSE_PRODUCT_GROUP")?></h2>
	</div>
	<div class="modal-body">
		<div class="tree">
		<ul role="tree">
			<li role="treeitem" class="parent_li">
				<ul role="group">
				<?php 
				foreach($arResult["SECTIONS"] as $arSection) {
	//deb($arSection);
	    
				    if ($arSection["DEPTH_LEVEL"] == 1) {
						$linkSection = $link.$arSection["CODE"]."/";
						dealerPrintSection($arSection, $arResult["SECTIONS"], $expandSectionCode, false, false, false);
					}
				}
				?>
				</ul>
			</li>
		</ul>
		</div>
	</div>
</div>
