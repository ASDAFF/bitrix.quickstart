<?
##########################################
#   Company developer: ALTASIB           #
#   Site: http://www.altasib.ru          #
#   E-mail: dev@altasib.ru               #
#   Copyright (c) 2006-2015 ALTASIB      #
##########################################

global $MESS, $APPLICATION;

IncludeModuleLangFile(__FILE__);

$arClassesList = array(
	"CAltasibping" => "general/ping.php"
);

if (method_exists(CModule, "AddAutoloadClasses")) {
	CModule::AddAutoloadClasses(
		"altasib.ping",
		$arClassesList
	);
} else {
	foreach ($arClassesList as $sClassName => $sClassFile) {
		require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/altasib.ping/".$sClassFile);
	}
}

IncludeModuleLangFile(__FILE__);

class CAltasibpingOther
{
	function AddStatPingButtontoPannel()
	{
		global $APPLICATION;

		$RIGHT = $APPLICATION->GetGroupRight("altasib.ping");
		if($RIGHT>="W"){
			$scheme = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ||
				(isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') ? "https" : "http");

			$arPostParams = array(
				"backurl" => CAltasibping::PrepareUrl($scheme."://".$_SERVER['SERVER_NAME'].$APPLICATION->GetCurPageParam("", array())),
				"pagename" => $APPLICATION->GetTitle(),
				"siteid" => SITE_ID
			);

			$APPLICATION->AddPanelButton(array(
				"HREF" => "javascript:(new BX.CDialog({
					'content_url':'/bitrix/admin/altasib_statpageping.php',
					'content_post':".CUtil::PhpToJsObject(($arPostParams)).",
					'width':'400',
					'height':'200',
					'min_width':'400',
					'min_height':'200'
				})).Show();
				BX.removeClass(this.parentNode.parentNode, 'bx-panel-button-icon-active');",
				"SRC"		=> "/bitrix/modules/altasib.ping/images/icon_panel.png", 
				"ALT"		=> GetMessage("PING_INCLUDE_ONPROLOG_TITLE"), 
				"MAIN_SORT"	=> 300, 
				"SORT"		=> 10,
				"TEXT"		=>"Ping",
			));
		}
	}
}
?>