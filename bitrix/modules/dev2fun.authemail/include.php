<?php
/**
 * 
 * @author dev2fun (darkfriend)
 * @copyright (c) 2016, darkfriend
 * @version 1.0.0
 * 
 */
if(class_exists('dev2funModelAuthEmailClass')) return;

class dev2funModelAuthEmailClass {
	function auth(){
		if(!filter_var($_REQUEST['USER_LOGIN'], FILTER_VALIDATE_EMAIL)) return;
		CModule::IncludeModule("main");
	    $rsUser = CUser::GetList(
	        ($by="id"),
	        ($order="asc"),
	        array(
	            "=EMAIL"=>htmlspecialcharsbx($_REQUEST['USER_LOGIN'])
	        )
	    );
	    global $USER;
	    if($arU = $rsUser->GetNext()){
	        if($_REQUEST["USER_LOGIN"]==$arU['EMAIL']){
	            $_POST["USER_LOGIN"] = $_REQUEST["USER_LOGIN"] = $arU['LOGIN'];
	        }
	    }
	}
}