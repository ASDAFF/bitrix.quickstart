<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>

<?

			
global $USER;
$user_id = $USER->GetId();
if(CModule::IncludeModule("iblock")){
	if(!$user_id){
		$is_user = 'N';
		$user = new CUser;
		$password = rand('100000','99999999');
		$login = "820eb5b".$password."@".rand('100000','99999999').".com";
		$arFields = Array(
		"EMAIL"				=> $login,	
		  "LOGIN"             => $login,
		  "LID"               => "ru",
		  "ACTIVE"            => "Y",
		  "GROUP_ID"          => array(2),
		  "PASSWORD"          => $password,
		  "CONFIRM_PASSWORD"  => $password
		);
		
		$user_id = $user->Add($arFields);
		if (intval($user_id) > 0)
			$USER->Authorize($user_id);
	}
	
	$bs = new CIBlockSection;
	$name = $_REQUEST['NAME'];
	if($name){
		$arParams = array("replace_space"=>"-","replace_other"=>"-");
		$trans = Cutil::translit($name,"ru",$arParams);
		
		$arFields = Array(
		  "ACTIVE" => "Y",
		  "IBLOCK_ID" => 2,
		  "NAME" => $_REQUEST['NAME'],
		  "CODE" => $trans.'-'.$user_id
		);	
		$ID = $bs->Add($arFields);
		
		$category = '({"cat":{"ID":"'.$ID.'","NAME":"'.$name.'","CODE":"'.$trans.'","USER":"'.$is_user.'"}})';
		
		$res = ($ID>0);
		if($res)
			echo $category; 
	}
}
?>