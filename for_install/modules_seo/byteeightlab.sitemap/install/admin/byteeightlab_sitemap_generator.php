<?
$module_id = "byteeightlab.sitemap";
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

IncludeModuleLangFile(__FILE__);
CModule::IncludeModule($module_id);
$Sitemap = new Sitemap;

$STAT_RIGHT = $APPLICATION->GetGroupRight($module_id);
if($STAT_RIGHT=="D") $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

if($REQUEST_METHOD=="GET" && $USER->CanDoOperation('byteeightlab_sitemap_edit_all_settings') && $generate=='Y'){
	$Sitemap->Generate();
	LocalRedirect("byteeightlab_sitemap_generator.php?lang=".LANG."&generate_ok=Y");
}

$APPLICATION->SetTitle(GetMessage("PAGE_TITLE"));

$sTableID="tbl_byteeightlab_sitemap_file";

InitFilterEx($arSettings, $sTableID."_settings", "set");

$oSort = new CAdminSorting($sTableID);
$lAdmin = new CAdminList($sTableID, $oSort);

$lAdmin->bMultipart = true;

$arHeaders = Array();

$arHeaders[] = array("id"=>"FILENAME","content"=>GetMessage("FIELD_FILENAME"),"default"=>true);
$arHeaders[] = array("id"=>"INFO", "content"=>GetMessage("FIELD_INFO"), "default"=>true);
$arHeaders[] = array("id"=>"DATE", "content"=>GetMessage("FIELD_DATE"), "default"=>true);
$arHeaders[] = array("id"=>"SIZE", "content"=>GetMessage("FIELD_SIZE"), "default"=>true);

$lAdmin->AddHeaders($arHeaders);

$arSelectedFields = $lAdmin->GetVisibleHeaderColumns();

$URL = COption::GetOptionString($module_id,'URL','http://'.$_SERVER['HTTP_HOST']);
$NAME = COption::GetOptionString($module_id,'NAME','sitemap');
$PATH = COption::GetOptionString($module_id,'PATH','/');
$INDEX = (COption::GetOptionString($module_id,'INDEX','Y')=='Y')?true:false;
$POSTFIX = COption::GetOptionString($module_id,'POSTFIX','_');

if($REQUEST_METHOD=="GET" && $action=="delete"){
	$fname = $_SERVER["DOCUMENT_ROOT"].$PATH.$_REQUEST['file'];
	if(file_exists($fname)) unlink($fname);
	LocalRedirect("byteeightlab_sitemap_generator.php?lang=".LANG);
}

$files = scandir($_SERVER["DOCUMENT_ROOT"].$PATH);

foreach($files as $i=>$file){
	if(is_file($_SERVER["DOCUMENT_ROOT"].$PATH.$file)&&strtolower(end(explode(".", $file)))=="xml"){
		
		$size = filesize($_SERVER["DOCUMENT_ROOT"].$PATH.$file);
		if($size>1024){ 
			$size = number_format($size/1024,2,'.','');
			if($size>1024){
				$size = number_format($size/1024,2,'.','');
				if($size>1024) $size = number_format($size/1024,2,'.','');
				else $size .= " MB";	
			}else $size .= " KB";
		}else $size .= " B";
		
		$info = reset(explode(".",$file));
		if($INDEX){
			if($info==$NAME) $info = GetMessage("FIELD_INFO_TYPE1");
			else{
				$info = reset(explode($POSTFIX,$file));
				if($info==$NAME) $info = GetMessage("FIELD_INFO_TYPE2");
				else $info = GetMessage("FIELD_INFO_TYPE3");
			}
		}else{
			if($info==$NAME) $info = GetMessage("FIELD_INFO_TYPE2");
			else $info = GetMessage("FIELD_INFO_TYPE3");
		}
		
		$row = $lAdmin->AddRow($i,array(
			"FILENAME"=>$URL.$PATH.$file,
			"INFO"=>$info,
			"DATE"=>FormatDate("d.m.Y H:i:s",filectime($_SERVER["DOCUMENT_ROOT"].$PATH.$file)),
			"SIZE"=>$size
		),'http://dev.1c-bitrix.ru/','123123123');
		$row->AddViewField("FILENAME", '<a href="'.$URL.$PATH.$file.'" target="_blank" title="'.$URL.$PATH.$file.'">'.$URL.$PATH.$file.'</a>');

		$arActions = array();
		$arActions[] = array(
			"ICON" => "delete",
			"TEXT" => GetMessage('MAIN_DELETE'),
			"TITLE" => GetMessage("IBLOCK_DELETE_ALT"),
			"ACTION" => "if(confirm('".GetMessageJS('IBLOCK_CONFIRM_DEL_MESSAGE')."')) location.href = '?lang=".LANG."&action=delete&file=".$file."';",
		);
		$row->AddActions($arActions);
	}
}

$aContext = array();
$aContext[] = array(
	"TEXT" => GetMessage("BUT_GENERATE"),
	"ICON" => "bel_but_green",
	"LINK" => "?lang=".LANG."&generate=Y",
);
$aContext[] = array(
	"TEXT" => GetMessage("BUT_SETTING"),
	"ICON" => "",
	"LINK" => "/bitrix/admin/settings.php?lang=".LANG."&mid=".$module_id."&mid_menu=1",
);

$lAdmin->AddAdminContextMenu($aContext);


?>

<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");?>

<?if($REQUEST_METHOD=="GET" && $USER->CanDoOperation('byteeightlab_sitemap_edit_all_settings') && $generate_ok=='Y'){
	echo CAdminMessage::ShowNote(GetMessage("GENERATE_OK"));
}?>

<?if($message) echo $message->Show();?>

<?$lAdmin->DisplayList();?>

<div class="adm-info-message">
	<h3 style="margin-top: 0;"><?=GetMessage("CRON_TITLE")?></h3>
	<?=GetMessage("CRON_TEXT1")?> <a href="<?=$URL?>/bel_sitemap_generator.php" target="_blank"><b><?=$URL?>/bel_sitemap_generator.php</b></a>, <?=GetMessage("CRON_TEXT2")?>
</div>

<?require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>