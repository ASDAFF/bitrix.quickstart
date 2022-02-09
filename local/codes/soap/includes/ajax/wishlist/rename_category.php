<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>

<?

			
global $USER;
$user_id = $USER->GetId();
$cat_id = intval($_REQUEST['cat']);
$name = $_REQUEST['name'];


if(CModule::IncludeModule("iblock")&&$user_id&&$cat_id&&$name){

	$res = CIBlockSection::GetByID($cat_id);
	$ar_res = $res->GetNext();
	if($ar_res['CREATED_BY']==$user_id){

		$arParams = array("replace_space"=>"-","replace_other"=>"-");
		$trans = Cutil::translit($name,"ru",$arParams);
		$bs = new CIBlockSection;
		$arFields = Array(
		  "ACTIVE" => "Y",
		  "IBLOCK_ID" => 2,
		  "NAME" => $name,
		  "CODE" => $trans
		);	
		//pr($arFields);
		$res = $bs->Update($cat_id, $arFields);
		$res = $bs->Update($cat_id, $arFields);
		//print_r($res);
		$category = '({"cat":{"ID":"'.$cat_id.'","NAME":"'.$name.'","USER":"'.$user_id.'","CODE":"'.$trans.'"}})';
		
		$res = ($cat_id>0);
		if($res){
			echo $category; 
		}
	}
}

?>