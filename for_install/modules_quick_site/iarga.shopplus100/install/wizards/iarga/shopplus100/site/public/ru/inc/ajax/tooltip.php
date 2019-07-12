<?include($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/prolog_before.php");
$template = '/bitrix/templates/iarga.shopplus100.main';
include($_SERVER['DOCUMENT_ROOT'].$template."/inc/functions.php");
IncludeTemplateLangFile($template.'/header.php');
CModule::IncludeModule("iblock");
if(trim($_POST['q'])=='') die();
if(BX_UTF!='Y') $_POST['q'] = iconv("utf-8","windows-1251",$_POST['q']);

// First find sections
$sectFilter = Array("IBLOCK_ID"=>$_POST['IBLOCK_ID'],"NAME"=>'%'.$_POST['q'].'%');
$list = CIBlockSection::GetList(Array("DEPTH_LEVEL"=>"ASC","NAME"=>"ASC"),$sectFilter,false,false,Array("nPageSize"=>3));
while($sect = $list->GetNext()):
?>
	<a href='<?=$sect['SECTION_PAGE_URL']?>' class="section"><?=$sect['NAME']?> <em>(<?=GetMessage('SECTION')?>)</em></a>
<?endwhile;?>
<?
// Then find elements
$elFilter = Array("IBLOCK_ID"=>$_POST['IBLOCK_ID'],0=>Array("LOGIC"=>"OR","%NAME"=>$_POST['q'],"%KEYWORDS"=>$_POST['q']));
if($_POST['SECTION_ID']>0){
	$elFilter['INCLUDE_SUBSECTIONS'] = 'Y';
	$elFilter['SECTION_ID'] = $_POST['SECTION_ID'];
}
$list = CIBlockElement::GetList(Array("NAME"=>"ASC"),$elFilter,false,Array("nPageSize"=>11));
while($el = $list->GetNext()):?>
	<a href='#1' ><?=$el['NAME']?></a>
<?endwhile;?>
