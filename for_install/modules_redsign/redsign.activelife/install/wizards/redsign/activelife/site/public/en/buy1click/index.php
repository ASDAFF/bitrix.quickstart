<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Buy 1 click");
?>
<?$APPLICATION->IncludeComponent(
    "rssline:forms",
    "form",
    array(
    "COMPONENT_TEMPLATE" => "form",
    "AJAX_OPTION_ADDITIONAL" => "",
    "AJAX_OPTION_HISTORY" => "N",
    "AJAX_OPTION_JUMP" => "N",
    "AJAX_OPTION_STYLE" => "N",
    "ALFA_EMAIL_TO" => "#SHOP_EMAIL#",
    "ALFA_EXT_FIELDS_COUNT" => "1",
    "ALFA_MESSAGE_AGREE" => "Thank you for your application is accepted!",
    "ALFA_USE_CAPTCHA" => "N",
    "CACHE_TIME" => "3600",
    "CACHE_TYPE" => "A",
    "DESCRIPTION_FOR_WEBFORM" => "",
    "EMAIL_TO" => "",
    "EVENT_TYPE" => "REDSIGN_BUY1CLICK",
    "FORM_DESCRIPTION" => "",
    "FORM_TITLE" => "Buy 1 click",
    "INPUT_NAME_RS_PERSONAL_SITE" => "Your website",
    "INPUT_NAME_RS_TEXTAREA" => "A comment",
    "MESSAGE_AGREE" => "Thank you for your application is accepted!",
    "REQUIRED_FIELDS" => array(
        0 => "RS_NAME",
        1 => "RS_PHONE",
    ),
    "RS_FLYAWAY_FIELD_0_NAME" => "Product",
    "SHOW_FIELDS" => array(
        0 => "RS_NAME",
        1 => "RS_PHONE",
        2 => "RS_TEXTAREA",
    ),
    "DISABLED_FIELDS" => array(
        0 => "RS_EXT_FIELD_0"
     ),
    "TITLE_FOR_WEBFORM" => "Buy 1 click",
    "USE_CAPTCHA" => "Y"
    ),
    false
);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>