<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$strReturn = '';
if($arResult){
	CModule::IncludeModule("iblock");
	global $OptimusSectionID;
	$cnt = count($arResult);
	$lastindex = $cnt - 1;
	$bShowCatalogSubsections = COption::GetOptionString("aspro.optimus", "SHOW_BREADCRUMBS_CATALOG_SUBSECTIONS", "Y", SITE_ID) == "Y";
	
	for($index = 0; $index < $cnt; ++$index){
		$arSubSections = array();
		$arItem = $arResult[$index];
		$title = htmlspecialcharsex($arItem["TITLE"]);
		$bLast = $index == $lastindex;
		if($OptimusSectionID && $bShowCatalogSubsections){
			$arSubSections = COptimus::getChainNeighbors($OptimusSectionID, $arItem['LINK']);
		}
		if($index){
			$strReturn .= '<span class="separator">-</span>';
		}
		if($arItem["LINK"] <> "" && $arItem['LINK'] != GetPagePath() && $arItem['LINK']."index.php" != GetPagePath() || $arSubSections){
			if($arSubSections){
				$strReturn .= '<div class="drop">';
					if($index == ($cnt-1)):
						$strReturn .= '<span class="number" id="bx_breadcrumb_'.$index.'">';
					else:
						$strReturn .= '<a class="number" id="bx_breadcrumb_'.$index.'" href="'.$arItem["LINK"].'">';
					endif;
					$strReturn .=($arSubSections ? '<span>'.$title.'</span><b class="space"></b><span class="separator'.($bLast ? ' cat_last' : '').'"></span>' : '<span>'.$title.'</span>');
					if($index == ($cnt-1)):
						$strReturn .= '</span>';
					else:
						$strReturn .= '</a>';						
					endif;
					$strReturn .= '<div class="dropdown_wrapp"><div class="dropdown">';
						foreach($arSubSections as $arSubSection){
							$strReturn .= '<a href="'.$arSubSection["LINK"].'">'.$arSubSection["NAME"].'</a>';
						}
					$strReturn .= '</div></div>';
				$strReturn .= '</div>';
			}
			else{
				$strReturn .= '<a href="'.$arItem["LINK"].'" id="bx_breadcrumb_'.$index.'" title="'.$title.'"><span>'.$title.'</span></a>';
			}
		}
		else{
			$strReturn .= '<span>'.$title.'</span>';
		}
	}
	
	return '<div class="breadcrumbs" id="navigation">'.$strReturn.'</div>';
}
else{
	return $strReturn;
}
?>