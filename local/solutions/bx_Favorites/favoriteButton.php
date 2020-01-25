<?

use Bitrix\Main\Application;
use Bitrix\Main\Web\Cookie;
$application = Application::getInstance();
$context = $application->getContext(); 
 /* Вывод количества избранного */
    if(!$USER->IsAuthorized()) // Для неавторизованного
    {
        $arElements = unserialize($APPLICATION->get_cookie('favorites'));
        if($arElements == '')
		unset($arElements);

	foreach($arElements as $k=>$fav) // Checking empty IDs
	{
		if($fav == '0')
			 unset($arElements[$k]);
			unset($fav);
	}
        $wishCount = count($arElements);
    }
    else {
         $idUser = $USER->GetID();
         $rsUser = CUser::GetByID($idUser);
         $arUser = $rsUser->Fetch();
        foreach($arUser['UF_FAVORITES'] as $k=>$fav) // Checking empty IDs
	{
		if($fav == '0') 
		{
			unset($arUser['UF_FAVORITES'][$k]);
			unset($fav);
		}
	}
        $wishCount = count($arUser['UF_FAVORITES']);


     }

 /* Вывод количества избранного */
?>
<a id='want' class="block" href="/personal/wishlist/">
    <span class="col"><?=$wishCount?></span>
    <div class="icon"></div>
    <p>Хочу</p>
</a>
