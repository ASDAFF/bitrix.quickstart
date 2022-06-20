<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 36000;
if(count($arParams['SOCIAL'])){
if($this->StartResultCache(false, ($arParams["CACHE_GROUPS"]==="N"? false: $USER->GetGroups())))
{
		$this->SetResultCacheKeys(array("PROPERTIES"));

		$arResult['PROPERTIES']['SIZE']=$arParams['SIZE'].'px';
		($arParams['POSITION']=='Y')?$arResult['PROPERTIES']['POSITION']='vertical':$arResult['PROPERTIES']['POSITION']='horizontal';
		($arParams['DARK']=='Y')?$color='light':$color='dark';
		
		($arParams['OTHER'])?$arParams['SOCIAL']=array_merge($arParams['SOCIAL'] ,$arParams['OTHER']):'';
		foreach($arParams['SOCIAL'] as $soc){
			switch ($soc) {
				case 'VK':
					$link='http://vk.com/'.$arParams[$soc.'_GROUPS']; break;
				case 'FB':
					$link='https://www.facebook.com/'.$arParams[$soc.'_GROUPS']; break;
				case 'OK':
					$link='http://www.odnoklassniki.ru/'.$arParams[$soc.'_GROUPS']; break;
				case 'TW':
					$link='http://twitter.com/'.$arParams[$soc.'_GROUPS']; break;
				case 'GP':
					$link='https://plus.google.com/'.$arParams[$soc.'_GROUPS']; break;
				case 'MM':
					$link='http://my.mail.ru/community/'.$arParams[$soc.'_GROUPS']; break;
				case 'HH':
					$link='http://habrahabr.ru/company/'.$arParams[$soc.'_GROUPS']; break;
				case 'BX':
					$link='http://www.1c-bitrix.ru/partners/'.$arParams[$soc.'_GROUPS'].'.php'; break;
				case 'GH':
					$link='https://github.com/'.$arParams[$soc.'_GROUPS']; break;
				case 'IG':
					$link='http://instagram.com/'.$arParams[$soc.'_GROUPS']; break;
				case 'YT':
					$link='http://www.youtube.com/channel/'.$arParams[$soc.'_GROUPS']; break;
			}
			
			$arResult['PROPERTIES']['SOCIAL'][$soc]['LINK']=$link;
			
			if($arParams[$soc.'_Y']=='Y' && $arParams[$soc.'_ICONS']){
				$arResult['PROPERTIES']['SOCIAL'][$soc]['ICONS']=$arParams[$soc.'_ICONS'];
			}
			else{
				$arResult['PROPERTIES']['SOCIAL'][$soc]['ICONS']=$componentPath.'/images/'.$color.'/'.$soc.'.png';
			}
			
			if($arParams[$soc.'_Y']=='Y' && $arParams[$soc.'_ICONS_HOVER']){
				$arResult['PROPERTIES']['SOCIAL'][$soc]['ICONS_HOVER']=$arParams[$soc.'_ICONS_HOVER'];
			}
			else{
				$arResult['PROPERTIES']['SOCIAL'][$soc]['ICONS_HOVER']=$componentPath.'/images/hover/'.$soc.'.png';
			}
		}
		
	if($arParams['JQUERY']=='Y')$APPLICATION->AddHeadScript("//code.jquery.com/jquery-1.10.2.min.js");
	if($arParams['JQUERY_UI']=='Y')$APPLICATION->AddHeadScript("//code.jquery.com/ui/1.10.3/jquery-ui.js");
	$APPLICATION->AddHeadScript($componentPath.'/js/script.js');
		
 	$this->IncludeComponentTemplate();
}
}?>