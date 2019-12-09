<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$frame = $this->createFrame()->begin("");

if(isset($arResult['REQUEST_ITEMS']))
{
	CJSCore::Init(array('ajax'));

	$injectId = 'bigdata_recommeded_products_'.rand();

	// component parameters
	$signer = new \Bitrix\Main\Security\Sign\Signer;
	$signedParameters = $signer->sign(
		base64_encode(serialize($arResult['_ORIGINAL_PARAMS'])),
		'bx.bd.products.recommendation'
	);
	$signedTemplate = $signer->sign($arResult['RCM_TEMPLATE'], 'bx.bd.products.recommendation');

	?><span id="<?=$injectId?>" class="bigdata_recommended_products_container"></span><?

	?><script type="application/javascript">

		BX.cookie_prefix = '<?=CUtil::JSEscape(COption::GetOptionString("main", "cookie_name", "BITRIX_SM"))?>';
		BX.cookie_domain = '<?=$APPLICATION->GetCookieDomain()?>';
		BX.current_server_time = '<?=time()?>';

		BX.ready(function(){

			var params = <?=CUtil::PhpToJSObject($arResult['RCM_PARAMS'])?>;
			var url = 'https://analytics.bitrix.info/crecoms/v1_0/recoms.php';
			var data = BX.ajax.prepareData(params);

			if(data) {
				url += (url.indexOf('?') !== -1 ? "&" : "?") + data;
				data = '';
			}

			var onready = function(response) {

				if (!response.items) {
					response.items = [];
				}

				var mainUrl = '/bitrix/components/bitrix/catalog.bigdata.products/ajax.php?'+BX.ajax.prepareData({'AJAX_ITEMS': response.items, 'RID': response.id});
				var subUrl = '<?=$templateFolder?>/ajax.php';

				if( $('#<?=$injectId?>').parents('.js-bigdata').length>0 ) {
					var $jsBigdata = $('#<?=$injectId?>').parents('.js-bigdata');
					$jsBigdata.data('parameters','<?=CUtil::JSEscape($signedParameters)?>');
					$jsBigdata.data('template','<?=CUtil::JSEscape($signedTemplate)?>');
					$jsBigdata.data('injectId','<?=CUtil::JSEscape($injectId)?>');
					$jsBigdata.data('url',subUrl);
				}

				BX.ajax({
					url: mainUrl,
					method: 'POST',
					data: {'parameters':'<?=CUtil::JSEscape($signedParameters)?>', 'template': '<?=CUtil::JSEscape($signedTemplate)?>', 'rcm': 'yes'},
					dataType: 'html',
					processData: false,
					start: true,
					onsuccess: function (html) {
						console.warn( 'bigData loaded' );
						var ob = BX.processHTML(html);
						// inject
						BX('<?=$injectId?>').innerHTML = ob.HTML;
						BX.ajax.processScripts(ob.SCRIPT);
						setTimeout(function(){
							if( $('.js-bigdata').find('.js-element').length>0 ) {
								$('.bigdata').show();
								RSGoPro_ScrollInit('.prices_jscrollpane');
								RSGoPro_TIMER();
							}
						},75); // for slow shit
					}
				});
			};

			BX.ajax({
				'method': 'GET',
				'dataType': 'json',
				'url': url,
				'timeout': 3,
				'onsuccess': onready,
				'onfailure': onready
			});
		});
	</script><?

	$frame->end();
	
} else {
	if (!empty($arResult['ITEMS'])) {
		
		?><input type="hidden" name="bigdata_recommendation_id" value="<?=htmlspecialcharsbx($arResult['RID'])?>"><?

		switch($arParams['VIEW']) {
			case 'showcase': //////////////////////////////////////// showcase ////////////////////////////////////////
				include($_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/components/bitrix/catalog.section/gopro/showcase.php');
				break;
			case 'gallery': //////////////////////////////////////// gallery ////////////////////////////////////////
				include($_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/components/bitrix/catalog.section/gopro/gallery.php');
				break;
			default: //////////////////////////////////////// table ////////////////////////////////////////
				include($_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/components/bitrix/catalog.section/gopro/table.php');
		}
	}

	$frame->end();
}