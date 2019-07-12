<!DOCTYPE html>
<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<?
$module_id = "webmechanic.landing";
CModule::IncludeModule($module_id);
IncludeTemplateLangFile(__FILE__);

$phone = COption::GetOptionString($module_id, "WEBMECHANIC_CREDIT_PHONE");
$index = stripos($phone, ")");

if ($index == FALSE) {
    
    $phone = str_replace("  ", " ", $phone);
    $phone = preg_replace("/^(([^\ ]+)\ ([^\ ]+))/", "<span>$1</span> ", $phone);
    
} else {
    $str = substr($phone, 0, $index+1);
    $phone = str_replace($str, "<span>".$str."</span>", $phone);
}
?>

<!--[if IE 8]>         <html class="ie8"> <![endif]-->
<!--[if IE 9]>         <html class="ie9 gt-ie8"> <![endif]-->
<!--[if gt IE 9]><!--> <html class="gt-ie8 gt-ie9 not-ie"> <!--<![endif]-->
<head>
    <? $APPLICATION->ShowHead() ?>
    <title><? $APPLICATION->ShowTitle() ?></title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
    <?
        $APPLICATION->IncludeComponent("webmechanic:mobile.detect", ".default", array(), false);
        $detect = new Mobile_Detect();
        $is_mobile = $detect->isMobile();

        $path = SITE_TEMPLATE_PATH . '/js/';
        $pathCss = SITE_TEMPLATE_PATH . '/css/';

        $APPLICATION->AddHeadScript($path . "jquery.min.js");
        $APPLICATION->AddHeadScript($path . "bootstrap.min.js");
        $APPLICATION->AddHeadScript($path . "jquery-validate.min.js");
        $APPLICATION->AddHeadScript($path . "jquery.maskedinput131.min.js");
        $APPLICATION->AddHeadScript($path . "jquery.nouislider.min.js");
        $APPLICATION->AddHeadScript($path . "jquery.numberMask.js");
        $APPLICATION->AddHeadScript($path . "jquery.ui.js");
        $APPLICATION->AddHeadScript($path . "select2.js");
        //$APPLICATION->AddHeadScript($path . "select2_locale_ru.js");
        $APPLICATION->AddHeadScript($path . "prettyCheckable.min.js");
        $APPLICATION->AddHeadScript($path . "accounting.min.js");
        $APPLICATION->AddHeadScript($path . "odometer.js");
        

        $APPLICATION->SetAdditionalCSS($pathCss . "bootstrap.css");
        $APPLICATION->SetAdditionalCSS($pathCss . "bootstrap-theme.css");
        $APPLICATION->SetAdditionalCSS($pathCss . "select2.css");
        $APPLICATION->SetAdditionalCSS($pathCss . "select2-bootstrap.css");
        $APPLICATION->SetAdditionalCSS($pathCss . "jquery.nouislider.min.css");
        $APPLICATION->SetAdditionalCSS($pathCss . "prettyCheckable.css");
        $APPLICATION->SetAdditionalCSS($pathCss . "odometer.css");
        $APPLICATION->SetAdditionalCSS($pathCss . "../theme.css");
        //var_dump($pathCss . "../theme.css");exit();

    ?>
    <!--[if lt IE 9]>
        <script type="text/javascript" src="<?=$path?>html5shiv.js"></script>
    <![endif]-->
    <!--[if lt IE 9]>
        <script type="text/javascript" src="<?=$path?>respond.min.js"></script>
    <![endif]-->


    <script type="text/javascript">
        var wm = {
            percent: <?=COption::GetOptionString($module_id, "WEBMECHANIC_CREDIT_PERCENT"); ?>,
            fpay: <?=COption::GetOptionString($module_id, "WEBMECHANIC_CREDIT_FPAY"); ?>,
            min_month: <?=COption::GetOptionString($module_id, "WEBMECHANIC_CREDIT_MIN_MONTH"); ?>,
            max_month: <?=COption::GetOptionString($module_id, "WEBMECHANIC_CREDIT_MAX_MONTH"); ?>,
            start_month: <?=COption::GetOptionString($module_id, "WEBMECHANIC_CREDIT_START"); ?>,
            phone_mask: '<?=COption::GetOptionString($module_id, "WEBMECHANIC_CREDIT_PHONE_CODE"); ?>',
        };

        (function ($) {
            "use strict";

            $.fn.select2.locales['ru'] = {
                formatNoMatches: function () { return "<?=GetMessage('webmechanic_landing_select2_notfound')?>"; },
                formatInputTooShort: function (input, min) { return "<?=GetMessage('webmechanic_landing_select2_tooshort')?>" + character(min - input.length); },
                formatInputTooLong: function (input, max) { return "<?=GetMessage('webmechanic_landing_select2_toolong')?>" + character(input.length - max) + " <?=GetMessage('webmechanic_landing_select2_less')?>"; },
                formatSelectionTooBig: function (limit) { return "<?=GetMessage('webmechanic_landing_select2_toobig')?> " + limit + " <?=GetMessage('webmechanic_landing_select2_element')?>" + (limit%10 == 1 && limit%100 != 11 ? "<?=GetMessage('webmechanic_landing_select2_a')?>" : "<?=GetMessage('webmechanic_landing_select2_ov')?>"); },
                formatLoadMore: function (pageNumber) { return "<?=GetMessage('webmechanic_landing_select2_loading')?>"; },
                formatSearching: function () { return "<?=GetMessage('webmechanic_landing_select2_search')?>"; }
            };

            $.extend($.fn.select2.defaults, $.fn.select2.locales['ru']);

            function character (n) {
                return " " + n + " <?=GetMessage('webmechanic_landing_select2_symbol')?>" + (n%10 < 5 && n%10 > 0 && (n%100 < 5 || n%100 > 20) ? n%10 > 1 ? "<?=GetMessage('webmechanic_landing_select2_a')?>" : "" : "<?=GetMessage('webmechanic_landing_select2_ov')?>");
            }
        })(jQuery);

    </script>
</head>
<body class="<?=($is_mobile) ? 'mobile' : '' ?>">
    <div id="panel">
        <? $APPLICATION->ShowPanel(); ?>
    </div>

    <div class="container">
        <!--top-->
        <div class="row header">
            <div class="col-xs-6 col-sm-4 col-sm-height col-middle">
                <a href="/" class="logo">
                    <img src="<?=COption::GetOptionString($module_id, "WEBMECHANIC_CREDIT_MARK_LOGO"); ?>" alt="" class="img-responsive"/>
                </a>
            </div>

            <div class="col-xs-6 col-sm-8 col-sm-height col-middle header-info">
                
                <div class="phone pull-right text-center">
                    <div><?=$phone?></div>
                    <p class="hidden-xs"><?=COption::GetOptionString($module_id, "WEBMECHANIC_CREDIT_ADDRESS");?></p>
                </div>
                
            </div>

            <div class="col-xs-12 visible-xs-block">
                <p class="text-right"><?=COption::GetOptionString($module_id, "WEBMECHANIC_CREDIT_ADDRESS");?></p>
            </div>

        </div>