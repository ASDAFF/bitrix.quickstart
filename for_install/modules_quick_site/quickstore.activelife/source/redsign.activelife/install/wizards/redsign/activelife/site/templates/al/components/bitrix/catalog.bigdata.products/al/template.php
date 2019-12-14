<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */
/** @global CDatabase $DB */
/** @global CMain $APPLICATION */

use Bitrix\Main\Application;

$frame = $this->createFrame()->begin("");

$injectId = 'bigdata_recommeded_products_'.rand();

$request = Application::getInstance()->getContext()->getRequest();

$injectId = $arParams['UNIQ_COMPONENT_ID'];

if ($request->get('rs_ajax') == 'Y' && $request->get('ajax_id') == $arParams['TEMPLATE_AJAXID']) {
    ob_start();
}

if (isset($arResult['REQUEST_ITEMS']))
{
	// code to receive recommendations from the cloud
	CJSCore::Init(array('ajax'));

	// component parameters
	$signer = new \Bitrix\Main\Security\Sign\Signer;
	$signedParameters = $signer->sign(
		base64_encode(serialize($arResult['_ORIGINAL_PARAMS'])),
		'bx.bd.products.recommendation'
	);
	$signedTemplate = $signer->sign($arResult['RCM_TEMPLATE'], 'bx.bd.products.recommendation');

	?>

	<span id="<?=$injectId?>"></span>
	<script type="text/javascript">
    <?php if ($request->get('rs_ajax') == 'Y' && $request->get('ajax_id') == $arParams['TEMPLATE_AJAXID']): ?>
        if (window.jQuery) {
            $.ajax({
                url:'<?=$templateFolder?>/script.js?05092016',
                type:'GET',
                dataType:'script',
                cache:true,
                success: function(){
                    bx_rcm_get_from_cloud(
                        '<?=CUtil::JSEscape($injectId)?>',
                        <?=CUtil::PhpToJSObject($arResult['RCM_PARAMS'])?>,
                        {
                            'parameters':'<?=CUtil::JSEscape($signedParameters)?>',
                            'template': '<?=CUtil::JSEscape($signedTemplate)?>',
                            'site_id': '<?=CUtil::JSEscape(SITE_ID)?>',
                            'rcm': 'yes'
                        }
                    );
                }

            });
        }
    <?php else: ?>
		BX.ready(function(){
			bx_rcm_get_from_cloud(
				'<?=CUtil::JSEscape($injectId)?>',
				<?=CUtil::PhpToJSObject($arResult['RCM_PARAMS'])?>,
				{
					'parameters':'<?=CUtil::JSEscape($signedParameters)?>',
					'template': '<?=CUtil::JSEscape($signedTemplate)?>',
					'site_id': '<?=CUtil::JSEscape(SITE_ID)?>',
					'rcm': 'yes'
				}
			);
		});
    <?php endif; ?>
	</script>
	<?
	$frame->end();

    if ($request->get('rs_ajax') == 'Y' && $request->get('ajax_id') == $arParams['TEMPLATE_AJAXID']) {
        $arJson = array(
            $arParams['TEMPLATE_AJAXID'] => ob_get_flush()
        );

        $APPLICATION->restartBuffer();
        echo CUtil::PhpToJSObject($arJson, false, false, true);
        die();
    }

	return;

	// \ end of the code to receive recommendations from the cloud
}

// regular template then
// if customized template, for better js performance don't forget to frame content with <span id="{$injectId}_items">...</span>
if (!empty($arResult['ITEMS']))
{
	?>
	<div id="<?=$injectId?>_items">
        <?php
        $sTemplateExtPath = $_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/template_ext/catalog.section/al/template.php';
        if (file_exists($sTemplateExtPath)) {
            include($sTemplateExtPath);    
        }
        ?>
	</div>
<?

}

$frame->end();