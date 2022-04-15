<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

global $APPLICATION;

if(!$USER->IsAuthorized())
{
    $arFavorites = unserialize($APPLICATION->get_cookie("favorites"));
    //print_r($arFavorites);
}
else {
    $idUser = $USER->GetID();
    $rsUser = CUser::GetByID($idUser);
    $arUser = $rsUser->Fetch();
    $arFavorites = $arUser['UF_FAVORITES'];  // Достаём избранное пользователя
}
count($arFavorites);
/* Меняем отображение сердечка товара */
foreach($arFavorites as $k => $favoriteItem):?>
    <script>
        if($('a.favor[data-item="<?=$favoriteItem?>"]'))
            $('a.favor[data-item="<?=$favoriteItem?>"]').addClass('active');
    </script>
<?endforeach;?>