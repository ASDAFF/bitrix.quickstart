<?

class CMailTrigEventsHandler
{
	function __construct()
	{
	}

	private static function getVisitorId()
	{
		global $APPLICATION;

		$customerId = $APPLICATION->get_cookie("MAILTRIG_CUSTOMER_ID");
		if(empty($customerId) || intval($customerId) <= 0)
		{
			// get next customerId from options
			$optionsCustomerId = COption::GetOptionInt("mailtrig.events", "CUSTOMER_ID");

			if(intval($optionsCustomerId) <= 0)
			{
				$optionsCustomerId = 0;
			}
			$optionsCustomerId++;

			COption::SetOptionInt("mailtrig.events", "CUSTOMER_ID", $optionsCustomerId);

			$customerId = $optionsCustomerId;
		}

		$APPLICATION->set_cookie("MAILTRIG_CUSTOMER_ID", intval($customerId), time() + 60*60*24*365*2);

		return $customerId;
	}

	private static function sendVisitEvent($arParams = array(), $bCheckVisit = true)
	{
		global $APPLICATION, $USER;

		$bCheckCustomer = (intval($APPLICATION->get_cookie("MAILTRIG_CUSTOMER_ID")) > 0)?true:false;

		$customerId = self::getVisitorId();

		// check last visit
		$lastVisit = $APPLICATION->get_cookie("LAST_VISIT");
		if(empty($lastVisit))
		{
			$lastVisit = date("d.m.Y H:i:s",time());
		}

		$lastVisit = ConvertDateTime($lastVisit, "YYYY.MM.DD");

		$lastVisitTimeStamp = MakeTimeStamp($lastVisit, "YYYY.MM.DD");

		$bDebugMode = (COption::GetOptionString("mailtrig.events", "debug_mode") == "Y")?true:false;
		if($bDebugMode) {
			$arDebug = array(
				"bCheckVisit" => $bCheckVisit,
				"bCheckCustomer" => $bCheckCustomer,
				"lastVisitTimeStamp" => $lastVisitTimeStamp,
				"currentTimeStamp" => MakeTimeStamp(date("Y.m.d"), "YYYY.MM.DD"),
				"check" => ($lastVisitTimeStamp < MakeTimeStamp(date("Y.m.d"), "YYYY.MM.DD") || !$bCheckCustomer)
			);

			//CMailTrigLogger::debug("before_visit_send", $arDebug);
		}

		if($bCheckVisit)
		{
			if($lastVisitTimeStamp < MakeTimeStamp(date("Y.m.d"), "YYYY.MM.DD") || !$bCheckCustomer)
			{
				// send event
				if($USER->IsAuthorized())
				{
					$arUser["email"] = $USER->GetEmail();
					$arUser["bitrix_id"] = $USER->GetID();
				}
				else
				{
					if(CModule::IncludeModule("subscribe"))
					{
						$email = $APPLICATION->get_cookie("SUBSCR_EMAIL");
						if(check_email($email))
							$arUser["email"] = $email;
					}
				}

				$arEvent = array(
					array(
						"_event",
						array(
							"name" => "visit"
						)
					)
				);


				$client = new CMailTrigClient();
				$arResult = $client->sendEvent($arEvent, $customerId, $arUser);
			}
		}
		else
		{
			// send event
			if($USER->IsAuthorized())
			{
				$arUser["email"] = $USER->GetEmail();
				$arUser["bitrix_id"] = $USER->GetID();
			}
			else
			{
				if(CModule::IncludeModule("subscribe"))
				{
					$email = $APPLICATION->get_cookie("SUBSCR_EMAIL");
					if(check_email($email))
						$arUser["email"] = $email;
				}
			}

			$arEvent = array(
				array(
					"_event",
					array(
						"name" => "visit"
					)
				)
			);

			$client = new CMailTrigClient();
			$arResult = $client->sendEvent($arEvent, $customerId, $arUser);
		}
	}

	public static function onEpilogHandler()
	{
		global $APPLICATION, $USER;

		$APPLICATION->set_cookie("LAST_VISIT", date("d.m.Y H:i:s",time()));

		// check admin panel
		if(defined("ADMIN_SECTION") && ADMIN_SECTION === true)
			return;

		// some additional params
		$arParams = array();
		self::sendVisitEvent($arParams);

		// check session events
		session_start();
		if(array_key_exists("MAILTRIG_EVENTS", $_SESSION))
		{
			$customerId = self::getVisitorId();

			$arUser = array();
			if($USER->IsAuthorized())
			{
				$arUser = array(
					"email" => $USER->GetEmail()
				);
			}
			else
			{
				if(CModule::IncludeModule("subscribe"))
				{
					$email = $APPLICATION->get_cookie("SUBSCR_EMAIL");
					if(check_email($email))
						$arUser = array(
							"email" => $email
						);
				}
			}

			if(array_key_exists("CART", $_SESSION["MAILTRIG_EVENTS"]) && CModule::IncludeModule("sale"))
			{
				$arEvent = array();

				$arCartEvents = $_SESSION["MAILTRIG_EVENTS"]["CART"];

				if(array_key_exists("ADD", $arCartEvents))
				{
					foreach($arCartEvents["ADD"] as $key => $arValue)
					{
						$arEvent[] = array(
							"_event",
							$arValue
						);
					}
				}

				if(array_key_exists("UPDATE", $arCartEvents))
				{
					$arBasketID = array();
					foreach($arCartEvents["UPDATE"] as $key => $arValue)
					{
						$arBasketID[] = $arValue["cart_id"];
					}


					$resBasket = CSaleBasket::GetList(
						array(),
						array(
							"ID" => $arBasketID
						),
						false,
						false,
						array("ID", "PRODUCT_ID")
					);
					$arBasket = array();
					while($ar = $resBasket->Fetch())
					{
						$arBasket[$ar["ID"]] = $ar["PRODUCT_ID"];
					}

					foreach($arCartEvents["UPDATE"] as $key => $arValue)
					{
						$arValue["product_id"] = $arBasket[$arValue["cart_id"]];
						$arEvent[] = array(
							"_event",
							$arValue
						);
					}
				}

				if(array_key_exists("DELETE", $arCartEvents))
				{
					foreach($arCartEvents["DELETE"] as $key => $arValue)
					{
						$arEvent[] = array(
							"_event",
							$arValue
						);
					}
				}

				unset($_SESSION["MAILTRIG_EVENTS"]["CART"]);

				$client = new CMailTrigClient();
				$arResult = $client->sendEvent($arEvent, $customerId, $arUser, "post");
			}
		}
	}

	public static function onAfterUserAuthorizeHandler()
	{
		// some additional params
		$arParams = array();
		self::sendVisitEvent($arParams, false);
	}

	public static function onAfterUserRegisterHandler(&$arFields)
	{
		$customerId = self::getVisitorId();

		$arUser = array(
			"email" => $arFields["EMAIL"],
			"bitrix_id" => $arFields["USER_ID"]
		);

		$arEvent = array(
			array(
				"_event",
				array(
					"name" => "register"
				)
			)
		);

		$client = new CMailTrigClient();
		$arResult = $client->sendEvent($arEvent, $customerId, $arUser);
	}

	public static function onBeforeUserUpdateHandler(&$arFields)
	{
		global $APPLICATION, $USER;

		$customerId = self::getVisitorId();

		$resUserData = CUser::GetByID($arFields["ID"]);
		if($arUserData = $resUserData->Fetch())
		{
			if($arUserData["EMAIL"] !== $arFields["EMAIL"])
			{
				// send event update user
				if($USER->IsAuthorized() && $USER->GetEmail() == $arUserData["EMAIL"])
				{
					$arUser["email"] = $arFields["EMAIL"];
					$arUser["bitrix_id"] = $USER->GetID();
				}
				else
				{
					if(CModule::IncludeModule("subscribe"))
					{
						$email = $APPLICATION->get_cookie("SUBSCR_EMAIL");
						if(check_email($email))
							$arUser["email"] = $email;
					}
				}

				$arEvent = array(
					array(
						"_event",
						array(
							"name" => "visit"
						)
					)
				);

				$client = new CMailTrigClient();
				$arResult = $client->sendEvent($arEvent, $customerId, $arUser);
			}
		}
	}

	public static function onBasketAddHandler($id, $arFields)
	{
		global $USER;

		if(!CModule::IncludeModule("sale"))
			return;

		session_start();
		if(!array_key_exists("MAILTRIG_EVENTS", $_SESSION))
			$_SESSION["MAILTRIG_EVENTS"] = array();

		if(!array_key_exists($id, $_SESSION["MAILTRIG_EVENTS"]["CART"]["ADD"]) || empty($_SESSION["MAILTRIG_EVENTS"]["CART"]["ADD"]))
		{
			$_SESSION["MAILTRIG_EVENTS"]["CART"]["ADD"][$id] = array(
				"name" => "add_to_cart",
				"cart_id" => $id,
				"product_id" => $arFields["PRODUCT_ID"],
				"summ" => floatval($arFields["QUANTITY"] * $arFields["PRICE"])
			);
		}

		/*
		$customerId = self::getVisitorId();

		$arUser = array();
		if($USER->IsAuthorized())
		{
			$arUser = array(
				"email" => $USER->GetEmail()
			);
		}

		$arEvent = array(
			"cart_id" => $id,
			"product_id" => $arFields["PRODUCT_ID"],
			"summ" => floatval($arFields["QUANTITY"] * $arFields["PRICE"])
		);

		$client = new CMailTrigClient();
		$arResult = $client->sendEvent("add_to_cart", $customerId, $arUser, $arEvent);
		*/
	}

	public static function onBeforeBasketDeleteHandler($id)
	{
		global $USER;

		if(!CModule::IncludeModule("sale"))
			return;

		$arBasket = CSaleBasket::GetByID($id);

		session_start();
		if(!array_key_exists("MAILTRIG_EVENTS", $_SESSION))
			$_SESSION["MAILTRIG_EVENTS"] = array();

		if(!array_key_exists($id, $_SESSION["MAILTRIG_EVENTS"]["CART"]["DELETE"]) || empty($_SESSION["MAILTRIG_EVENTS"]["CART"]["DELETE"]))
		{
			$_SESSION["MAILTRIG_EVENTS"]["CART"]["DELETE"][$id] = array(
				"name" => "delete_from_cart",
				"cart_id" => $id,
				"product_id" => $arBasket["PRODUCT_ID"],
				"summ" => floatval($arBasket["QUANTITY"] * $arBasket["PRICE"])
			);
		}

		/*
		$customerId = self::getVisitorId();

		$arUser = array();
		if($USER->IsAuthorized())
		{
			$arUser = array(
				"email" => $USER->GetEmail()
			);
		}

		$arBasket = CSaleBasket::GetByID($id);

		$arEvent = array(
			"cart_id" => $id,
			"product_id" => $arBasket["PRODUCT_ID"],
			"summ" => floatval($arBasket["QUANTITY"] * $arBasket["PRICE"])
		);

		$client = new CMailTrigClient();
		$arResult = $client->sendEvent("delete_from_cart", $customerId, $arUser, $arEvent);
		*/
	}

	public static function onBasketUpdateHandler($id, $arFields)
	{
		global $USER;

		if(!CModule::IncludeModule("sale"))
			return;

		$bDebugMode = (COption::GetOptionString("mailtrig.events", "debug_mode") == "Y")?true:false;
		if($bDebugMode) {
			$arDebug = array(
				"arFields" => $arBasket
			);
		//	CMailTrigLogger::debug("before_basket_update", $arDebug);
		}

		if(!array_key_exists($id, $_SESSION["MAILTRIG_EVENTS"]["CART"]["UPDATE"]) || empty($_SESSION["MAILTRIG_EVENTS"]["CART"]["UPDATE"]))
		{
			//$arBasket = CSaleBasket::GetByID($id);

			$_SESSION["MAILTRIG_EVENTS"]["CART"]["UPDATE"][$id] = array(
				"name" => "cart_update",
				"cart_id" => $id,
				//"product_id" => $arBasket["PRODUCT_ID"],
				"summ" => floatval($arFields["QUANTITY"] * $arFields["PRICE"])
			);
		}
		/*
		$customerId = self::getVisitorId();

		$arUser = array();
		if($USER->IsAuthorized())
		{
			$arUser = array(
				"email" => $USER->GetEmail()
			);
		}

		$arEvent = array(
			"cart_id" => $id,
			"product_id" => $arBasket["PRODUCT_ID"],
			"summ" => floatval($arFields["QUANTITY"] * $arFields["PRICE"])
		);

		$client = new CMailTrigClient();
		$arResult = $client->sendEvent("cart_update", $customerId, $arUser, $arEvent);
		*/
	}

	public static function orderStartHandler($id, $arOrder)
	{
		global $APPLICATION, $USER;

		if(!CModule::IncludeModule("sale"))
			return;

		$customerId = self::getVisitorId();

		$arUser = array();
		if($USER->IsAuthorized())
		{
			$arUser = array(
				"email" => $USER->GetEmail()
			);
		}
		else
		{
			if(CModule::IncludeModule("subscribe"))
			{
				$email = $APPLICATION->get_cookie("SUBSCR_EMAIL");
				if(check_email($email))
					$arUser["email"] = $email;
			}
		}

		$arEvent = array(
			array(
				"_event",
				array(
					"name" => "purchase_start",
					"order_id" => $id,
					"summ" => $arOrder["PRICE"]
				)
			)
		);

		$client = new CMailTrigClient();
		$arResult = $client->sendEvent($arEvent, $customerId, $arUser);
	}

	public static function orderFinishHandler($id, $flag)
	{
		global $USER;

		if(!CModule::IncludeModule("sale"))
			return;

		$arOrder = CSaleOrder::GetByID($id);

		if($USER->GetID() == $arOrder["USER_ID"])
		{
			$arUser = array(
				"email" => $USER->GetEmail()
			);
		}
		else
		{
			$resUser = CUser::GetByID($arOrder["USER_ID"]);
			if($arUser = $resUser->Fetch())
			{
				$arUser = array(
					"email" => $arUser["EMAIL"]
				);
			}
		}

		$arEvent = array(
			array(
				"_event",
				array(
					"name" => "purchase_finish",
					"order_id" => $id,
					"summ" => $arOrder["PRICE"]
				)
			)
		);

		$client = new CMailTrigClient();
		$arResult = $client->sendEvent($arEvent, "", $arUser);
	}
}
?>