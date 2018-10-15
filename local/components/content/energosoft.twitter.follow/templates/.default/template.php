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
	<a href="https://twitter.com/<?=$arParams["ES_TWITTER"]?>" class="twitter-follow-button" data-show-count="<?=$arParams["ES_DATA_SHOW_COUNT"]?>" data-button="<?=$arParams["ES_DATA_BUTTON"]?>" data-text-color="<?=$arParams["ES_DATA_TEXT_COLOR"]?>" data-link-color="<?=$arParams["ES_DATA_LINK_COLOR"]?>" data-width="<?=$arParams["ES_DATA_WIDTH"]?>" data-align="<?=$arParams["ES_DATA_ALIGN"]?>" data-lang="<?=$arParams["ES_DATA_LANG"]?>"></a>
<?endif;?>