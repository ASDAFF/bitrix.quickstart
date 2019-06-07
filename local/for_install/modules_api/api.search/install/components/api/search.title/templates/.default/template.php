<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();
/**
 * Bitrix vars
 *
 * @var CBitrixComponent         $component
 * @var CBitrixComponentTemplate $this
 * @var array                    $arParams
 * @var array                    $arResult
 * @var array                    $arLangMessages
 * @var array                    $templateData
 *
 * @var string                   $templateFile
 * @var string                   $templateFolder
 * @var string                   $parentTemplateFolder
 * @var string                   $templateName
 * @var string                   $componentPath
 *
 * @var CDatabase                $DB
 * @var CUser                    $USER
 * @var CMain                    $APPLICATION
 */

if(method_exists($this, 'setFrameMode'))
	$this->setFrameMode(true);

$this->addExternalCss($templateFolder . '/css/baron.min.css');
$this->addExternalJs($templateFolder . '/js/baron.min.js');

if($arParams['INCLUDE_CSS'])
	$this->addExternalCss($templateFolder . '/styles.css');
?>
<div class="api-search-title tpl-default" id="<?=$arResult['COMPONENT_ID']?>">
	<form action="<?=$arResult['FORM_ACTION']?>" method="get">
		<div class="api-search-fields">
			<div class="api-query">
				<input class="api-search-input"
				       placeholder="<?=$arParams['INPUT_PLACEHOLDER']?>"
				       autocomplete="off"
				       name="q"
				       maxlength="300"
				       <?if($arParams['USE_SEARCH_QUERY']):?>
				       value="<?=htmlspecialcharsEx($arResult['q'])?>"
				       <?endif?>
				       type="text">
					<span class="api-ajax-icon"></span>
					<span class="api-clear-icon"></span>
			</div>
			<div class="api-search-button">
				<button type="submit"><?=($arParams['BUTTON_TEXT'] ? $arParams['BUTTON_TEXT'] : '<i class="api-search-icon"></i>')?></button>
			</div>
		</div>
		<div class="baron baron__root baron__clipper <?=$arParams['JQUERY_SCROLL_THEME']?>">
			<div class="baron__scroller">
				<div class="api-search-result"></div>
			</div>
			<div class="baron__track">
				<div class="baron__control baron__up">&bigtriangleup;</div>
				<div class="baron__free">
					<div class="baron__bar"></div>
				</div>
				<div class="baron__control baron__down">&bigtriangledown;</div>
			</div>
		</div>
	</form>
</div>
<?
ob_start();
?>
<script type="text/javascript">
	jQuery(function ($) {
		$.fn.apiSearchTitle({
			component_id: '<?=$arResult['COMPONENT_ID']?>',
			parent_id: '<?=$arParams['JQUERY_SEARCH_PARENT_ID']?>',
			container_id: '#<?=$arResult['COMPONENT_ID']?>',
			input_id: '.api-search-input',
			result_id: '.api-search-result',
			scroll_id: '.baron',
			ajax_icon_id: '.api-ajax-icon',
			clear_icon_id: '.api-clear-icon',
			wait_time: <?=intval($arParams['JQUERY_WAIT_TIME'])?>,
			backdrop: {
				active: true,
				id: '<?=$arResult['COMPONENT_ID']?>_backdrop',
				clas: 'api-search-backdrop',
				animate:{
					fadeIn: 0,
					fadeOut: 0
				},
				css: {
					"opacity": "<?=$arParams['JQUERY_BACKDROP_OPACITY']?>",
					"filter": "alpha(opacity=20)",
					"position": "fixed",
					"top": 0,
					"right": 0,
					"bottom": 0,
					"left": 0,
					"z-index": "<?=$arParams['JQUERY_BACKDROP_Z_INDEX']?>",
					"background-color": "<?=$arParams['JQUERY_BACKDROP_BACKGROUND']?>"
				}
			},
			parent: {
				css: {
					"z-index": "<?=++$arParams['JQUERY_BACKDROP_Z_INDEX']?>"
				}
			},
			mess: {}
		});
	});

	$(window).on('load',function(){
		baron({
			root: '.baron',
			scroller: '.baron__scroller',
			bar: '.baron__bar',
			scrollingCls: '_scrolling',
			draggingCls: '_dragging'
		}).fix({
			elements: '.header__title',
			outside: 'header__title_state_fixed',
			before: 'header__title_position_top',
			after: 'header__title_position_bottom',
			clickable: true
		}).controls({
			track: '.baron__track',
			forward: '.baron__down',
			backward: '.baron__up'
		});
	});
</script>
<?
$html = ob_get_contents();
ob_end_clean();

\Bitrix\Main\Page\Asset::getInstance()->addString($html);
?>