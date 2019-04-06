<?
global $DBType;
IncludeModuleLangFile(__FILE__);

$arClasses = array(
	"DSocialMediaPoster" => "classes/general/socialmediaposter.php",
	"DSocialMediaPosterShedule" => "classes/general/socialmediaposter_shedule.php",
	"DSocialMediaPosterAJAX" => "classes/general/socialmediaposter_ajax.php",
	"DSocialMediaPosterEvent" => "classes/general/socialmediaposter_event.php",
	"DSocialMediaPosterCIBlockEvent" => "classes/general/socialmediaposter_event.php",
	"DSocialMediaPosterEventLog" => "classes/general/socialmediaposter_log.php",
	"DSocialMediaPosterCIBlockProperty" => "classes/general/socialmediaposter_property.php",
);

if (!class_exists('idna_convert')) {
	$arClasses["idna_convert"] = "classes/general/idna_convert.class.php";
}

CModule::AddAutoloadClasses("defa.socialmediaposter", $arClasses);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/defa.socialmediaposter/classes/general/post.tools.php");

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/defa.socialmediaposter/classes/general/post.params.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/defa.socialmediaposter/classes/general/post.connection.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/defa.socialmediaposter/classes/general/post.connection.curl.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/defa.socialmediaposter/classes/general/post.entity.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/defa.socialmediaposter/classes/general/post.entity.settings.php");

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/defa.socialmediaposter/classes/general/post.entity.one.facebook.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/defa.socialmediaposter/classes/general/post.entity.one.vkontakte.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/defa.socialmediaposter/classes/general/post.entity.one.odnoklassniki.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/defa.socialmediaposter/classes/general/post.entity.one.twitter.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/defa.socialmediaposter/classes/general/post.entity.one.livejournal.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/defa.socialmediaposter/classes/general/post.entity.one.googleplus.php");

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/defa.socialmediaposter/classes/general/post.factory.php");

CJSCore::RegisterExt("defa_smp", array(
	'js' => "/bitrix/js/defa.socialmediaposter/smp_iblock_element.js",
	'css' => "/bitrix/js/defa.socialmediaposter/css/smp_iblock_element.css",
	'lang' => "/bitrix/modules/defa.socialmediaposter/lang/" . LANGUAGE_ID . "/js/smp_iblock_element_js.php",
	'rel' => array('ajax')
));
