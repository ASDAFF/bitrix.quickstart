<?php
global $MESS;
include($_SERVER["DOCUMENT_ROOT"].BX_ROOT.'/modules/gsa.modul/lang/ru/all.php');

class cGsa {
    static $MODULE_ID="gsa.modul";
	var $login='';
	var $is_log = 0;
    static $arFunctions = array(
            "StoresRead" => array(
                "URL" => 'https://api.getshopapp.com/v1/cms/Stores/Read/',
                "METHOD" => 'GET',
                ),
            "PaymentMethodsReadAll" => array(
                "URL" => "https://api.getshopapp.com/v1/cms/PaymentMethods/ReadAll/",
                "METHOD" => "GET",
                ),
            "DeliveryMethodsReadAll" => array(
                "URL" => "https://api.getshopapp.com/v1/cms/DeliveryMethods/ReadAll/",
                "METHOD" => "GET",
                ),
            "DeliveryMethodsRead" => array(
                "URL" => "https://api.getshopapp.com/v1/cms/DeliveryMethods/Read/",
                "METHOD" => "GET",
                ),
            "ShopInfoRead" => array(
                "URL" => "https://api.getshopapp.com/v1/cms/ShopInfo/Read/",
                "METHOD" => "GET",
                ),
            "DeliveryMethodsCreate" => array(
                "URL" => "https://api.getshopapp.com/v1/cms/DeliveryMethods/Create/",
                "METHOD" => "POST",
                ),
            "DeliveryMethodsDelete" => array(
                "URL" => "https://api.getshopapp.com/v1/cms/DeliveryMethods/Delete/",
                "METHOD" => "POST",
                ),
            "DeliveryMethodsUpdate" => array(
                "URL" => "https://api.getshopapp.com/v1/cms/DeliveryMethods/Update/",
                "METHOD" => "POST",
                ),
            "CheckDomain" => array(
                "URL" => "https://api.getshopapp.com/v1/cms/Stores/CheckDomain/",
                "METHOD" => "POST",
                ),
            "OrdersImport" => array(
                "URL" => "https://api.getshopapp.com/v1/cms/Orders/Import/",
                "METHOD" => "POST",
                ),
			"OrdersUpdate" => array(
                "URL" => "https://api.getshopapp.com/v1/cms/Orders/Update/",
                "METHOD" => "POST",
                ),
			"OrdersReadAll" => array(
                "URL" => "https://api.getshopapp.com/v1/cms/Orders/ReadAll/",
                "METHOD" => "GET",
                ),
            "OrderCreatedHook" => array(
                "URL" => "https://api.getshopapp.com/v1/cms/Hooks/Create/",
                "METHOD" => "POST",
                ),
           	"ShopInfoCreate" => array(
                "URL" => "https://api.getshopapp.com/v1/cms/ShopInfo/Create/",
                "METHOD" => "POST",
                ),
           	"ShopInfoDelete" => array(
                "URL" => "https://api.getshopapp.com/v1/cms/ShopInfo/Delete/",
                "METHOD" => "POST",
                ),
           	"Launchpad" => array(
                "URL" => "https://api.getshopapp.com/v1/cms/Links/Launchpad/",
                "METHOD" => "GET",
                ),
           "HooksReadAll" => array(
                "URL" => "https://api.getshopapp.com/v1/cms/Hooks/ReadAll/",
                "METHOD" => "GET",
                ),
           "HookDelete" => array(
                "URL" => "https://api.getshopapp.com/v1/cms/Hooks/Delete/",
                "METHOD" => "POST",
                ),
           "AddCatalogue" => array(
           		"URL" => "https://api.getshopapp.com/v1/cms/Catalogues/Create/",
                "METHOD" => "POST",
           	),
           "ReadAllCat" => array(
           		"URL" => "https://api.getshopapp.com/v1/cms/Catalogues/ReadAll/",
                "METHOD" => "GET",
           	),
            "DeleteCatalogue" => array(
           		"URL" => "https://api.getshopapp.com/v1/cms/Catalogues/Delete/",
                "METHOD" => "POST",
           	),
           	"UserUpdatedBitrix" => array(
           		"URL" => "https://api.getshopapp.com/v1/cms/Users/Update/",
                "METHOD" => "POST",
           	),



           	

        );




	//устанавливает редирект с стендовой версии на мобильную
    public function LaunchpadSetter()
    {    	
    	global $APPLICATION;
    	$user_name = COption::getOptionString("gsa.modul", "user_name");
		$user_pass = COption::getOptionString("gsa.modul", "user_pass");
		/*RegisterModuleDependences("sale", "OnOrderAdd", "gsa.modul", "cGsa", "OrderCreateBitrix");
        RegisterModuleDependences("sale", "OnOrderUpdate", "gsa.modul", "cGsa", "OrderUpdateBitrix");
        RegisterModuleDependences("main", "OnPageStart", "gsa.modul","cGsa", "LaunchpadSetter");*/	

       /* $rsEvents = GetModuleEvents("gsa.modul", "OnPageStart");
        print_r($rsEvents);
		while ($arEvent = $rsEvents->Fetch())
		{
			var_dump($arEvent);
		}*/
		//exit;

		if(strlen($user_name)==0 || strlen($user_pass)==0) return false;

		self::writeToLog("LaunchpadSetter");

		ob_start();
    	$launch = COption::getOptionString("gsa.modul", "Launchpad");
		if(strlen($launch)<10)
		{
			$resa = self::callApi('Launchpad', array());
			COption::SetOptionString("gsa.modul", "Launchpad", $resa->URL);
		}
		$launch = COption::getOptionString("gsa.modul", "Launchpad");
		$APPLICATION->AddHeadString('<script type="text/javascript">var getshopappLaunchpadParams ={};</script>
									<script src="'.$launch.'"></script>',true);
		ob_end_clean();

    }

    public function UserRegister($info)
    {
    	
		$login = $info->Credentials->Login;
		$password = $info->Credentials->Password;
		$name = @$info->UserInfo->FirstName;
		$userid = self::createUser($login,$name,$password);

		$ret = array();
		$ret['OpStatus'] = 0;
		$ret['ForeignUID'] = $userid;		
		return json_encode($ret);
    }

    public function UserAuth($info)
    {
    	$order_ids = array();    
    	$ret = array();
    	$ret['OpStatus'] = "5";
    	$ret['ForeignUID'] = "0";

    	$fp = fopen($_SERVER['DOCUMENT_ROOT'].'/gsa_userAUTH.txt', 'a+');
		fwrite($fp, "RET: ".serialize($info));
		fclose($fp);

    	//if($info->WithUserInfo==true)    	
    	//{
    		$ret = self::getUser($info->Credentials->Login,$info->Credentials->Password);    		
    		$uid = $ret['ForeignUID'];    	


    		$fp = fopen($_SERVER['DOCUMENT_ROOT'].'/gsa_userAUTH.txt', 'a+');
			fwrite($fp, "UID: ".serialize($ret)." STATUS: ".$ret['OpStatus']);
			fclose($fp);

    		if(CModule::IncludeModule("sale") && intval($ret['OpStatus'])==0)
    		{
	    		$rsSales = CSaleOrder::GetList(array(), array("USER_ID"=>$uid));
				while($order = $rsSales->Fetch())
				{
					ob_start();self::OrdersImport(intval($order['ID']));ob_end_clean();

					$fp = fopen($_SERVER['DOCUMENT_ROOT'].'/gsa_userAUTH.txt', 'a+');
					fwrite($fp, "ORDER: ".serialize($order));
					fclose($fp);

				}
			}			
    	//}    	
    	return json_encode($ret);
    }

   

    //обновление информации по пользователю после действий со стороны ГСА в сторону битрикса
    public function UserUpdated($info)
    {
    	$user = array();
    	//$info->ForeignUID = "2";
    	/*$rsUser = CUser::GetByID(intval($info->ForeignUID));
    	$arUser = $rsUser->Fetch();
    	print_r($arUser);
    	exit;*/
    	
    	$user['LAST_NAME']=(string)$info->UserInfo->LastName;
    	$user['NAME']=(string)$info->UserInfo->FirstName;
    	$user['PERSONAL_PHONE']=(string)$info->UserInfo->Phone;
    	$user['EMAIL']=(string)$info->UserInfo->Email;

    	$u = new CUser;
    	$u->Update(intval($info->ForeignUID),$user);
    	return json_encode(array("OpStatus"=>0));
    }

    //обновление информации по пользователю после действий со стороны Битрикса в сторону ГСА
    public function UserUpdatedBitrix(&$arFields)
    {
    	
    	$ret = self::getUser("","",$arFields['ID']);    	
    	unset($ret['OpStatus']);
    	$user = $ret;
    	$rsUser = CUser::GetByID($user['ForeignUID']);
		$arUser = $rsUser->Fetch();
    	//print_r($arUser);exit;
    	$user['Login'] = $arUser['LOGIN'];
    	//print_r(json_encode($user));exit;
    	self::callApi('UserUpdatedBitrix', $user);
    	//exit;
    	return true;    	
    }






    

	//получение всех хуков
    public function getHooks()
    {
    	self::writeToLog("getHooks");
    	$tmp = self::callApi('HooksReadAll', array());			//удаляем инфу если таковая имелась
    	//print_r($tmp);exit;
    	$gsa_key = COption::getOptionString("gsa.modul", "init_key");
    	$hooks = $tmp->Hooks;

    	//удалем все хуки
    	if(strlen($gsa_key)==0)
    	{
    		for($i=0;$i<count($hooks);$i++)
    			self::HookDelete($hooks[$i]->HookID);
    		unset($hooks);
    		$hooks = array();
    	}


    	//если хуков нет, то инициализируем их
    	if(count($hooks)==0)
    	{
    		COption::SetOptionString("gsa.modul", "init", "0");
    		self::makeInit();
    	}
    	return $hooks;

    }

    //удаление хуков
    public function HookDelete($id)
    {
    	self::writeToLog("HookDelete");
    	self::callApi('HookDelete', array("HookID"=>$id));			//удаляем инфу если таковая имелась
    }

    public function removeAllHooks()
    {
    	self::writeToLog("removeAllHooks");
    	$hooks = self::getHooks();
    	for($i=0;$i<count($hooks);$i++)
    			self::HookDelete($hooks[$i]->HookID);
    }

    //проверка статуса регистрации
    public function getAuth()
    {
    	$user_name  = COption::GetOptionString("gsa.modul", "user_name");
		$user_pass  = COption::GetOptionString("gsa.modul", "user_pass");
    	if(strlen($user_name)==0 || strlen($user_pass)==0)  return false;
    	$cookie = self::getCookieString();
        if (!$cookie) {
            $cookie = self::getNewCookieString();
            if (!$cookie) {                
                return false;
            }
        }
        return true;
    }


	//функция на изменение параметра магахина
	public static function ShopInfoUpdate($str)
	{
		if(!self::getAuth()) return false;
		self::writeToLog("ShopInfoUpdate(".json_encode($str).")");
		$arr['ShopInfo']=$str;
		self::callApi('ShopInfoDelete', array("Locale"=>$str['Locale']));			//удаляем инфу если таковая имелась
		self::callApi('ShopInfoCreate', $arr);							//и пишем по новой
	}

	//функция на прослушку изменения ордера
	public static function  OrderUpdateBitrix(&$ID, &$arFields)
	{
		self::writeToLog("OrderUpdateBitrix(".$ID.",".json_encode($arFields).")");
		ob_start();self::OrdersUpdate(intval($ID));ob_end_clean();
	}

	//функция на прослушку создания заказа
	public static function OrderCreateBitrix($ID=0,&$arOrder=array(),&$arParams=array())
	{		
		
		/*UnRegisterModuleDependences("sale", "OnSaleComponentOrderComplete", "gsa.modul", "cGsa", "OrderCreateBitrix");
		RegisterModuleDependences("sale", "OnOrderNewSendEmail","gsa.modul", "cGsa", "OrderCreateBitrix");exit;*/

		self::writeToLog("OrderCreateBitrix(".$ID.")");
		$fp = fopen($_SERVER['DOCUMENT_ROOT'].'/gsa_order_bitrix.txt', 'a+');
		fwrite($fp, $ID);
		fclose($fp);
		ob_start();
		self::OrdersImport(intval($ID));
		ob_end_clean();
	}

    //блок инициализации хуков
    //запускается только при установке системы и инициализирует дальнейшую обработку
    public static function makeInit()
    {

		$user_name = COption::getOptionString("gsa.modul", "user_name");
		$user_pass = COption::getOptionString("gsa.modul", "user_pass");
		$hook_exist = COption::getOptionString("gsa.modul", "init");
		$catalog_init = COption::getOptionString("gsa.modul", "catalog_init");

		if(strlen($user_name)==0 || strlen($user_pass)==0) return false;

		if(isset($hook_exist) && intval($hook_exist)==1) return false;
		else
		{
			//чистим старые хуки


			//проставляем защиту в виде созхдания файла
			$gsa_key = COption::getOptionString("gsa.modul", "init_key");
	    	if(strlen($gsa_key)==0)
	    	{
		    	$filename = time();
		    	$handle = fopen($_SERVER['DOCUMENT_ROOT']."/gsa_key_".$filename, 'w');
		    	fwrite($handle,'');
		    	fclose($handle);
		    	if(!file_exists($_SERVER['DOCUMENT_ROOT']."/gsa_key_".$filename))  exit(json_encode(array("STATUS" => 1,"MESSAGE" => "CANT WRITE THE FILE TO ROOT DIRECTORY")));
		    	COption::SetOptionString("gsa.modul", "init_key", $filename);
	    	}
	    	$gsa_key = COption::getOptionString("gsa.modul", "init_key");

			//создаем хук на создание заказа
			$hook = array();
			$hook["Hook"]["HookEvent"] = "OrderCreated";
			$hook["Hook"]["AuthProto"] = "None";
			$hook["Hook"]["URL"] = "http://".$_SERVER['HTTP_HOST']."/gsa_block.php?action=createOrder&key=".$gsa_key;
			$resa = self::callApi('OrderCreatedHook', $hook);

			//создаем хук на обновление заказа
			$hook["Hook"]["HookEvent"] = "OrderUpdated";
			$hook["Hook"]["AuthProto"] = "None";
			$hook["Hook"]["URL"] = "http://".$_SERVER['HTTP_HOST']."/gsa_block.php?action=updateOrder&key=".$gsa_key;
			$resa = self::callApi('OrderCreatedHook', $hook);

			//хук на доставку
			$hook["Hook"]["HookEvent"] = "DeliveryCheck";
			$hook["Hook"]["AuthProto"] = "None";
			$hook["Hook"]["URL"] = "http://".$_SERVER['HTTP_HOST']."/gsa_block.php?action=DeliveryCheck&key=".$gsa_key;
			$resa = self::callApi('OrderCreatedHook', $hook);

			//хук на регистраицию пользвоателя
			$hook["Hook"]["HookEvent"] = "UserRegister";
			$hook["Hook"]["AuthProto"] = "None";
			$hook["Hook"]["URL"] = "http://".$_SERVER['HTTP_HOST']."/gsa_block.php?action=UserRegister&key=".$gsa_key;
			$resa = self::callApi('OrderCreatedHook', $hook);


	    	//хук на регистраицию пользвоателя
			$hook["Hook"]["HookEvent"] = "UserRegister";
			$hook["Hook"]["AuthProto"] = "None";
			$hook["Hook"]["URL"] = "http://".$_SERVER['HTTP_HOST']."/gsa_block.php?action=UserRegister&key=".$gsa_key;
			$resa = self::callApi('OrderCreatedHook', $hook);

	    	//хук на аутентификацию пользвоателя
			$hook["Hook"]["HookEvent"] = "UserAuth";
			$hook["Hook"]["AuthProto"] = "None";
			$hook["Hook"]["URL"] = "http://".$_SERVER['HTTP_HOST']."/gsa_block.php?action=UserAuth&key=".$gsa_key;
			$resa = self::callApi('OrderCreatedHook', $hook);

			//хук на обновление информации от пользователя
			$hook["Hook"]["HookEvent"] = "UserUpdated";
			$hook["Hook"]["AuthProto"] = "None";
			$hook["Hook"]["URL"] = "http://".$_SERVER['HTTP_HOST']."/gsa_block.php?action=UserUpdated&key=".$gsa_key;
			$resa = self::callApi('OrderCreatedHook', $hook);
			

			//пишем что типа инициализация прошла успешно
			COption::SetOptionString("gsa.modul", "init", "1", "Инициализация модуля");

		}

		if(strlen($catalog_init)==0)
		{
			//создаем ссылку на каталог для выгрузки
			$catalog = array();
			$catalog['Catalogue']['Locale'] = 'ru_RU';
			$catalog['Catalogue']['Proto'] = 'JSON';
			$catalog['Catalogue']['UpdateInterval'] = '120';
			$catalog['Catalogue']['URL'] = "http://".$_SERVER['HTTP_HOST']."/upload/gsa_export.txt";
			$resa = self::callApi('AddCatalogue', $catalog);

			COption::SetOptionString("gsa.modul", "catalog_init", "1", "Инициализация ссылки на каталог");
		}


		//print_r($resa);
		//print_r($hook);
    }

    //получения полного перечня каталогов
    function ReadAllCat()
    {
    	self::writeToLog("ReadAllCat");
		return self::callApi('ReadAllCat');    		
    }

    function DeleteCatalog()
    {
    	self::writeToLog("DeleteCatalog");
    	$cat = array();
		$cat = self::callApi('ReadAllCat');    	
		foreach($cat AS $key=>$value)
		{
			ob_start();
			self::callApi('DeleteCatalogue',array('Locale'=>'ru_RU'));
			ob_end_clean();
			
		}
    }

    function setCatalog()
    {
    	self::writeToLog("setCatalog");
    	$catalog = array();
		$catalog['Catalogue']['Locale'] = 'ru_RU';
		$catalog['Catalogue']['Proto'] = 'JSON';
		$catalog['Catalogue']['UpdateInterval'] = '120';
		$catalog['Catalogue']['URL'] = "http://".$_SERVER['HTTP_HOST']."/upload/gsa_export.txt";
		//print_r($catalog);
		$resa = self::callApi('AddCatalogue', $catalog);
		COption::SetOptionString("gsa.modul", "catalog_init", "1", "Инициализация ссылки на каталог");
    }


	public static function createOrder($string)
	{
		self::writeToLog("createOrder(".json_encode($string).")");
		$orderInfo = $string->OrderInfo;
		if(strlen($orderInfo->Login)>0)
			$user_id = self::createUser(strtolower($orderInfo->Login),$orderInfo->ResolvedOrder->FirstName);
		else
		{
			$name = preg_replace('/(^A-Za-z0-9)/', '', strtolower($orderInfo->ResolvedOrder->FirstName));
			$phone = (int) preg_replace('/\D/', '',$orderInfo->ResolvedOrder->Phone);
			$login =$name.$phone;
			$user_id = self::createUser($login,$orderInfo->ResolvedOrder->FirstName);
		}
		$order = self::orderInfoConvertOneItem($orderInfo,$user_id);


		/*echo "<pre>";
		print_r($orderInfo);
		print_r($order);
        echo "</pre>";
		exit;*/


		//создаем заказ
		if(CModule::IncludeModule("sale"))
		{
			// создаем заказ с корзиной
			$tempbasket = new CSaleBasket;
			for($i=0;$i<count($order['items']);$i++)
				$tempbasket->Add($order['items'][$i]);
			$uid = $tempbasket->GetBasketUserID();
			$ORDER_ID = CSaleOrder::Add($order['order']);
			$ORDER_ID = IntVal($ORDER_ID);
			$tempbasket->OrderBasket($ORDER_ID, "1", 's1');
		}
		//echo "Заказ успешно создан";
		/*echo "<pre>";
		print_r($orderInfo);
		print_r($orders);
        echo "</pre>";*/
        return json_encode(array("OpStatus"=>0));
	}

	public function updateOrder($string)
	{
		self::writeToLog("updateOrder(".json_encode($string).")");
		$orderInfo = $string->OrderInfo;

		/*echo "<pre>";
		print_r($orderInfo);
        echo "</pre>";*/

		$order = array();
		if(CModule::IncludeModule("sale"))
		{
			$arFilter = Array("ADDITIONAL_INFO" => $orderInfo->OID);
			$rsSales = CSaleOrder::GetList(array(), $arFilter);
			while ($arSales = $rsSales->Fetch())
			{
				$order = $arSales;
			}

			$change_array = array(
				"PAYED" => self::getPayment($orderInfo->PaymentStatus),          //флаг (Y/N) оплачен ли заказ;
                "CANCELED" => self::getCanceled($orderInfo->OrderStatus),        //флаг (Y/N) отменён ли заказ;
                "STATUS_ID" => self::getStatusGSAB($orderInfo->OrderStatus),   //код статуса заказа;
				);

			CSaleOrder::Update($order['ID'],$change_array);


			//просто тупо проверка
			/*$arFilter = Array("ADDITIONAL_INFO" => $orderInfo->OID);
			$rsSales = CSaleOrder::GetList(array(), $arFilter);
			$order = $rsSales->Fetch();
			echo "<pre>";
			print_r($arSales);
			echo "</pre>";*/
		}
		 return json_encode(array("OpStatus"=>0));
	}

	public function DeliveryCheck($string)
	{
		self::writeToLog("DeliveryCheck(".json_encode($string).")");
		if (CModule::IncludeModule('iblock') && CModule::IncludeModule("catalog") && CModule::IncludeModule("sale"))
		{
			$order = $string->Order;
			$delivery = $string->DeliveryQuery;

			//если ничего не пришло
			if (!is_numeric($order->PaymentInfo->Sum) || !is_numeric($delivery->LocationID))
			{
				$arError = array("STATUS" => 1,"MESSAGE" => "BAD PRICE OR LOCATION",);
				exit(json_encode($arError));
			}
			// print_r($order);


			$count = count($order->ItemInfo);
			foreach ($order->ItemInfo as $item)
			{
				$id = $item->ForeigID;
				$count= $item->Amount;
				if (!is_numeric($id) || !is_numeric($count))
				{
					$arError = array("STATUS" => 1,"MESSAGE" => "BAD ID OR QUANTITY",);
					exit(json_encode($arError));
				}
				else
				{
					$arItem = CCatalogProductProvider::GetProductData(
					    array(
					    		"PRODUCT_ID" => $id,
					    		"QUANTITY" => $count,
					    	)
					);
					if (count($arItem) > 0)
					{
						$weight+=$arItem["WEIGHT"];
						$dimensions = unserialize($arItem["DIMENSIONS"]);
						$arItemsDimensions[] = $dimensions;
						$maxdimension = max($maxdimension, max($dimensions));
					}
					else
					{
						$arError = array("STATUS" => 1,"MESSAGE" => "NO ITEM IN CATALOG");
						exit(json_encode($arError));
					}
				}
			}


			if ($weight <= 0) $weight = 1;
			if ($maxdimension <= 0) $maxdimension = 1;

			$arOrder = array(
			  "WEIGHT" => $weight, // вес заказа в граммах
			  "PRICE" => $arInput["Price"], // стоимость заказа в базовой валюте магазина
			  "LOCATION_FROM" => COption::GetOptionInt('sale', 'location'), // местоположение магазина
			  "LOCATION_TO" => $arInput["LocationTo"], // местоположение доставки
			  "MAX_DIMENSIONS" => $maxdimension,
			  "ITEMS" => $arItemsDimensions,
			);
			// echo "<pre>";
			// print_r($arOrder);
			if(count($arVariants)==0) exit(json_encode(array("STATUS" => 1,"MESSAGE" => "NO DELIVERY VARIANTS")));

			$str = array();
			$str['OpStatus']=0;
			$i=0;
			foreach($arVariants AS $key=>$value)
			{

				$str['DeliveryVariants'][$i]['ForeignID'] = $value['SID'];
				$str['DeliveryVariants'][$i]['DeliveryProfiles']['DeliveryProfileID'] = $value['SID'];
				$str['DeliveryVariants'][$i]['DeliveryProfiles']['Name'] = $value['PROFILES']['NAME'];
				$str['DeliveryVariants'][$i]['DeliveryProfiles']['Description'] = $value['PROFILES']['DESCRIPTION'];
				$str['DeliveryVariants'][$i]['DeliveryProfiles']['Price'] = doubleval($value['PROFILES']['Price']);
				$str['DeliveryVariants'][$i]['DeliveryProfiles']['Curr'] = "RUB";
				$str['DeliveryVariants'][$i]['DeliveryProfiles']['ProcessingTime'] = "[24,48]";
				$i++;

			}
			//echo "<pre>";print_r($arVariants);echo "</pre>";
			exit(json_encode($str));
		}
	}


	//функция получает текущего пользователя и создает нового пользователя
	private function createUser($login,$name,$password="passpasspass")
	{
		self::writeToLog("createUser(".$login.",".$name.")");
		//ищем или создаем пользователя
		$rsUser = CUser::GetByLogin($login);
		if (!($arUser = $rsUser->Fetch()))
		{
			$user = new CUser;
			$arFields = Array(
				"NAME" => $name,
				"EMAIL" => "app@netnetnetmail.ru",
				"LOGIN" => $login,
				"LID" => "s1",
				"ACTIVE" => "Y",
				"GROUP_ID" => array(1,2),
				"PASSWORD" => $password,
				"CONFIRM_PASSWORD" => $password
			);
			$ID = $user->Add($arFields);
			if (intval($ID) > 0) {$userID = $ID;/*echo "Новый создан";*/}
			else {/*echo @$user->LAST_ERROR;*/$userID = '1';}
		}
		else
		{
			$userID = $arUser["ID"];
			/*echo "ЕСТЬ ТАКОЙ";*/
		}
		return $userID;
	}

	public function getUser($login,$password,$uid=0)
	{
		$return = array();

		//echo $login."<br/>".CUser::GetPasswordHash($password);
		if($uid==0)
			$rsUser = CUser::GetByLogin($login);
		else
			$rsUser = CUser::GetByID($uid);

		$arUser = $rsUser->Fetch();
		//print_r($arUser);
		if(!$rsUser) 
		{
			$return['OpStatus'] = 4;
			$return['ForeignUID'] = "0";
		}
		if(self::isUserPassword($arUser['ID'],$password) || intval($uid)>0) 
		{
			$return['OpStatus'] = 0;
			$return['ForeignUID'] = $arUser['ID'];
			$return['UserInfo']['FirstName'] = (string)$arUser['NAME'];
			$return['UserInfo']['LastName'] = (string)$arUser['LAST_NAME'];

			if(strlen($arUser['PERSONAL_PHONE'])>0)
				$return['UserInfo']['Phone'] = intval((string)$arUser['PERSONAL_PHONE']);
			else
				$return['UserInfo']['Phone'] = null;

			$return['UserInfo']['Email'] = (string)$arUser['EMAIL'];
			$return['UserInfo']['Age'] = (string) intval(date("Y")-date("Y",strtotime($arUser['PERSONAL_BIRTHDAY'])));
			$return['UserInfo']['Sex'] = "Male";
			if($arUser['PERSONAL_GENDER']=='0') $return['UserInfo']['Sex'] = "Female";

			$return['UserInfo']['UserAddresses'][0]['ID'] = 0;
			$return['UserInfo']['UserAddresses'][0]['ZipCode'] = (string)$arUser['PERSONAL_ZIP'];
			$return['UserInfo']['UserAddresses'][0]['City'] = (string)$arUser['PERSONAL_CITY'];
			$return['UserInfo']['UserAddresses'][0]['Street'] = (string)$arUser['PERSONAL_STREET'];
			$return['UserInfo']['UserAddresses'][0]['House'] = "";
			$return['UserInfo']['UserAddresses'][0]['Gate'] = "";
			$return['UserInfo']['UserAddresses'][0]['DoorCode'] = "";
			$return['UserInfo']['UserAddresses'][0]['Floor'] = "";
			$return['UserInfo']['UserAddresses'][0]['Apartment'] = "";
			if(strlen($arUser['WORK_PHONE'])>0)
				$return['UserInfo']['UserAddresses'][0]['Phone'] = intval((string)$arUser['WORK_PHONE']);		
			else
				$return['UserInfo']['UserAddresses'][0]['Phone'] = NULL;		
		}
		else
		{
			$return['OpStatus'] = 2;
			$return['ForeignUID'] = "0";
		}
		return $return;
	}

	



	private function isUserPassword($userId, $password)
	{
	    $userData = CUser::GetByID($userId)->Fetch();
	    $salt = substr($userData['PASSWORD'], 0, (strlen($userData['PASSWORD']) - 32));
	    $realPassword = substr($userData['PASSWORD'], -32);
	    $password = md5($salt.$password);
	    return ($password == $realPassword);
	}


	//функция смотрит все возможные типы оплаты
	public function PaymentMethodsReadAll()
	{
		self::writeToLog("PaymentMethodsReadAll");
		if (CModule::IncludeModule('iblock') && CModule::IncludeModule("catalog") && CModule::IncludeModule("sale"))
        {
    		$resa = self::callApi('PaymentMethodsReadAll', array());
			//print_r($resa);
		}
	}


    //функция переправки заказа в интернет магазин
    public static function OrdersImport($order_id=0)
    {

 
    	$order_id = intval($order_id);
    	self::writeToLog("OrdersImport(".$order_id.")");
        if (CModule::IncludeModule('iblock') && CModule::IncludeModule("catalog") && CModule::IncludeModule("sale"))
        {
            //получаем перечень заказов конкретного пользователя
            $ar_sales = CSaleOrder::GetByID($order_id);
            //print_r($ar_sales);exit;
            //echo "<pre>";

            //получаем перечень товаров
			$tmp = CSaleBasket::GetList(array(),array("ORDER_ID" =>$ar_sales['ID']));
			
			//print_r($tmp);exit;


			$rsUser = CUser::GetByID($ar_sales['USER_ID']);
			$arUser = $rsUser->Fetch();
			if(strlen($arUser['EMAIL'])==0) return false;		//[15:20:42] Anton: если аноним или его нет, то мы не примем, потому что нам такие заказы пока не интересны

            //собираем нужный заказ
			//print_r($ar_sales);
			$arRequest = array();
			$arRequest["OrderInfo"] = array(
				"Login"=> $arUser['LOGIN'],
				"ResolvedOrder"=>self::ResolveOrderComposer($tmp,$ar_sales,$arUser['ID']),
				"Sum" =>doubleval($ar_sales['PRICE']),
				"CreatedAt" => gmdate("Y-m-d\TH:i",strtotime($ar_sales['DATE_INSERT'])),
				"UpdatedAt" => gmdate("Y-m-d\TH:i",strtotime($ar_sales['DATE_UPDATE'])),
				"OrderStatus"=>self::setStatus($ar_sales['STATUS_ID']),
				"PaymentStatus"=>self::setPayment($ar_sales['PAYED']),
				//"OID"=>4,
				"HumanOID"=>intval($ar_sales['USER_ID']),
				"ForeignOID"=>intval($ar_sales['ID']),
			);
			//print_r($arRequest);          exit;

			$resa = self::callApi('OrdersImport', $arRequest);
			//если все круто отправилось, то доотправляем только те что не были отправлены в противном случае сохраняем запрос и отправляем те что не получилось отправить
			//if(true)
				//self::saveSendRequest('OrdersImport',serialize($arRequest),false);
			//else
			//self::saveSendRequest('OrdersImport',serialize($arRequest),true);

			//на всякий случай
            //var_dump($resa);
            //echo "</pre><br/>";
        }
    }

	//функция обновления заказа
	public static function OrdersUpdate($order_id=0)
	{
		self::writeToLog("OrdersUpdate(".$order_id.")");
		if (CModule::IncludeModule('iblock') && CModule::IncludeModule("catalog") && CModule::IncludeModule("sale"))
        {
			$ar_sales = CSaleOrder::GetByID($order_id);
			//echo "<pre>";
			//получаем перечень товаров
            $tmp = CSaleBasket::GetList(array(),array("ORDER_ID" =>$order_id));

			$rsUser = CUser::GetByID($ar_sales['USER_ID']);
			$arUser = $rsUser->Fetch();
			if(strlen($arUser['EMAIL'])==0) return false;		//[15:20:42] Anton: если аноним или его нет, то мы не примем, потому что нам такие заказы пока не интересны


			//собираем нужный заказ
			$arRequest = array();
            $arRequest["Login"] = $arUser['EMAIL'];
			$arRequest["OrderInfo"] = array(
				"ResolvedOrder"=>self::ResolveOrderComposer($tmp,$ar_sales,$arUser['ID']),
				"Sum" =>doubleval($ar_sales['PRICE']),
				"CreatedAt" => gmdate("Y-m-d\TH:i",strtotime($ar_sales['DATE_INSERT'])),
				"UpdatedAt" => gmdate("Y-m-d\TH:i",strtotime($ar_sales['DATE_UPDATE'])),
				"OrderStatus"=>self::setStatus($ar_sales['STATUS_ID']),
				"PaymentStatus"=>self::setPayment($ar_sales['PAYED']),
				//"OID"=>4,
				"HumanOID"=>intval($ar_sales['USER_ID']),
				"ForeignOID"=>intval($ar_sales['ID']),
			);
            //print_r($arRequest);
			$resa = self::callApi('OrdersImport', $arRequest);
			//если все круто отправилось, то доотправляем только те что не были отправлены в противном случае сохраняем запрос и отправляем те что не получилось отправить
			/*if(true)
				self::saveSendRequest('OrdersImport',serialize($arRequest),false);
			else
				self::saveSendRequest('OrdersImport',serialize($arRequest),true);*/

			//echo "</pre>";
		}
	}

	//функция получения всех заказов на стороне магазина
    //и пока создание
	public static function OrdersReadAll()
	{
		self::writeToLog("OrdersReadAll");
        if (CModule::IncludeModule('iblock') && CModule::IncludeModule("catalog") && CModule::IncludeModule("sale"))
        {
    		$resa = self::callApi('OrdersReadAll', array());
    		$orderInfo = $resa->OrderInfo;
			$orders = self::orderInfoConvert($orderInfo,$user_id);



            /*echo "<pre>";
            print_r($orders[2]['items']);
            echo "</pre>";
            exit;*/

			// создаем заказ с корзиной
			$tempbasket = new CSaleBasket;
			$tempbasket->Add($orders[2]['items'][0]);
			$tempbasket->Add($orders[2]['items'][1]);
			$tempbasket->Add($orders[2]['items'][2]);
			$uid = $tempbasket->GetBasketUserID();
			$ORDER_ID = CSaleOrder::Add($orders[2]['order']);
			$ORDER_ID = IntVal($ORDER_ID);
			$tempbasket->OrderBasket($ORDER_ID, "1", 's1');

        }
	}

	//конверстирует OrderInfo в формат для битрикса
	private static function  orderInfoConvert($orderInfo,$user_id=1)
	{
		self::writeToLog("orderInfoConvert(".json_encode($orderInfo).",".$user_id.")");
		$orders = array();
		foreach($orderInfo AS $key=>$value)
    	{
			$orders[$key] = array();
			$arFields = array(
                   "LID" => "s1",                                              // код сайта, на котором сделан заказ;
                   "PERSON_TYPE_ID" => 1,										//тип плательщика, к которому принадлежит посетитель, сделавший заказ (заказчик);
                   "PAYED" => self::getPayment($value->PaymentStatus),          //флаг (Y/N) оплачен ли заказ;
                   "CANCELED" => self::getCanceled($value->OrderStatus),        //флаг (Y/N) отменён ли заказ;
                   "STATUS_ID" => self::getStatusGSAB($value->PaymentStatus),   //код статуса заказа;
                   "PRICE" => $value->Sum,                                      // общая стоимость заказа;
                   "CURRENCY" => $value->ResolvedOrder->PaymentInfo->Curr,  //валюта стоимости заказа;
                   "USER_ID" => $user_id,	                                // код пользователя заказчика;
                   "PAY_SYSTEM_ID" => 3,					//платежная система, которой (будет) оплачен заказа; http://gsa.lsup.ru/bitrix/admin/sale_pay_system.php?lang=ru
                   "DELIVERY_ID" => self::getDeliveryID($value->ResolvedOrder->DeliveryInfo->DeliveryMethodID),          //способ (служба) доставки заказа;
                   "PRICE_DELIVERY"=>doubleval($value->ResolvedOrder->DeliveryInfo->DeliveryMethod->Price),    //цена за доставку
                   "USER_DESCRIPTION" => $value->ResolvedOrder->Comment,                            //произвольные комментарии;
                );

			$orders[$key]['order'] = $arFields;
            /*echo "<pre>";
			print_r($arFields);
			print_r($value);
			echo "</pre>"; */
			$items = array();
			$items_tmp = $value->ResolvedOrder->ItemInfo;
			//echo "<pre>";print_r($items_tmp);echo "</pre><br/>";
			for($i=0;$i<count($items_tmp);$i++)
			{
				$items[$i] = array(
					//"PRODUCT_ID"=>$items_tmp[$i]->IID,   //уникальный в рамках модуля код товара (обязательное поле);
					"PRODUCT_ID"=>$items_tmp[$i]->ForeignID,   //уникальный в рамках модуля код товара (обязательное поле);
                    //PRODUCT_PRICE_ID - ID (идентификатор) конкретного ценового предложения товара, пришедшего в корзину. Может быть использован в классе CPrice модуля Catalog для получения детальной информации о цене.
                    "PRICE"=>$items_tmp[$i]->Price,     // стоимость единицы товара (обязательное поле);
                    "CURRENCY"=>$items_tmp[$i]->Curr,   //валюта стоимости единицы товара (обязательное поле), если валюта отличается от базовой валюты для данного сайта, то стоимость будет автоматически сконвертирована по текущему курсу;
                    //WEIGHT - вес единицы товара;
                    "QUANTITY"=>$items_tmp[$i]->Amount, //количество единиц товара;
                    "LID"=>"s1",                       //сайт, на котором сделана покупка (обязательное поле);
                    //DELAY - флаг "товар отложен" (Y/N);
                    //CAN_BUY - флаг "товар можно купить" (Y/N) - может устанавливаться автоматически про наличии функции обратного вызова для поддержки актуальности корзины;
                    "NAME"=>$items_tmp[$i]->Name,   //название товара (обязательное поле);
                    //"ORDER_ID"=>$items_tmp[$i]->IID,   //идентификатор заказа. Ключ будет пустым, если товар еще не добавлен в заказ;
                    "MODULE"=>"getshopapp",             //модуль, добавляющий товар в корзину;
					"NOTES" => "",
					"FUSER_ID" => 1,
                    );
			}
			$orders[$key]['items'] = $items;

			/*echo "<pre>";
			print_r($items);
			echo "</pre>";
			*/
			unset($items);
		}
		return $orders;
	}


	//конверстирует OrderInfo в формат для битрикса
	private static function  orderInfoConvertOneItem($orderInfo,$user_id)
	{
		self::writeToLog("orderInfoConvertOneItem(".json_encode($orderInfo).",".$user_id.")");

		$orders = array();
		$arFields = array(
           "LID" => "s1",													// код сайта, на котором сделан заказ;
           "PERSON_TYPE_ID" => 1,											//!!! тип плательщика, к которому принадлежит посетитель, сделавший заказ (заказчик);
           "PAYED" => self::getPayment($orderInfo->PaymentStatus),          //флаг (Y/N) оплачен ли заказ;
           "CANCELED" => self::getCanceled($orderInfo->OrderStatus),        //флаг (Y/N) отменён ли заказ;
           "STATUS_ID" => self::getStatusGSAB($orderInfo->PaymentStatus),   //код статуса заказа;
           "PRICE" => $orderInfo->Sum,                                      // общая стоимость заказа;
           "CURRENCY" => $orderInfo->ResolvedOrder->PaymentInfo->Curr,		//валюта стоимости заказа;
           "USER_ID" => $user_id,													//!!! код пользователя заказчика;
           "PAY_SYSTEM_ID" => 3,					//платежная система, которой (будет) оплачен заказа; http://gsa.lsup.ru/bitrix/admin/sale_pay_system.php?lang=ru
           "DELIVERY_ID" => self::getDeliveryID($orderInfo->ResolvedOrder->DeliveryInfo->DeliveryMethodID),          //способ (служба) доставки заказа;
           "PRICE_DELIVERY"=>doubleval($orderInfo->ResolvedOrder->DeliveryInfo->DeliveryMethod->Price),    //цена за доставку
           "USER_DESCRIPTION" => $orderInfo->ResolvedOrder->Comment,                            //произвольные комментарии;
		   "ADDITIONAL_INFO"=> $orderInfo->OID,
           );

		$orders['order'] = $arFields;
		/*echo "<pre>";
		print_r($arFields);
		print_r($value);
		echo "</pre>"; */
		$items = array();
		$items_tmp = $orderInfo->ResolvedOrder->ItemInfo;
		//echo "<pre>";print_r($items_tmp);echo "</pre><br/>";
		for($i=0;$i<count($items_tmp);$i++)
		{
			$items[$i] = array(
				//"PRODUCT_ID"=>$items_tmp[$i]->CID,   //уникальный в рамках модуля код товара (обязательное поле);
				"PRODUCT_ID"=>$items_tmp[$i]->ForeignID,   //уникальный в рамках модуля код товара (обязательное поле);
                   //PRODUCT_PRICE_ID - ID (идентификатор) конкретного ценового предложения товара, пришедшего в корзину. Может быть использован в классе CPrice модуля Catalog для получения детальной информации о цене.
                "PRICE"=>$items_tmp[$i]->Price,     // стоимость единицы товара (обязательное поле);
                "CURRENCY"=>$items_tmp[$i]->Curr,   //валюта стоимости единицы товара (обязательное поле), если валюта отличается от базовой валюты для данного сайта, то стоимость будет автоматически сконвертирована по текущему курсу;
                //WEIGHT - вес единицы товара;
                "QUANTITY"=>$items_tmp[$i]->Amount, //количество единиц товара;
                "LID"=>"s1",                       //сайт, на котором сделана покупка (обязательное поле);
                //DELAY - флаг "товар отложен" (Y/N);
                //CAN_BUY - флаг "товар можно купить" (Y/N) - может устанавливаться автоматически про наличии функции обратного вызова для поддержки актуальности корзины;
                "NAME"=>$items_tmp[$i]->Name,   //название товара (обязательное поле);
                //"ORDER_ID"=>$items_tmp[$i]->IID,   //идентификатор заказа. Ключ будет пустым, если товар еще не добавлен в заказ;
                "MODULE"=>"getshopapp",             //модуль, добавляющий товар в корзину;
				"NOTES" => "",
				"FUSER_ID" =>1,
                );
		}
		$orders['items'] = $items;
		return $orders;
	}



    //собирает необходимый заказ
    private function ResolveOrderComposer($tmp,$order,$user_id=0)
    {

    	self::writeToLog("ResolveOrderComposer(".json_encode($tmp).",".json_encode($order).")");

        $str = array();

        if(intval($user_id)==0)
        {
        	$str['FirstName']='admin';
        	$str['Phone']=79261061620;
    	}
    	else
    	{
    		$rsUser = CUser::GetByID($user_id);
    		$arUser = $rsUser->Fetch();
    		$str['FirstName']=(string)$arUser['NAME'];
        	$str['Phone']=preg_replace("/[^0-9]/","",$arUser['PERSONAL_PHONE']);
    	}    	

        $str['Comment']=$order['USER_DESCRIPTION'];

        $str['DeliveryInfo']['DateTime']=array(gmdate("Y-m-d\TH:i",strtotime($order['DATE_INSERT'])), gmdate("Y-m-d\TH:i",strtotime($order['DATE_UPDATE'])));
        $str['DeliveryInfo']['DeliveryProfileID']=self::findDelivery($order['DELIVERY_ID']);
        $str['DeliveryInfo']['UserAddressID']=0;
        //DeliveryMethod

        $str['PaymentInfo']['PaymentType']="Cash";
        $str['PaymentInfo']['Sum']=doubleval($order['PRICE']);
        $str['PaymentInfo']['Curr']=$order['CURRENCY'];
        $str['PaymentInfo']['NeedChange']=false;

        $str['ItemInfo'] = array();
        while($tp = $tmp->Fetch())
        {        	
            $product = array();
            $product['ForeignID']=intval($tp['ID']);
            $product['Name']=$tp['NAME'];
            $product['Price']=doubleval($tp['PRICE']);
            $product['Curr']=$tp['CURRENCY'];
            $product['Amount']=intval($tp['QUANTITY']);
            $str['ItemInfo'][]=$product;
        }           
        return $str;
    }

	//Сохранение результата запроса в случае неудачного коннекта к серверу
	//а также попытка отправки тех которые были уже сохранены
	private function saveSendRequest($oper,$str,$save=false)
	{				
		//тупо сохраняем, в противном случае просто отправляем запросы
		if($save)
		{
			$tmp_dir = $_SERVER['DOCUMENT_ROOT']."/bitrix/modules/gsa.modul/tmp";
			if(!is_dir($tmp_dir)) mkdir($tmp_dir);
			$fp = fopen($tmp_dir."/".time().'.txt', 'w');
			fwrite($fp,$oper."~~~".$str);
			fclose($fp);
		}

		//отправляем ранее не отправленные запросы
		if ($handle = opendir($tmp_dir))
			while (false !== ($entry = readdir($handle)))
				if($entry!='.' && $entry!='..')
				{
					$filename =  $tmp_dir."/".$entry;
					$handle = fopen($filename, "r");
					$contents = fread($handle, filesize($filename));
					fclose($handle);
					$tmp = explode("~~~",$contents);
					$resa = self::callApi($tmp[0], unserialize($tmp[1]));
					//echo $tmp[0]."<br/>";
					//!!!ЗАМЕНИТЬ - типа если запрос удался удаляем файл
					if(true) unlink($filename);
				}
	}

    /*
    "Saved" - принят GSA
    "Submitted" - отправлен магазину
    "Accepted" - принят магазином
    "Processing" - обрабатывается магазином
    "OnDelivery" - доставляется
    "PickupReady" - можно забрать из пункта самовывоза
    "Done" - заказ исполнен
    "Failed"
    "Rejected" - магазин не принял заказ
    */
    private function setStatus($status)
    {
        switch($status)
        {
            case 'N':           //[NAME] => Принят, ожидается оплата
                return "Accepted";
                break;
            case 'F':           //[NAME] => Выполнен
                return "Done";
                break;
            case 'P':            //[NAME] => Оплачен, формируется к отправке
                return "Processing";
                break;
        }
    }

    /*
    "None" - оплата не через GSA
    "Pending" - оплата ожидается
    "Received" - транзакция прошла успешно
    "Rejected" - транзакция отклонена
    "GatewayError" - ошибка связи с платежным шлюзом
    "InternalError" - внутренняя ошибка платежного модуля GSA
    "InvalidTransaction" - неверно сформирована транзакция
    "InsufficientFunds" - на карте недостаточно средств
    */
    private function setPayment($pay)
    {
        if($pay=='N') return 'Pending';
        else return 'Received';
    }

    private function getPayment($pay)
    {
        if($pay=='Received') return 'Y';
        return 'N';
    }

    private function getCanceled($stat)
    {
        if($stat=='Rejected') return 'Y';
        return 'N';
    }
    //получения нашего ид доставки по их
    private function getDeliveryID($id)
    {
        // echo "!!!".$id."<br/>";
        if(strlen($id)==0) return 0;
        $delivery = (array)self::callApi('DeliveryMethodsRead', array("ID"=>$id));
        if(!isset($delivery['DeliveryMethod'])) return 0;
        return $delivery['DeliveryMethod']->ForeignID;
        /*if(is_array($delivery))
        {
            echo "<pre>";
            print_r($delivery['DeliveryMethod']);
            echo "</pre>";
        }
        print_r($delivery);
        echo "!!!".$id."<br/>";*/
    }

    //поиск их деливери по нашему
    private function findDelivery($id)
    {
    	self::writeToLog("findDelivery(".($id).")");
        $delivery = (array)self::callApi('DeliveryMethodsReadAll', array());
        $arVariants = self::getDeliveryMethods();
        // print_r($arVariants);
        // echo "!!!".$id."<br/>";
        foreach($arVariants AS $key=>$value)
        {
            if(intval($value['ForeignID'])==intval($id))
                return $value['ID'];
        }
        return '0';
    }

	//получение общего списка статусов оплаты
	public static function getStatusList()
	{
		global $APPLICATION;
		global $MESS;
		return array(	"None" => GetMessage("GSA_PST1"),
						"Pending" => GetMessage("GSA_PST2"),
						"Received" => GetMessage("GSA_PST3"),
						"Rejected" => GetMessage("GSA_PST4"),
						"GatewayError" => GetMessage("GSA_PST5"),
						"InternalError" => GetMessage("GSA_PST6"),
						"InvalidTransaction" => GetMessage("GSA_PST7"),
						"InsufficientFunds" => GetMessage("GSA_PST8"));
	}

	//получение статусов заказов
	public static function getStatusOrder()
	{
		global $APPLICATION;
		global $MESS;
		return array(	"Saved" => GetMessage("GSA_OST1"),
						"Submitted" => GetMessage("GSA_OST2"),
						"Accepted" => GetMessage("GSA_OST3"),
						"Processing" => GetMessage("GSA_OST4"),
						"OnDelivery" => GetMessage("GSA_OST5"),
						"PickupReady" => GetMessage("GSA_OST6"),
						"Done" => GetMessage("GSA_OST7"),
						"Failed" => GetMessage("GSA_OST8"),
						"Rejected" => GetMessage("GSA_OST9")

						);
	}




	//исходя из стауса битрикса возвращает статус товара GSA
	public function getStatusBGSA($status)
	{
		$arStatus = COption::GetOptionString("gsa.modul", "STATUS");
		if($arStatus)
		{
			$arStatus = unserialize($arStatus);
			return $arStatus['status'][$status];
		}
		else return false;
	}

	//исходя из статуса товара GSA возвращает статус битрикса
	public function getStatusGSAB($status)
	{
		$arStatus = COption::GetOptionString("gsa.modul", "STATUS");
		if($arStatus)
		{
			$arStatus = unserialize($arStatus);
			foreach($arStatus['status_order'] AS $key=>$value)
			{
				//echo $value."---".$status."<br/>";
				if($value==$status)
					return $key;
			}
			return false;
		}
		else return false;
	}



    public static function getDeliveryMethods() {
       if (CModule::IncludeModule('iblock') && CModule::IncludeModule("catalog") && CModule::IncludeModule("sale")) {


                // echo "ZIP ".COption::GetOptionInt("sale", "location_zip");
                // echo "LOCATION ".COption::GetOptionInt("sale", "location");


                $currency = CSaleLang::GetLangCurrency(SITE_ID);
                $arVariants = array();

                $db_dtype = CSaleDelivery::GetList(
                    array(
                            "SORT" => "ASC",
                            "NAME" => "ASC"
                        ),
                    array(
                            // "LID" => SITE_ID,
                            "ACTIVE" => "Y"
                        ),
                    false,
                    false,
                    array()
                );
                while ($ar_dtype = $db_dtype->Fetch())
                {
                    if ($ar_dtype["PERIOD_FROM"] && $ar_dtype["PERIOD_TO"] > 0) {
                        $processingtime = ($ar_dtype["PERIOD_TYPE"] == "H") ? array($ar_dtype["PERIOD_FROM"], $ar_dtype["PERIOD_TO"]) : array($ar_dtype["PERIOD_FROM"]*24, $ar_dtype["PERIOD_TO"]*24);
                    }

                    $arVariants[$ar_dtype["ID"]] = array(
                        "ID"=>$ar_dtype["ID"],
                        "ForeignID" => $ar_dtype["ID"],
                        "Type"=>self::getDeliveryType($ar_dtype["ID"]),
                        "Name" => $ar_dtype["NAME"],
                        "Description" => strip_tags($ar_dtype["DESCRIPTION"]),
                        "Price" => $ar_dtype["PRICE"],
                        "Curr" => $currency,
                        "PaymentMethods" => array(),
                        "ProcessingTime" => $processingtime,
                        "Phone" => "",
                        "WorkTime" => "",
                        "Address" => "",
                        "Route" => "",
                        );
                }


                $arProfilesResult = array();
                //получаем список автоматизированных служб доставки с первичным ограничением по заказу
                $dbHandlers = CSaleDeliveryHandler::GetList(
                    array("SORT" => "ASC", "NAME" => "ASC"),
                    array("ACTIVE" => "Y")
                );


                while ($arHandler = $dbHandlers->GetNext()) { //для каждого обработчика
                    $arVariants[$arHandler["SID"]] = array(
                        "ID"=>$arHandler["SID"],
                        "ForeignID" => $arHandler["SID"],
                        "Type"=>self::getDeliveryType($arHandler["SID"]),
                        "Name" => $arHandler["NAME"],
                        "Description" => $arHandler["DESCRIPTION"]." - ".strip_tags(html_entity_decode($arHandler["DESCRIPTION_INNER"])),
                        "Price" => "",
                        "Curr" => $arHandler["BASE_CURRENCY"],
                        "PaymentMethods" => array(),
                        "ProcessingTime" => $processingtime,
                        "Phone" => "",
                        "WorkTime" => "",
                        "Address" => "",
                        "Route" => "",
                        );


                }

                if (count($arVariants) > 0) {
                    return $arVariants;
                } else {
                    return false;
                }
        } else {
            return false;
        }
    }

    public static function getDeliveryType($id) {
        $arDelivery = COption::GetOptionString("gsa.modul", "DELIVERY");
        $arDelivery = unserialize($arDelivery);
			if(!$arDelivery) $arDelivery = array();
        if (array_key_exists($id, $arDelivery)) {
            $type = $arDelivery[$id];
            switch ($type) {
                case 'n':
                    return false;
                case 'c':
                    return "CourierDelivery";
                case 'p':
                    return "PostDelivery";
                case 's':
                    return "PickupDelivery";
                default:
                    return false;
                    break;
            }
        } else {
            return false;
        }
    }

    public static function deliverySync() 
   	{
    	if(!self::getAuth()) return false;
    	self::writeToLog("deliverySync");

        /*$DeliveryMethodsReadAll = (array)self::callApi('DeliveryMethodsReadAll', array());  
        if(is_array($DeliveryMethodsReadAll['DeliveryMethods']) && count($DeliveryMethodsReadAll['DeliveryMethods'])>0)
       		foreach($DeliveryMethodsReadAll['DeliveryMethods'] AS $key=>$value)
       		{       	
       			$resa = self::callApi('DeliveryMethodsDelete', array("ID"=>$key));
       		}
        $DeliveryMethodsReadAll = (array)self::callApi('DeliveryMethodsReadAll', array());  
        print_r($DeliveryMethodsReadAll);
        
        exit;      
        if (!is_array($DeliveryMethodsReadAll["DeliveryMethods"]) || count($DeliveryMethodsReadAll["DeliveryMethods"])==0)
        {
        	;
        }*/


        $arVariants = self::getDeliveryMethods();

        $arIds = array();
        foreach ($DeliveryMethodsReadAll["DeliveryMethods"] as $value)
        {
            $arIds[] = $value->ID;
        }
        $lastId = max($arIds);
        if (!$lastId) $lastId = 0;

        $str = "";
        $res = "";

        $arToDelete = array();
        $arToUpdate = array();
        $arToCreate = array();
        $arVariantsGSA = array();
        foreach ($DeliveryMethodsReadAll["DeliveryMethods"] as $value) {
            $arVal = (array)$value;
            if ($arVal["ForeignID"] == null) {$arToDelete[] = $arVal["ID"]; continue;}
            if (!array_key_exists($arVal["ForeignID"], $arVariants)) {$arToDelete[] = $arVal["ID"]; continue;}
            $arVariantsGSA[$arVal["ForeignID"]] = $arVal;

            // $arRequest["ID"] =$arVal["ID"];
            // $id = $arVal["ID"];
            // $resa = self::callApi('DeliveryMethodsDelete', $arRequest);
            // if ($resa->OpStatus === 0) {$str .= "Служба доставки ID: {$id} удалена<br>";} else {$str .= "Ошибка удаления службы доставки {$id}<br>";};

        }

        // return $str;


        foreach ($arVariants as $forId => $var) {
            if (!$var["Type"]) continue;

            if (!array_key_exists($forId, $arVariantsGSA)) {
                $arToCreate[] = $var;
            }

            if (array_key_exists($forId, $arVariantsGSA)) {
                $arProps = array_keys($var);
                foreach ($arProps as $prop) {
                    if ($prop == "ID") continue;
                    if ($var[$prop] != $arVariantsGSA[$forId][$prop]) {
                       // $str .= "<br><br><br>-----------------------------------------------NOT OK---MY: {$var[$prop]} GSA: {$arVariantsGSA[$forId][$prop]}--------------------------------------------------<br><br><br>";
                        $var["ID"] = $arVariantsGSA[$forId]["ID"];
                        $arToUpdate[] = $var;
                        break;
                    }
                }
            }
        }

        foreach ($arToDelete as $id) {
            $arRequest["ID"] = $id;
            $resa = self::callApi('DeliveryMethodsDelete', $arRequest);
            if ($resa->OpStatus === 0) {$str .= "Служба доставки ID: {$id} удалена<br>";} else {$str .= "Ошибка удаления службы доставки {$id}<br>";};
        }

        foreach ($arToCreate as $req) {
           $arRequest["DeliveryMethod"] = array(
              "ID" => (int)$lastId + 1,
              "ForeignID" => $req["ForeignID"],
              "Type" => $req["Type"],
              "Name" => $req["Name"],
              "Description" => $req["Description"],
              "Price" => (double) $req["Price"],
              "Curr" => $req["Curr"],
              "PaymentMethods" => $req["PaymentMethods"],
              // "ProcessingTime" => $var["ProcessingTime"]
          );
          ob_start();$resa = self::callApi('DeliveryMethodsCreate', $arRequest);ob_clean();
          if ($resa->OpStatus === 0) {$str .= "Служба доставки {$req['Name']} добавлена<br>"; $lastId++;} else {$str .= "Ошибка синхронизации службы доставки {$req['Name']}<br>";};
        }
        foreach ($arToUpdate as $req) {
           $arRequest["DeliveryMethod"] = array(
              "ID" => $req["ID"],
              "ForeignID" => $req["ForeignID"],
              "Type" => $req["Type"],
              "Name" => $req["Name"],
              "Description" => $req["Description"],
              "Price" => (double) $req["Price"],
              "Curr" => $req["Curr"],
              "PaymentMethods" => $req["PaymentMethods"],
              // "ProcessingTime" => $var["ProcessingTime"]
          );
          $resa = self::callApi('DeliveryMethodsUpdate', $arRequest);
          if ($resa->OpStatus === 0) {$str .= "Служба доставки {$req['Name']} обновлена<br>";} else {$str .= "Ошибка обновления службы доставки {$req['Name']}<br>";};
        }


        //$str .= "<br><br><br>ITOG: TO DELETE: ".print_r($arToDelete, true)."<br><br><br> TOUPATE: ".print_r($arToUpdate, true)." <br><br><br> TO ADD: ".print_r($arToCreate,true);
        return $str;
    }

    public static function getCredentialsData() {
        $user_name = COption::GetOptionString("gsa.modul", "user_name");
        $user_pass = COption::GetOptionString("gsa.modul", "user_pass");
        $vendor = "gsa";

        if ($user_name && $user_pass && $vendor) {
            return array("Vendor" => $vendor, "Login" => $user_name, "Password" => $user_pass);
        } else {
            return false;
        }
    }

    //Производит авторизацию на сервере ГША и возвращает строку куки
    public static function getNewCookieString() {
        $credentials = self::getCredentialsData(); //получаем рег.инфо

        if (!$credentials) return false;

        $url = 'https://admin.getshopapp.com/api/api-dev/user/Auth/';
        $request = array(
            'Credentials'    => $credentials,
            "DID" => "",
            "Locale" => "ru_RU"
        );
        $request = json_encode($request);

        $options = array(
            'http' => array(
                'header'  => "Content-Type: application/json\r\n",
                'method'  => 'POST',
                'content' => $request,
            ),
        );
        $context  = stream_context_create($options); //формируем контекст запроса
        $result = file_get_contents($url, false, $context);
        //print_r($result);  ///ошибка авторизации
        if ($result) {
            $result = self::checkResult($result); //проверяем на ошибки

            if (is_array($result) && $result["STATE"] == "ERROR") {
                ShowError($result["MSG"]);
                return false;
            } else {
                $cookie = "";

                foreach ($http_response_header as $string) { //ищем в возвращенном заголовке информацию о куки
                    if (strpos($string, 'Set-Cookie: ') !== false) {
                        $cookie .= str_replace('Set-Cookie: ', '', $string).";";
                    }
                }

                if ($cookie) {
                    COption::SetOptionString("gsa.modul","cookie",$cookie);
                    return $cookie;
                } else {
                    COption::SetOptionString("gsa.modul","cookie",'');
                    return false;
                }
            }

        }

    }

    //берет строку куки из настроек модуля
    public static function getCookieString() {
        $cookie = COption::GetOptionString("gsa.modul", "cookie");
        if ($cookie) {
            return $cookie;
        } else {
            return false;
        }
    }

    //проверяет ответ сервера на предмет ошибок и выводит сообщение об ошибке или возвращает ответ
    private static function checkResult($result) {
    	global $APPLICATION;
    	global $MESS;
        $arMSG = array(GetMessage("GSA_OPSTATUS1"), GetMessage("GSA_OPSTATUS2"), GetMessage("GSA_OPSTATUS3"), GetMessage("GSA_OPSTATUS4"), GetMessage("GSA_OPSTATUS5"), GetMessage("GSA_OPSTATUS6"), GetMessage("GSA_OPSTATUS7"), GetMessage("GSA_OPSTATUS8"));
        try {
            $result = json_decode($result); 
            //exit("!!!!".$result);           
            if (!$result) return array("STATE" => "ERROR", "STATUS" => 999, "MSG" => GetMessage("GSA_OPSTATUS9"));
            if ($result->OpStatus !== 0) {
                return array("STATE" => "ERROR", "STATUS" => $result->OpStatus, "MSG" => $arMSG[$result->OpStatus]);
            } else {
                return $result;
            }
        } catch (Exception $e) {
            return array("STATE" => "ERROR", "STATUS" => 999, "MSG" => GetMessage("GSA_OPSTATUS9"));
        }
    }

    public static function StoresRead($cookie) {





    }

    public static function json_encode_cyr($str) {
        $arr_replace_utf = array('\u0410', '\u0430','\u0411','\u0431','\u0412','\u0432',
        '\u0413','\u0433','\u0414','\u0434','\u0415','\u0435','\u0401','\u0451','\u0416',
        '\u0436','\u0417','\u0437','\u0418','\u0438','\u0419','\u0439','\u041a','\u043a',
        '\u041b','\u043b','\u041c','\u043c','\u041d','\u043d','\u041e','\u043e','\u041f',
        '\u043f','\u0420','\u0440','\u0421','\u0441','\u0422','\u0442','\u0423','\u0443',
        '\u0424','\u0444','\u0425','\u0445','\u0426','\u0446','\u0427','\u0447','\u0428',
        '\u0448','\u0429','\u0449','\u042a','\u044a','\u042b','\u044b','\u042c','\u044c',
        '\u042d','\u044d','\u042e','\u044e','\u042f','\u044f');
        $arr_replace_cyr = array('А', 'а', 'Б', 'б', 'В', 'в', 'Г', 'г', 'Д', 'д', 'Е', 'е',
        'Ё', 'ё', 'Ж','ж','З','з','И','и','Й','й','К','к','Л','л','М','м','Н','н','О','о',
        'П','п','Р','р','С','с','Т','т','У','у','Ф','ф','Х','х','Ц','ц','Ч','ч','Ш','ш',
        'Щ','щ','Ъ','ъ','Ы','ы','Ь','ь','Э','э','Ю','ю','Я','я');

        //если не utf-8 то конвертируем в utf-8
        if(strtolower(SITE_CHARSET)!='utf-8')
       		array_walk_recursive($str, function(&$value, $key) 
       		{
       			if (is_string($value)) 
       			{
       				$value = $APPLICATION->ConvertCharset($value, SITE_CHARSET, 'utf-8');       				
       		    }
       		});




        $str1 = json_encode($str);
        $str2 = str_replace($arr_replace_utf,$arr_replace_cyr,$str1);        
    return $str2;
    }

    private function writeToLog($text='')
    {
    	$is_log = 0;
    	$write_log_status = COption::getOptionString("gsa.modul", "write_log");	
		if(intval($write_log_status)==1) $is_log=1;
		if($is_log==0) return false;

		$fp = fopen($_SERVER['DOCUMENT_ROOT'].'/gsa_main_log.txt', 'a+');
		fwrite($fp,date("d.m.Y H:i:s")." --- ".$text."\r\n");
		fclose($fp);
    }

    public static function callApi($functionName, $arArgs) 
   	{   		
   		self::writeToLog("callApi(".$functionName.",".json_encode($arArgs).")");
   		//return false;
    	global $APPLICATION;
    	global $MESS;    	
    	//echo $functionName."<br/>";
    	if($functionName!='OrderCreatedHook' && $functionName!='HooksReadAll' && $functionName!='HookDelete')
    		self::makeInit();

    	//логирование запросов
    	$fp = fopen($_SERVER['DOCUMENT_ROOT'].'/gsa_request.txt', 'a+');
		fwrite($fp, $functionName." --- ".json_encode($arArgs)."\n");
		fclose($fp);
		//print_r($arArgs);return false;
		//echo SITE_CHARSET;return false;

        $cookie = self::getCookieString();
        if (!$cookie) {
            $cookie = self::getNewCookieString();
            if (!$cookie) {
                ShowError(GetMessage("GSA_SOMEERROR1"));
                return false;
            }
        }

        if (!array_key_exists($functionName, self::$arFunctions)) {
            ShowError(GetMessage("GSA_SOMEERROR2").$functionName);
        } else {
            $arSettings = self::$arFunctions[$functionName];
        }

        $url = $arSettings['URL'];
        $method = $arSettings['METHOD'];
        if ($arArgs === false || count($arArgs) == 0) {
            $request = '';
        } else {
            $request = self::json_encode_cyr($arArgs);
        }
       
       // echo $request;return false;
        $options = array(
            'http' => array(
                'header'  => "Content-Type: application/json\r\n".
                             "Cookie: ".$cookie."\r\n",
                'method'  => $method,
                'content' => $request,
            ),
        );

        // echo "URLLLL: ".$url;

        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        //print_r($result);
        //print_r($context);return false;
        //exit;
        //echo "OPTIONS: ".print_r($options,true);
        //echo "<br>RESULT: ".print_r($result,true);
        //var_dump($http_response_header);
        $result = self::checkResult($result); //проверяем на ошибки
        //self::writeToLog("callApiRETURN(".serialize($result).")");
        

        if (is_array($result) && $result["STATE"] == "ERROR") {
            if ($result["STATUS"] == 1) { //скорее всего не прошла кука, пытаемся получить новую куку и повторить запрос

                //echo "<br>--------------------<br>SECOND TIME";
                $cookie = self::getNewCookieString(); //ставим новую куку
                $options = array(
                    'http' => array(
                        'header'  => "Content-Type: application/json\r\n".
                                     "Cookie: ".$cookie."\r\n",
                        'method'  => $method,
                        'content' => $request,
                    ),
                );

                $context  = stream_context_create($options);
                $result = file_get_contents($url, false, $context);
                $result = self::checkResult($result); //проверяем на ошибки

                if (is_array($result) && $result["STATE"] == "ERROR") { //если ошибка, то ничего больше не делаем
                    ShowError($result["MSG"]);
                    return false;
                } else {
                    return $result;
                }

            } else {
                ShowError($result["MSG"]);
                return false;
            }
        } else {
            return $result;
        }

    }



    public static function StoresCheckDomain($domain) {
        $url = 'https://api.getshopapp.com/v1/cms/Stores/CheckDomain/';
        $request = array(
            'Domain' => "bitrixtest"
        );
        $request = json_encode($request);
        //$data = array('key1' => 'value1', 'key2' => 'value2');
        //echo $request;
        //echo "----------";
        // use key 'http' even if you send the request to https://...
        $options = array(
            'http' => array(
                'header'  => "Content-Type: application/json\r\n",
                'method'  => 'POST',
                'content' => $request,
            ),
        );
        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        //var_dump($http_response_header);
        //var_dump($result);

        return $result;
    }


    ///cms/Stores/CheckDomain/
    //https://admin.getshopapp.com/api/api-dev/user/Auth/

    // {"Credentials":{"Vendor":"gsa","Login":"dsagasdgsdgas","Password":""},"DID":"66a037a0657211e3b9e42f7267e826ff","Locale":"ru_RU"}
    // {"Credentials":{"Vendor":"gsa","Login":"sfdf","Password":"sdf"}}
}