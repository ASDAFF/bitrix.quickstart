<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Localization\Loc;
use Bitrix\Sale\Location;

Loc::loadMessages(__FILE__);

if (!empty($arResult['ERRORS']['FATAL'])) {
	foreach ($arResult['ERRORS']['FATAL'] as $error) {
		?><div class="alert alert-danger"><?=$error?></div><?
	}
} else {
	?><div id="sls-<?=$arResult['RANDOM_TAG']?>" class="bx-sls <?if(strlen($arResult['MODE_CLASSES'])):?> <?=$arResult['MODE_CLASSES']?><?endif?>"><?
		if (is_array($arResult['DEFAULT_LOCATIONS']) && !empty($arResult['DEFAULT_LOCATIONS'])) {
			?><div class="bx-ui-sls-quick-locations quick-locations"><?
			foreach($arResult['DEFAULT_LOCATIONS'] as $lid => $loc) {
				?><a href="javascript:void(0)" data-id="<?=intval($loc['ID'])?>" class="quick-location-tag"><?=htmlspecialcharsbx($loc['NAME'])?></a><?
			}
			?></div><?
		}
		?><div class="dropdown-block bx-ui-sls-input-block"><?
			?><span class="dropdown-icon"></span><?
			?><input type="text" autocomplete="off" name="<?=$arParams['INPUT_NAME']?>" value="<?=$arResult['VALUE']?>" class="dropdown-field" placeholder="<?=Loc::getMessage('SALE_SLS_INPUT_SOME')?> ..." /><?
			?><div class="dropdown-fade2white"></div><?
			?><div class="bx-ui-sls-loader"></div><?
			?><div class="bx-ui-sls-clear" title="<?=Loc::getMessage('SALE_SLS_CLEAR_SELECTION')?>"></div><?
			?><div class="bx-ui-sls-pane"></div><?
			?></div><?

		if(!$arParams['SUPPRESS_ERRORS']) {
			?><script type="text/html" data-template-id="bx-ui-sls-error">
				<div class="bx-ui-sls-error">
					<div></div>
					{{message}}
				</div>
			</script><?
		}
		?><script type="text/html" data-template-id="bx-ui-sls-dropdown-item">
			<li class="dropdown-item bx-ui-sls-variant">
				<span class="dropdown-item-text">{{display_wrapped}}</span>
				<?if($arResult['ADMIN_MODE']):?>
					[{{id}}]
				<?endif?>
			</li>
		</script><?

		?><div class="bx-ui-sls-error-message"><?
			if (!empty($arResult['ERRORS']['NONFATAL'])) {
				foreach ($arResult['ERRORS']['NONFATAL'] as $error) {
					?><div class="alert alert-danger"><?=$error?></div><?
				}
			}
			?></div><?
		?></div><?
	?><div class="display:none"><?
		?><script>
			<?
			$bIsAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) || (isset($_REQUEST['AJAX_CALL']) && $_REQUEST['AJAX_CALL']=='Y');
			if (!$bIsAjax) {
				CJSCore::Init();
				$GLOBALS['APPLICATION']->AddHeadScript('/bitrix/js/sale/core_ui_widget.js');
				$GLOBALS['APPLICATION']->AddHeadScript('/bitrix/js/sale/core_ui_etc.js');
				$GLOBALS['APPLICATION']->AddHeadScript('/bitrix/js/sale/core_ui_autocomplete.js');
			} else {
				CJSCore::Init(array(), true);?>
				if (window.jQuery) {
					$.ajax({url:'/bitrix/js/sale/core_ui_widget.js',dataType:'script',cache:true})
						.then(function(){
							return $.ajax({url:'/bitrix/js/sale/core_ui_etc.js',dataType:'script',cache:true});
						}).then(function(){
							return $.ajax({url:'/bitrix/js/sale/core_ui_autocomplete.js',dataType:'script',cache:true});
						}).then(function(){
							return $.ajax({url:'<?=$templateFolder?>/script.js',dataType:'script',cache:true});
						}).then(function() {
			<?}?>
				 if (!window.BX && top.BX)
					window.BX = top.BX;

				<?if(strlen($arParams['JS_CONTROL_DEFERRED_INIT'])):?>
					 if(typeof window.BX.locationsDeferred == 'undefined') window.BX.locationsDeferred = {};
					 window.BX.locationsDeferred['<?=$arParams['JS_CONTROL_DEFERRED_INIT']?>'] = function(){
				<?endif?>

				<?if(strlen($arParams['JS_CONTROL_GLOBAL_ID'])):?>
					 if(typeof window.BX.locationSelectors == 'undefined') window.BX.locationSelectors = {};
					 window.BX.locationSelectors['<?=$arParams['JS_CONTROL_GLOBAL_ID']?>'] =
				<?endif?>

				new BX.Sale.component.location.selector.search(<?=CUtil::PhpToJSObject(array(

				// common
				'scope' => 'sls-'.$arResult['RANDOM_TAG'],
				'source' => $this->__component->getPath().'/get.php',
				'query' => array(
					'FILTER' => array(
						'EXCLUDE_ID' => intval($arParams['EXCLUDE_SUBTREE']),
						'SITE_ID' => $arParams['FILTER_BY_SITE'] && !empty($arParams['FILTER_SITE_ID']) ? $arParams['FILTER_SITE_ID'] : ''
					),
					'BEHAVIOUR' => array(
						'SEARCH_BY_PRIMARY' => $arParams['SEARCH_BY_PRIMARY'] ? '1' : '0',
						'LANGUAGE_ID' => LANGUAGE_ID
					),
				),

				'selectedItem' => !empty($arResult['LOCATION']) ? $arResult['LOCATION']['VALUE'] : false,
				'knownItems' => $arResult['KNOWN_ITEMS'],
				'provideLinkBy' => $arParams['PROVIDE_LINK_BY'],

				'messages' => array(
					'nothingFound' => Loc::getMessage('SALE_SLS_NOTHING_FOUND'),
					'error' => Loc::getMessage('SALE_SLS_ERROR_OCCURED'),
				),

				// "js logic"-related part
				'callback' => $arParams['JS_CALLBACK'],
				'useSpawn' => $arParams['USE_JS_SPAWN'] == 'Y',
				'initializeByGlobalEvent' => $arParams['INITIALIZE_BY_GLOBAL_EVENT'],
				'globalEventScope' => $arParams['GLOBAL_EVENT_SCOPE'],

				// specific
				'pathNames' => $arResult['PATH_NAMES'], // deprecated
				'types' => $arResult['TYPES'],

			), false, false, true)?>);

				<?if(strlen($arParams['JS_CONTROL_DEFERRED_INIT'])):?>
				 };
				<?endif?>

				<?if ($bIsAjax){?>
					});
				}
			<?}?>
		</script><?
		?></div><?
}