<?php

return ;

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

use Lema\Seo\OpenGraph,
    Lema\Common\Request,
    Lema\Common\Helper;

//OpenGraph

//show opengrath view content (in header)
OpenGraph::get()->show();
//set image to opengrath (from detail page)
OpenGraph::get()->setImage($arResult['DETAIL_PICTURE']['SRC']);
//set open graph values (in footer)
$og = new OpenGraph();
$og->setItems(array(
    'locale' => strtolower(LANGUAGE_ID) . '_' . strtoupper(LANGUAGE_ID),
    'type' => 'website',
    'title' => $APPLICATION->GetPageProperty('title'),
    'title2' => $APPLICATION->GetTitle(false),
    'description' => $APPLICATION->GetPageProperty('description'),
    'url' => Request::get()->getRequestedPageDirectory() . '/',
    'image' => $og->getImage(SITE_TEMPLATE_PATH . '/img/logo.png'),
    'site_name' => 'Аква-Вайт - Производство минеральной воды и лимонадов'
))->setViewContent();

//GTM

//set gtm.formSubmit
GTMFormSubmit::get()->setEvent()->setElementClasses('.contact_form')->setElements(array($name, $comment))->jsonResult();
//or
GTMFormSubmit::get()->setEvent()->setElementId('formId')->setElements(array($name, $comment))->jsonResult();
//or return in json (for ajax)
return Helper::getJson(array(
    'gtmObject' => GTMFormSubmit::get()->setEvent()->setElementId('formId')->setElements(array($name, $email, $phone, $text))->getResult(),
    //...
));


//ECommerce


//show ecommerce view content (in header)
ECommerce::get()->show();
//set ecommerce values (from confirm order page)
ECommerce::get()->setIblockId(2)->loadByAccNumber($arResult['ORDER']['ACCOUNT_NUMBER'])->setViewContent();
//or
ECommerce::get()->setIblockId(2)->load($arResult['ORDER']['ID'])->setViewContent();
