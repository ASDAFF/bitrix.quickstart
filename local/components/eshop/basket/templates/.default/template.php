<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if (!$arResult["ERROR_MESSAGE"])
{
	if(is_array($arResult["WARNING_MESSAGE"]) && !empty($arResult["WARNING_MESSAGE"])) {
		foreach($arResult["WARNING_MESSAGE"] as $v) {
			echo ShowError($v);
		}
	}
	
	if($arParams['JQUERY']=="Y") {
		$APPLICATION->AddHeadScript('https://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js');
	}	
	
	if ($arParams['BORDER'] == "Y") {
		$arParams['STYLE'] .= " beono-basket_border";
	}
	
	if (in_array($_GET['tab'], array('', 'delayed', 'missing'))) {
		$arResult['TAB'] = $_GET['tab'];
	}
	?>	
	<div class="beono-basket <?=$arParams['STYLE'];?>">	
	<?if($_GET['tab'] != '' || $arResult["ShowDelay"]=="Y" || $arResult["ShowNotAvail"]=="Y"):?>
		<div class="beono-basket-menu">
			<?if($_GET['tab'] == ''):?>
			<div class="beono-active"><?=GetMessage('BEONO_BASKET_READY_TITLE');?>&nbsp;<sup><?=count($arResult["ITEMS"]["AnDelCanBuy"]);?></sup></div>
			<?else:?>			
			<div><a href="<?=$APPLICATION->GetCurPageParam('', array('tab'));?>"><?=GetMessage('BEONO_BASKET_READY_TITLE');?></a>&nbsp;<sup><?=count($arResult["ITEMS"]["AnDelCanBuy"]);?></sup></div>
			<?endif;?>	
						
			<?if($_GET['tab'] == 'delayed'):?>
				<div class="beono-active"><?=GetMessage('BEONO_BASKET_DELAY_TITLE');?>&nbsp;<sup><?=count($arResult["ITEMS"]["DelDelCanBuy"]);?></sup></div>
			<?else:?>			
				<div><a href="<?=$APPLICATION->GetCurPageParam('tab=delayed', array('tab'));?>"><?=GetMessage('BEONO_BASKET_DELAY_TITLE');?></a>&nbsp;<sup><?=count($arResult["ITEMS"]["DelDelCanBuy"]);?></sup></div>
			<?endif;?>
			
			<?if($arResult["ShowNotAvail"]=="Y"):?>
				<?if($_GET['tab'] == 'missing'):?>
					<div class="beono-active"><?=GetMessage('BEONO_BASKET_UNAVAIL_TITLE');?>&nbsp;<sup><?=count($arResult["ITEMS"]["nAnCanBuy"]);?></sup></div>
				<?else:?>			
					<div><a href="<?=$APPLICATION->GetCurPageParam('tab=missing', array('tab'));?>"><?=GetMessage('BEONO_BASKET_UNAVAIL_TITLE');?></a>&nbsp;<sup><?=count($arResult["ITEMS"]["nAnCanBuy"]);?></sup></div>
				<?endif;?>
			<?endif;?>
		</div>
	<?endif;?>
			<?
			if ($_GET['tab'] == '') {
				
				if ($arResult["ShowReady"]=="Y") {
					$arResult["ITEMS"]['CURRENT'] = $arResult["ITEMS"]["AnDelCanBuy"];
				} else {
					ShowError(GetMessage('BEONO_BASKET_NO_READY'));
				}
				
			} else {
				if ($key = array_search('QUANTITY', $arParams["COLUMNS_LIST"])) {
					unset($arParams["COLUMNS_LIST"][$key]);
				}

				if ($_GET['tab'] == 'delayed') {		
					if ($arResult["ShowDelay"]=="Y") {
						$arResult["ITEMS"]['CURRENT'] = $arResult["ITEMS"]["DelDelCanBuy"];
					} else {
						ShowError(GetMessage('BEONO_BASKET_NO_DELAYED'));
					}
				} else if ($_GET['tab'] == 'missing') {					
					if ($arResult["ShowNotAvail"]=="Y") {
						$arResult["ITEMS"]['CURRENT'] = $arResult["ITEMS"]["nAnCanBuy"];
					} else {
						ShowError(GetMessage('BEONO_BASKET_NO_MISSING'));
					}
				}
			}
			
			if ($arResult["ITEMS"]['CURRENT']) {
				include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/basket_items.php");
			}
			?>
	</div>
	<?
} else {
	ShowError($arResult["ERROR_MESSAGE"]);
}
/**
 * Copyright (c) 25/7/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

?>