<?
######################################################
# Name: energosoft.twitter                           #
# File: template.php                                 #
# (c) 2005-2011 Energosoft, Maksimov M.A.            #
# Dual licensed under the MIT and GPL                #
# http://energo-soft.ru/                             #
# mailto:support@energo-soft.ru                      #
######################################################
?>
<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>

<?if($arParams["ES_TWITTER"]!=""):?>
	<a href="https://twitter.com/share" class="twitter-share-button" data-count="<?=$arParams["ES_DATA_COUNT"]?>" data-via="<?=$arParams["ES_TWITTER"]?>" data-lang="<?=$arParams["ES_TWEETBUTTON_LANGUAGE"]?>"<?=$arParams["ES_DATA_TEXT"]=="self"?" data-text=\"".$arParams["ES_DATA_TEXT_SELF"]."\"":""?><?=$arParams["ES_DATA_URL"]=="self"?" data-url=\"".$arParams["ES_DATA_URL_SELF"]."\"":""?><?=$arResult["DATA_RELATED"]!=""?" data-related=\"".$arResult["DATA_RELATED"]."\"":""?>></a>
<?endif;?>