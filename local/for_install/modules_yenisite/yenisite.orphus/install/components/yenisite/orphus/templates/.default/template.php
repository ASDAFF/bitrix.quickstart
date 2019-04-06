<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$APPLICATION->AddHeadString('<script>var orphus_email="'.$arParams["EMAIL"].'";</script>',true);
?>
<script type="text/javascript" src=<?=$this->__folder."/orphus/orphus.js"?>></script>
<a href="http://orphus.ru" id="orphus" target="_blank"><img alt="<?=GetMessage("YENISITE_ORPHUS_SISTEMA")?> Orphus" src=<?=$this->__folder."/orphus/orphus.gif"?> border="0" width="88" height="31" /></a>

	
