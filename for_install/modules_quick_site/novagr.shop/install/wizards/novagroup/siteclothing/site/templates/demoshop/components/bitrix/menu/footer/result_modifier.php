<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
//deb($arResult);
if (!empty($arResult)):

	$top_key = -1;
	foreach($arResult as $key => $arItem):
		// пункты с параметром MAIN_MENU - помещаем  в отдельный массив и приклииваем в конец списка
		// при выводе
		if ($arItem["DEPTH_LEVEL"] == 1 && $arItem["PARAMS"]["MAIN_MENU"] == 1) {
			$arFormatted["END"][] = $arItem;
		} elseif ($arItem["DEPTH_LEVEL"] == 1 && $arItem["PARAMS"]["MAIN_MENU"] != 1) {
		//deb($arItem);
			//if ($arItem["TEXT"] != 'Продукция') {
				continue;
		
			//}
	
			$top_key++;
			$arFormatted["TOP"][$top_key] = $arItem;
		} elseif ($arItem['PERMISSION'] > 'D') {
			$arFormatted["TOP"][$top_key]["ITEMS"][] = $arItem;
		}
	endforeach;
	
	/*foreach($arFormatted["TOP"] as $key => $arTopItem):
		
		if (count($arTopItem["ITEMS"]) > 12)
			$arFormatted["TOP"][$key]["LARGE"] = true;
		else
			$arFormatted["TOP"][$key]["LARGE"] = false;
	endforeach;*/
	
endif;
//deb("===========");
//deb($arFormatted["END"]);
//$j=0;
// подсчитываем число подразделов в разделах 2 уровня(Женская Одежда, Обувь)
if(is_array($arFormatted["TOP"]))
foreach($arFormatted["TOP"] as $key => $arTopItem) {
	//deb($arTopItem);
	
	$current = 0;
	$previous = '';
	foreach($arTopItem["ITEMS"] as $k => $val) {
		//deb($k);
		//deb($val);
		
		if ($val["DEPTH_LEVEL"] == 2 ) {
			
			if ($current>0) $arFormatted["TOP"][$key]["ITEMS"][$previous]["COUNT_CHILDS"] = $current;
			
			
			//$j++;
			/*if ($j>3) {
				deb($current);
				deb($previous);
				die('hhh');
			}*/
			$previous = $k;
			$current = 0;
			//$previousIndex = $k;
			//$previous
			//$current = 
		}
		
		if ($val["DEPTH_LEVEL"] == 3) {
			$current++;
		}
		
		
		//$previous
	}
	// для последнего элемента 2го уровня( акксессуары)
	if ($current>0) {
		// подсчитывем число строк и столбцов - в строке 4 элемента
		//$current
		//$rounded = round($current/4);
		$countTd = 3;
		$ceiled = ceil($current/$countTd);
		// число пустых <td>
		$pureTd = 0;
		if ($current>=$countTd) {
			$pureTd = $countTd-($current % $countTd);
		}
		//die('$current ' .$current . ' $$ceiled ' . $ceiled . ' $pureTd ' . $pureTd);
		
		
		$arFormatted["TOP"][$key]["ITEMS"][$previous]["PURE_TD"] = $pureTd;
		$arFormatted["TOP"][$key]["ITEMS"][$previous]["COUNT_CHILDS"] = $current;
		$arFormatted["TOP"][$key]["ITEMS"][$previous]["ROWS"] = $ceiled;
	}
}
//deb($arResult);
$arResult = $arFormatted["TOP"];

$arResult["END"] = $arFormatted["END"];
//deb("=============================");
//echo '<pre>'; print_r($arResult); echo '</pre>';
?>