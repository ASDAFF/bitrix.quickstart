<?php
/**
 * Created by PhpStorm.
 * User: anton
 * Date: 14.01.14
 * Time: 17:03
 */

$ImportMessages = function()
{
    include dirname(__FILE__) . '/import.class.php';
    $types = new ImportMessagesType();
    $types->importList();

    $messages = new ImportMessages();
    $messages->importList();
};
$ImportMessages();

//установка настроек социальных сетей
$socServices = array(
    "Facebook" => "N",
    "MyMailRu" => "N",
    "OpenID" => "Y",
    "YandexOpenID" => "Y",
    "MailRuOpenID" => "Y",
    "Livejournal" => "Y",
    "Liveinternet" => "Y",
    "Blogger" => "Y",
    "Twitter" => "N",
    "VKontakte" => "N",
    "GoogleOAuth" => "N",
    "LiveIDOAuth" => "N",
    "Odnoklassniki" => "N"
);
COption::SetOptionString("socialservices", "auth_services_bx_site_".WIZARD_SITE_ID, serialize($socServices));

$use_on_sites = COption::GetOptionString("socialservices","use_on_sites");
$use_on_sites = unserialize($use_on_sites);
$use_on_sites[WIZARD_SITE_ID] = "Y";
$use_on_sites = serialize($use_on_sites);
COption::SetOptionString("socialservices","use_on_sites",$use_on_sites);