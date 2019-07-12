<?if($arResult["STORES"]):?>
	<?
	CModule::IncludeModule('catalog');
	$arStoresIDs = $arStoresByID = array();
	foreach($arResult["STORES"] as $key => $arStore){
		$arResult["STORES"][$key]['SCHEDULE'] = htmlspecialchars_decode($arStore['SCHEDULE']);
		$arResult["STORES"][$key]['KEY'] = $key;
		$arStoresByID[$arStore['ID']] = &$arResult["STORES"][$key];
		$arStoresIDs[] = $arStore['ID'];
	}

	if($arStoresIDs){
		$dbRes = CCatalogStore::GetList(array('ID' => 'ASC'), array('ID' => $arStoresIDs, 'ACTIVE' => 'Y'), false, false, array("ID", "SORT", "TITLE", "IMAGE_ID", "ADDRESS", "EMAIL", "DESCRIPTION", "UF_METRO"));
		while($arStore = $dbRes->GetNext()){
			$arStoresByID[$arStore['ID']]["SORT"] = $arStore["SORT"];
			$arStoresByID[$arStore['ID']]["IMAGE"] = CFile::ResizeImageGet($arStore["IMAGE_ID"], array('width' => 100, 'height' => 69), BX_RESIZE_IMAGE_EXACT);
			$arStoresByID[$arStore['ID']]["TITLE"] = htmlspecialchars_decode($arStore["TITLE"]);
			$arStoresByID[$arStore['ID']]["ADDRESS"] = htmlspecialchars_decode($arStore["ADDRESS"]);
			$arStoresByID[$arStore['ID']]["ADDRESS"] = $arStoresByID[$arStore['ID']]["TITLE"].((strlen($arStoresByID[$arStore['ID']]["TITLE"]) && strlen($arStoresByID[$arStore['ID']]["ADDRESS"])) ? ', ' : '').$arStoresByID[$arStore['ID']]["ADDRESS"];
			$arStoresByID[$arStore['ID']]["EMAIL"] = htmlspecialchars_decode($arStore["EMAIL"]);
			$arStoresByID[$arStore['ID']]["DESCRIPTION"] = htmlspecialchars_decode($arStore['DESCRIPTION']);
			$arStoresByID[$arStore['ID']]["METRO_PLACEMARK_HTML"] = '';
			if($arStoresByID[$arStore['ID']]["METRO"] = unserialize($arStore['~UF_METRO'])){
				foreach($arStoresByID[$arStore['ID']]['METRO'] as $metro){
					$arStoresByID[$arStore['ID']]["METRO_PLACEMARK_HTML"] .= '<div class="metro"><i></i>'.$metro.'</div>';
				}
			}
		}
	}
	
	if(!function_exists('_sort_stores_by_SORT')){
		function _sort_stores_by_SORT($a, $b){
			return ($a['SORT'] == $b['SORT'] ? ($a['KEY'] < $b['KEY'] ? -1 : 1) : ($a['SORT'] > $b['SORT'] ? 1 : -1));
		}
	}
	
	usort($arResult["STORES"], '_sort_stores_by_SORT');
	?>
<?else:?>
	<?LocalRedirect(SITE_DIR.'contacts/');?>
<?endif;?>