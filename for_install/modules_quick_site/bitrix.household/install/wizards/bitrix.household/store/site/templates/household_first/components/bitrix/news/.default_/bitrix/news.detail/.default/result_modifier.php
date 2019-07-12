<?
print_r($arProperty);
$arProperty = &$arResult['DISPLAY_PROPERTIES'][$arParams['PROPERTY_VIDEO']];
print_r($arProperty);

if ($arProperty['DISPLAY_VALUE']) // проверим, установлено ли свойство
{
   global $APPLICATION;
   ob_start(); // включим буферизацию чтобы отловить вывод компонента
   $APPLICATION->IncludeComponent(
      "bitrix:player",
      "",
      Array(
         "PLAYER_TYPE" => "auto", 
         "USE_PLAYLIST" => "N", 
         "PATH" => CFile::GetPath($arProperty["VALUE"]),
         "WIDTH" => "400", 
         "HEIGHT" => "300", 
         "FULLSCREEN" => "Y", 
         "SKIN_PATH" => "/bitrix/components/bitrix/player/mediaplayer/skins", 
         "SKIN" => "bitrix.swf", 
         "CONTROLBAR" => "bottom", 
         "WMODE" => "transparent", 
         "HIDE_MENU" => "N", 
         "SHOW_CONTROLS" => "Y", 
         "SHOW_STOP" => "N", 
         "SHOW_DIGITS" => "Y", 
         "CONTROLS_BGCOLOR" => "FFFFFF", 
         "CONTROLS_COLOR" => "000000", 
         "CONTROLS_OVER_COLOR" => "000000", 
         "SCREEN_COLOR" => "000000", 
         "AUTOSTART" => "N", 
         "REPEAT" => "N", 
         "VOLUME" => "90", 
         "DISPLAY_CLICK" => "play", 
         "MUTE" => "N", 
         "HIGH_QUALITY" => "Y", 
         "ADVANCED_MODE_SETTINGS" => "N", 
         "BUFFER_LENGTH" => "10", 
         "DOWNLOAD_LINK_TARGET" => "_self" 
      )
   ); 
   $arProperty['DISPLAY_VALUE'] = ob_get_contents(); // подменим $arResult
   ob_clean(); // очистим наш буфер чтобы плеер не появился дважды
   ob_end_clean(); // закроем буфер
}
?>
