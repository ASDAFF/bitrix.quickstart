<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
header('Content-Type: text/html; charset='.SITE_CHARSET);
 
 if(SITE_CHARSET == "windows-1251")
 {
    $_REQUEST["user_name"] = iconv('UTF-8','windows-1251', $_REQUEST["user_name"]);
    $_REQUEST["user_message"] = iconv('UTF-8','windows-1251', $_REQUEST["user_message"]);   
 } 
if(
	isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
	!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
	strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
) {
	//Simple processing the feedback form
	$feedback_type = htmlspecialchars(strip_tags($_REQUEST['feedback_type']));
	$feedback_error = false;
	$feedback_response = '';

	
	switch($feedback_type)
	{
	    case 'feedback_call_2':	
        case 'feedback_call': 
		
			$user_name = htmlspecialcharsbx($_REQUEST['user_name']);
			$user_phone = htmlspecialcharsbx($_REQUEST['user_phone']);
			$user_email = htmlspecialcharsbx($_REQUEST['user_mail']);
            
			$reg = '/^((8|\+7)[\- ]?)?(\(?\d{3}\)?[\- ]?)?[\d\- ]{7,10}$/';	
			if(preg_match($reg, $user_phone))
			{
			 
                $strrod = "";
                if(CModule::IncludeModule("iblock"))
               { 
                 if($_REQUEST['product_id']> 0)
                {
                  $prod =   CIBlockElement::GetByID($_REQUEST['product_id'])->Fetch();
                  if($prod)
                  {
                    $strrod = "{$prod['NAME']}";
                  }
                }
               }
				$user_phone = '+7 '.$user_phone;
				$arEventFields = array(
					"USER_NAME"  => $user_name,
					"USER_PHONE" => $user_phone,                                       
				);
				$event = CEvent::Send('EMARKET_FEEDBACK_CALL', SITE_ID, $arEventFields);
				if($event > 0)
				{
					$feedback_response = 'js_kr_req_send';
					
				}
				else
				{
					$feedback_error = true;
					$feedback_response = 'js_kr_error_send';
				}
			}
			else
			{
				$feedback_error = true;
				$feedback_response = 'js_kr_error_phone';
			}
		break;
         case 'buyoneclick': 
		
			$user_name = htmlspecialcharsbx($_REQUEST['user_name']);
			$user_phone = htmlspecialcharsbx($_REQUEST['user_phone']);
			$user_email = htmlspecialcharsbx($_REQUEST['user_mail']);
            
			$reg = '/^((8|\+7)[\- ]?)?(\(?\d{3}\)?[\- ]?)?[\d\- ]{7,10}$/';	
			if(preg_match($reg, $user_phone))
			{
			 
                $strrod = "";
                if(CModule::IncludeModule("iblock"))
               { 
                 if($_REQUEST['product_id']> 0)
                {
                  $prod =   CIBlockElement::GetByID($_REQUEST['product_id'])->Fetch();
                  if($prod)
                  {
                    $strrod = "{$prod['NAME']}";
                  }
                }
               }
				$user_phone = '+7 '.$user_phone;
				$arEventFields = array(
					"USER_NAME"  => $user_name,
					"USER_PHONE" => $user_phone,
                    "USER_MAIL" => $user_mail,
                    "PRODUCT" => $strrod
				);
				$event = CEvent::Send('EMARKET_FEEDBACK_PROPD', SITE_ID, $arEventFields);
				if($event > 0)
				{
					$feedback_response = 'js_kr_req_send';
					
				}
				else
				{
					$feedback_error = true;
					$feedback_response = 'js_kr_error_send';
				}
			}
			else
			{
				$feedback_error = true;
				$feedback_response = 'js_kr_error_phone';
			}
		break;
		case 'feedback_write': 
			
			$user_name = htmlspecialcharsbx($_REQUEST['user_name']);
			$user_mail = htmlspecialcharsbx($_REQUEST['user_mail']);
			$user_message = htmlspecialcharsbx($_REQUEST['user_message']);
		
			$reg = '/.+@.+\..+/i';
			if(preg_match($reg, $user_mail))
			{
				$arEventFields = array(
					"USER_NAME"	   => $user_name,
					"USER_MAIL" => $user_mail,
					"USER_MESSAGE" => $user_message,
				);
				$event = CEvent::Send('EMARKET_FEEDBACK_WRITE', SITE_ID, $arEventFields);
				if($event > 0)
				{
				
					$feedback_response = 'js_kr_req_send';
				}
				else
				{
					$feedback_error = true;
					$feedback_response = 'js_kr_error_send';
				}
			}
			else
			{
				$feedback_error = true;
				$feedback_response = 'js_kr_error_email';
			}
		break;
	}

    echo json_encode(array(
                "error" => $feedback_error,
                "msg" =>  $feedback_response
            ));
}
?>