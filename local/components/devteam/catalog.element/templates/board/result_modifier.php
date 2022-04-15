<?
$user_id = $arResult['DISPLAY_PROPERTIES']['USER_ID']['DISPLAY_VALUE'];
if ($user_id)
{
	$rsUSER = CUser::GetById($user_id);
	$f=$rsUSER->Fetch();
	$arResult['DISPLAY_PROPERTIES']['USER_ID']['DISPLAY_VALUE'] = $f['NAME'].' '.$f['LAST_NAME'];
}
?>
