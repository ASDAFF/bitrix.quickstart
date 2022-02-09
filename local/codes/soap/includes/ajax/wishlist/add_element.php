<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>

<?
if(CModule::IncludeModule("iblock")){
	$IBLOCK_ID = 2;
	$bs = new CIBlockSection;
	$id = $_REQUEST['element'];
	$name = $_REQUEST['name'];
	if(!$name){
		$cat_id = $_REQUEST['cat'];
	}
	pr($_REQUEST);
	pr($cat_id);
	global $USER;
	$user_id = $USER->GetId();
	
	if($id){
		if(!$user_id){
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
		
		if($name){
			$arParams = array("replace_space"=>"-","replace_other"=>"-");
			$trans = Cutil::translit($name,"ru",$arParams);
			
			$arFilter = Array('IBLOCK_ID'=>$IBLOCK_ID, 'CODE'=>$trans, 'CREATED_BY'=>$user_id);
			$db_list = CIBlockSection::GetList(Array($by=>$order), $arFilter, true);
			pr('asdasdasd');
			if($ar_result = $db_list->GetNext()){
				//$cat_is = 'Y';
				
				$cat_id = $ar_result['ID'];
			} else {
				$arFields = Array(
				  "ACTIVE" => "Y",
				  "IBLOCK_ID" => $IBLOCK_ID,
				  "NAME" => $name,
				  "CODE" => $trans
				);
				$cat_id = $bs->Add($arFields);	
			}
		}
		$el = new CIBlockElement;
		$PROP = array();
		$PROP[10] = $id;
		$arLoadProductArray = Array(
		  "MODIFIED_BY"    => $USER->GetID(), // элемент изменен текущим пользователем
		  "IBLOCK_SECTION_ID" => $cat_id,          // элемент лежит в корне раздела
		  "IBLOCK_ID"      => $IBLOCK_ID,
		  "PROPERTY_VALUES"=> $PROP,
		  "NAME"           => rand(15).date('d-y-m'),
		  "ACTIVE"         => "Y",            // активен
		  );
		if($PRODUCT_ID = $el->Add($arLoadProductArray))
		  echo $cat_id;
	}
}
?>