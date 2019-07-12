<?include($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/prolog_before.php");
$template = '/bitrix/templates/iarga.shopplus100.main';
include($_SERVER['DOCUMENT_ROOT'].$template."/inc/functions.php");
IncludeTemplateLangFile($template.'/header.php');
CModule::IncludeModule("iblock");

if(isset($_POST['add'])){
	$good = GetIBlockElement($_POST['add']);
	if($_POST['act'] > 0){		
		$_SESSION['CATALOG_COMPARE_LIST'][$good['IBLOCK_ID']]['ITEMS'][$good['ID']] = $good;
	}else{print 'unlink '.$good['IBLOCK_ID'].' '.$good['ID'];
		unset($_SESSION['CATALOG_COMPARE_LIST'][$good['IBLOCK_ID']]['ITEMS'][$good['ID']]);
	}
}
$n = each($_SESSION['CATALOG_COMPARE_LIST']);
$num = sizeof($_SESSION['CATALOG_COMPARE_LIST'][$n[0]]['ITEMS']);
if($num == 0) unset($_SESSION['CATALOG_COMPARE_LIST'][$n[0]]);
//print_r($_SESSION['CATALOG_COMPARE_LIST']);
?>
<p><span><span class="icon"><img src="<?=$templateFolder.$template?>/images/icon-info-favorites.png" alt=""></span><?=GetMessage("IN_FAV")?> <strong><a class="innerlink" href="/favorite/"><?=$num?></a></strong>&nbsp;<?=GetMessage(iarga::sklon($num,"GOODS","GOOD","2GOODS"))?></span> <a class="bt_gray" href="/favorite/"><?=GetMessage("VIEW")?></a></p>

<?foreach($_SESSION['CATALOG_COMPARE_LIST'][$n[0]]['ITEMS'] as $good) if($good):
	?>
	<input type="hidden" name="<?=$good['ID']?>" >
<?endif;?>
<input type="hidden" class="to_fav_lang" value="<?=GetMessage("TO_FAV")?>">
<input type="hidden" class="in_fav_lang" value="<?=GetMessage("IN_FAV")?>">