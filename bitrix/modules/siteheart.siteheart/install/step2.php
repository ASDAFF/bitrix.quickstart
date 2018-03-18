<?

IncludeModuleLangFile(__FILE__);
	
try{

    if($_SERVER["REQUEST_METHOD"] == 'POST'){
	
	COption::SetOptionString("siteheart.siteheart", "widget_id", $_POST['widget_id']);
	COption::SetOptionString("siteheart.siteheart", "template", $_POST['template']);
	COption::SetOptionString("siteheart.siteheart", "side", $_POST['side']);
	COption::SetOptionString("siteheart.siteheart", "position", $_POST['position']);
	COption::SetOptionString("siteheart.siteheart", "title", $_POST['title']);
	COption::SetOptionString("siteheart.siteheart", "title_offline", $_POST['title_offline']);
	COption::SetOptionString("siteheart.siteheart", "inviteTimeout", $_POST['inviteTimeout']);
	COption::SetOptionString("siteheart.siteheart", "inviteCancelTimeout", $_POST['inviteCancelTimeout']);
	COption::SetOptionString("siteheart.siteheart", "inviteText", $_POST['inviteText']);
	COption::SetOptionString("siteheart.siteheart", "inviteImage", $_POST['inviteImage']);
	COption::SetOptionString("siteheart.siteheart", "text_layout", $_POST['text_layout']);
	COption::SetOptionString("siteheart.siteheart", "secret_key", $_POST['secret_key']);
	COption::SetOptionString("siteheart.siteheart", "devisions", $_POST['devisions']);
	COption::SetOptionString("siteheart.siteheart", "track", $_POST['track']);
	COption::SetOptionString("siteheart.siteheart", "hide", $_POST['hide']);
	COption::SetOptionString("siteheart.siteheart", "hide_offline", $_POST['hide_offline']);
	COption::SetOptionString("siteheart.siteheart", "offline_pay", $_POST['offline_pay']);

	RegisterModule("siteheart.siteheart");
    
	RegisterModuleDependences("main", "OnPageStart", "siteheart.siteheart", "siteheartClass", "addScriptTag", "0");
	
	echo CAdminMessage::ShowNote(GetMessage("SH_SUCCESS"));
	
    }
    
}catch(Exception $e){

    $errTxt =  $e->getMessage();

    echo CAdminMessage::ShowMessage(Array("TYPE"=>"ERROR", "MESSAGE" => GetMessage("SH_ERROR"), "DETAILS"=>$errTxt, "HTML"=>false));

    $APPLICATION->IncludeAdminFile(
	    
	'Step 1', 
	$_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/siteheart.siteheart/install/step1.php"
	    
    );

}


?>
	
