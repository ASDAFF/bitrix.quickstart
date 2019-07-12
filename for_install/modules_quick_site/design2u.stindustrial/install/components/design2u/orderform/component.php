<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

//Отсюда только администратору
if($this->StartResultCache(false))
{
  if($_POST["flg"])
  {
	  if(!empty($_POST["phone"])&& 
	    $_POST["phone"]!=GetMessage("ORDER_PHONE")
		&& 
	    $_POST["phone"]!=GetMessage("NO_PHONE"))
	  {
            $arEventFields = array(
			
			    "USEREMAIL"=>htmlspecialchars(stripslashes($_POST["email"])),
			    "PHONE"=>htmlspecialchars(stripslashes($_POST["phone"]))
 
             );
			 
			 
			$arFilter=array(
			 "TYPE_ID" => "eletro_mess",
			 "SUBJECT"=>GetMessage("BACK_CALL")
			
			); 
			 
			$resObj=CEventMessage::GetList($by="site_id", $order="desc", $arFilter);
			 
			$result=$resObj->Fetch();
			
			//echo $result['ID'];
			
			 
            CEvent::Send("eletro_mess", SITE_ID, $arEventFields,'N',$result['ID']); 
			$arResult['festatus']=GetMessage("ORDER_SUCCES");
	      
      }
	  else
	 {
		    $arResult['fnoestatus']=GetMessage("YOU_NO_PHONE");		  
      }
	
  }

	$this->IncludeComponentTemplate();
	
	return $arResult;


	
	  		     	 
 }
	  
	

	

	
    


?>