<?
IncludeModuleLangFile(__FILE__);

class CPixelPlusFormatSF extends CPixelPlusFormat {
		
	public function GetDispayFields(&$arSection) {
		$arSection['DISPLAY_FIELDS'] = Array();
		if (count($this->arFormatted) > 0) {
			foreach ($this->arFormatted as $fid) {			
				
				$arParams = $this->paramformatclass->GetParam($fid);
				
				unset($arEventRes);
				$events = GetModuleEvents("pixelplus.acomponents", "OnFormatSectionField");
				while ($arEvent = $events->Fetch()) {
					$arEventRes = ExecuteModuleEventEx($arEvent, array($fid,&$arSection,&$arParams));
				}
				if (isset($arEventRes)) {
					if ($arEventRes === false) {
						continue;
					} elseif ($arEventRes !== true) {
						$arSection["DISPLAY_FIELDS"][$fid] = $arEventRes;
						continue;
					}
				}
				
				$arValue['VALUE'] = $arSection[$fid];
				$arValue['~VALUE'] = $arSection["~".$fid];
				if ($arValue['VALUE']) {
					if ($fid == "PICTURE" || $fid == "DETAIL_PICTURE") {
						$arSection[$fid] = CFile::GetFileArray($arValue['VALUE']);
						
						if (is_array($arSection[$fid]) && $arParams['RESIZE'] && CPixelPlusFormatParamsC::FormatCheck('RESIZE',$arParams['RESIZE'])) {
							$arSection["PX_RESIZED_".$fid] = CFile::ResizeImageGet(
								$arSection[$fid],
								$arParams['RESIZE']['arSize'],
								$arParams['RESIZE']['resizeType'],
								$arParams['RESIZE']['bInitSizes'],
								$arParams['RESIZE']['arFilters']
							);
							$arSection["DISPLAY_FIELDS"][$fid] = '<img src="'.$arSection["PX_RESIZED_".$fid]['src'].'"/>';
						} else {
							$arSection["DISPLAY_FIELDS"][$fid] = '<img src="'.$arSection[$fid]['SRC'].'"/>';
						}					
					} elseif ($fid == "ACTIVE") {
						if ($arSection[$fid] == "Y") {
							$arSection["DISPLAY_FIELDS"][$fid] = getMessage('PIXELPLUS_UF_MOD_YES');
						} else {
							$arSection["DISPLAY_FIELDS"][$fid] = getMessage('PIXELPLUS_UF_MOD_NO');
						}
					} elseif ($fid == "MODIFIED_BY" || $fid == "CREATED_BY") {
						if(!array_key_exists($arValue['VALUE'],CPixelPlusFormatSF::$CACHE["U"])) {
							$arFilter["=ID"] = $arValue['VALUE'];
							$rsUser = CUser::GetList($gby,$gorder,$arFilter);
							CPixelPlusFormatSF::$CACHE["U"][$arValue['VALUE']] = $rsUser->GetNext();
						}
						if(is_array(CPixelPlusFormatSF::$CACHE["U"][$arValue['VALUE']])) {
							$arUser = CPixelPlusFormatSF::$CACHE["U"][$arValue['VALUE']];
							$arSection["DISPLAY_FIELDS"][$fid] = $arUser['LAST_NAME']." ".$arUser['NAME'];
						}
					} else {
						$arSection["DISPLAY_FIELDS"][$fid] = $arSection[$fid];
					}
				}
			}
		}
	}
}
?>