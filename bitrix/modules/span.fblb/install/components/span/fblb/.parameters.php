<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
?>
<div align="center" style="background-color: #FFFFFF; height: 30px; padding: 7px; margin-bottom: 5px;">
        <a href="http://www.spanltd.ru"><img src="/bitrix/components/span/fblb/images/logo.jpg" height="30px" align="left" /></a>
        <div style="margin: 13px 0px 0px 0px">
        <a href="http://www.spanltd.ru" style="color: #000; font-size: 10px;"><?=GetMessage("SPAN_IS")?></a>
        </div>
</div>

<?
$arComponentParameters = array(
	"GROUPS" => array(
	),
	"PARAMETERS" => array(
	    "FB_DOMAIN"       => array(
                                    "PARENT" => "BASE",
                                    "NAME" => GetMessage("FB_DOMAIN"), 
                                    "DEFAULT" => ""
                                   ),
	    "FB_WIDTH"       => array(
                                    "PARENT" => "BASE",
                                    "NAME" => GetMessage("FB_WIDTH"), 
                                    "DEFAULT" => ""
                                   ),
	    "FB_HEIGHT"       => array(
                                    "PARENT" => "BASE",
                                    "NAME" => GetMessage("FB_HEIGHT"), 
                                    "DEFAULT" => ""
                                   ),
	    "FB_BORDER"       => array(
                                    "PARENT" => "BASE",
                                    "NAME" => GetMessage("FB_BORDER"), 
                                    "DEFAULT" => ""
                                   ),
	    ),
);
?>