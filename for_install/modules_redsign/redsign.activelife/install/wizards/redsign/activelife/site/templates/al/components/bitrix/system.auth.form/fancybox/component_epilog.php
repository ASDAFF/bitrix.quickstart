<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use \Bitrix\Main\Application;

$request = Application::getInstance()->getContext()->getRequest();

$bIsAjax = (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) ||
    $request->get('rs_ajax') == 'Y' ||
    $request->get('rs_ajax__page') == 'Y'
);

if ($bIsAjax) {
	//$APPLICATION->RestartBuffer();
	?><div style="display:none"><?
		?><link rel="stylesheet" type="text/css" href="/bitrix/js/socialservices/css/ss.css" /><? // TODO _loadCSS
		?><style>.fancybox-inner .bx-auth-services div{width: auto;}.fancybox-inner .bx-auth-line, .fancybox-inner .bx-auth-title{border: none;padding: 0;}</style><?
		?><script>
		if (window.jQuery) {
			$.ajax({url:'/bitrix/js/socialservices/ss.js',type:'GET',dataType:'script',cache:true});
		}
		</script><?
	?></div><?
	//echo $templateData['TEMPLATE_HTML'];
	//die();
}