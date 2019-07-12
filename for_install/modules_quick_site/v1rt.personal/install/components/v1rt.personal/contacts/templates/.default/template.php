<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(count($arResult))
{
    echo '<p>';
    if($arResult["PHONE"] != "")
        echo '<strong>'.GetMessage("V1RT_PERSONAL_CONTACT_PHONE").':</strong> '.$arResult["PHONE"].'<br/>';
    if($arResult["EMAIL"] != "")
    {
        $arResult["EMAIL"] = explode("@", $arResult["EMAIL"]);
        echo '<strong>E-mail:</strong> '.v1rtPersonalEmail($arResult["EMAIL"][0], $arResult["EMAIL"][1]).'<br/>';
    }
    if($arResult["VK"] != "")
        echo '<strong>'.GetMessage("V1RT_PERSONAL_CONTACT_VK").':</strong> <a href="'.$arResult["VK"].'" target="_blank">'.$arResult["VK"].'</a><br/>';
    if($arResult["FACEBOOK"] != "")
        echo '<strong>Facebook:</strong> <a href="'.$arResult["FACEBOOK"].'" target="_blank">'.$arResult["FACEBOOK"].'</a><br/>';
    if($arResult["TWITTER"] != "")
        echo '<strong>Twitter:</strong> @'.$arResult["TWITTER"].'<br/>';
    echo '</p>';
}

?>