<?
CModule::IncludeModule("bestrank.mono");

$shopFacebook = COption::GetOptionString("mono", "shopFacebook", "", SITE_ID);
$shopVk = COption::GetOptionString("mono", "shopVk", "", SITE_ID);
$shopTwitter = COption::GetOptionString("mono", "shopTwitter", "", SITE_ID);
?>

<?if(strlen($shopFacebook )>0){?>
	<a href="<?=$shopFacebook?>"><img src="/bitrix/templates/#TEMPLATE_NAME#/images/ss_fb.png" border="0" width="32" height="32"  /></a>
<?}?><?if(strlen($shopVk )>0){?>
<a href="<?=$shopVk?>"><img src="/bitrix/templates/#TEMPLATE_NAME#/images/ss_vk.png" border="0" width="32" height="32"  /></a>
<?}?><?if(strlen($shopTwitter )>0){?>
<a href="<?=$shopTwitter?>"><img src="/bitrix/templates/#TEMPLATE_NAME#/images/ss_twitter.png" border="0"  width="32" height="32"   /></a>
<?}?>



