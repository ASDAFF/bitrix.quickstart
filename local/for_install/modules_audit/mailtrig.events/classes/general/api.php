<?

class CMailTrigAPI
{
	protected $arAvailableMethods = array(
		"get_user",
		"get_last_login_user",
		"get_basket",
		"get_forgotten_basket",
		"get_sale_stat",
		"get_order",
		"get_orders",
		"get_current_cart",
		"get_info"
	);
	protected $arStatuses = array(
		200 => "Ok",
		400 => "Unknown error",
		401 => "Unknown method",
		402 => "Wrong AppId",
		403 => "Wrong email",
		404 => "User not found",
		405 => "Basket item not found",
		406 => "Order not found",

		440 => "Wrong format"
	);
	protected $appId;

	public $arResult = array(
		"status" => 200,
		"data" => array()
	);

	private function checkAvailableMethod($sMethod)
	{
		if(!in_array($sMethod, $this->arAvailableMethods))
		{
			$this->arResult["status"] = 401;
			return false;
		}

		return true;
	}

	private function checkAppId($appId)
	{
		$appId = trim($appId);
		if(strlen($appId) == 0)
		{
			$this->arResult["status"] = 401;
			return false;
		}

		$this->appId = COption::GetOptionString("mailtrig.events", "appId");

		if($this->appId !== $appId)
		{
			$this->arResult["status"] = 401;
			return false;
		}

		return true;
	}

	private function setStatus()
	{
		$this->arResult["data"]["status"] = $this->arStatuses[$this->arResult["status"]];
	}

	private function setCustomStatus($arStatus = array())
	{
		if(empty($arStatus))
			return;

		if(!is_array($arStatus))
			$arStatus = array(
				$arStatus
			);

		if(!$this->arResult["data"])
		{
			$this->arResult["data"]["custom_statuses"] = array();
		}
		foreach($arStatus as $value)
			$this->arResult["data"]["custom_statuses"][] = $value;
	}

	public function getUser($email, $id = 0)
	{
		if(!check_email($email))
		{
			$this->arResult["status"] = 403;
			return false;
		}

		$arFilter = array(
			"ACTIVE" => "Y",
			"=EMAIL" => $email
		);
		if(intval($id) > 0)
			$arFilter["ID"] = intval($id);

		$resUser = CUser::GetList(
			$by = "id",
			$order = "asc",
			$arFilter
		);
		if($arUser = $resUser->Fetch())
		{
			$this->arResult["data"]["data"] = array(
				"id" => $arUser["ID"],
				"login" => $arUser["LOGIN"],
				"email" => $arUser["EMAIL"],
				"name" => $arUser["NAME"],
				"last_name" => $arUser["LAST_NAME"],
				"second_name" => $arUser["SECOND_NAME"],
				"birthday" => $arUser["PERSONAL_BIRTHDAY"],
				"date_register" => $arUser["DATE_REGISTER"],
				"phone" => $arUser["PERSONAL_PHONE"],
				"gender" => $arUser["PERSONAL_GENDER"]
			);
			return true;
		}
		else
		{
			$this->arResult["status"] = 404;
			return false;
		}

		return true;
	}

	public function getLastLoginUser()
	{
		$arFilter = array(
			"ACTIVE" => "Y"
		);

		$resUser = CUser::GetList(
			$by = "last_login",
			$order = "desc",
			$arFilter
		);
		if($arUser = $resUser->Fetch())
		{
			$this->arResult["data"]["data"] = array(
				"id" => $arUser["ID"],
				"login" => $arUser["LOGIN"],
				"email" => $arUser["EMAIL"],
				"name" => $arUser["NAME"],
				"last_name" => $arUser["LAST_NAME"],
				"second_name" => $arUser["SECOND_NAME"],
				"birthday" => $arUser["PERSONAL_BIRTHDAY"],
				"date_register" => $arUser["DATE_REGISTER"],
				"phone" => $arUser["PERSONAL_PHONE"],
				"gender" => $arUser["PERSONAL_GENDER"]
			);
			return true;
		}
		else
		{
			$this->arResult["status"] = 404;
			return false;
		}

		return true;
	}

	public function getBasketItem($id)
	{
		$id = intval($id);

		if($id <= 0)
		{
			$this->arResult["status"] = 405;
			return false;
		}

		if(!CModule::IncludeModule("sale") || !CModule::IncludeModule("catalog") || !CModule::IncludeModule("iblock"))
		{
			$this->arResult["status"] = 400;
			return false;
		}

		$arBasketItem = CSaleBasket::GetByID($id);

		if(empty($arBasketItem) || !is_array($arBasketItem))
		{
			$this->arResult["status"] = 405;
			return false;
		}

		$resProduct = CIBlockElement::GetByID($arBasketItem["PRODUCT_ID"]);
		if($arProduct = $resProduct->GetNext())
		{
			// check catalog
			$arCatalog = CCatalog::GetByID($arProduct["IBLOCK_ID"]);
			if(is_array($arCatalog))
			{
				// check if it is offers iblock
				if($arCatalog["OFFERS"] == "Y")
				{
					// find product element
					$resProp = CIBlockElement::GetProperty(
						$arProduct["IBLOCK_ID"],
						$arProduct["ID"],
						"SORT",
						"ASC",
						array("ID" => $arCatalog["SKU_PROPERTY_ID"])
					);
					$arProp = $resProp->Fetch();
					if($arProp && $arProp["VALUE"] > 0)
					{
						$iElementId = $arProp["VALUE"];
						$iIBlockId = $arCatalog["PRODUCT_IBLOCK_ID"];
						$iOffersIBlockId = $arCatalog["IBLOCK_ID"];
						$iOffersPropertyId = $arCatalog["SKU_PROPERTY_ID"];

						$resProductSKUProp = CIBlockElement::GetProperty(
							$iOffersIBlockId,
							$arProduct["ID"],
							array(),
							array(
								"ID" => $iOffersPropertyId
							)
						);
						if($arProductSKUProp = $resProductSKUProp->Fetch())
						{
							$resElement = CIBlockElement::GetByID($arProductSKUProp["VALUE"]);
							$arElement = $resElement->GetNext();

							if($arProduct["PREVIEW_PICTURE"] > 0)
								$arElement["PREVIEW_PICTURE"] = $arProduct["PREVIEW_PICTURE"];
						}
					}
				}
				//or it's regular catalog
				else
				{
					$arElement = $arProduct;
				}
			}
		}

		if($arElement["PREVIEW_PICTURE"] > 0 && !is_array($arElement["PREVIEW_PICTURE"]))
		{
			$arElement["PREVIEW_PICTURE"] = CFile::GetFileArray($arElement["PREVIEW_PICTURE"]);
		}

		// get server url from module options

		$sServerUrl = "http://" . COption::GetOptionString("mailtrig.events", "server_name");
		$sServerUrl .= ($_SERVER["SERVER_PORT"] != 80)?':'.$_SERVER["SERVER_PORT"]:'';

		$this->arResult["data"]["data"] = array(
			"cart_id" => $arBasketItem["ID"],
			"quantity" => $arBasketItem["QUANTITY"],
			"summ" => $arBasketItem["QUANTITY"] * $arBasketItem["PRICE"],
			"name" => $arBasketItem["NAME"],
			"detail_page_url" => $sServerUrl.$arElement["DETAIL_PAGE_URL"],
			"preview_text" => $arElement["PREVIEW_TEXT"],
			"preview_picture" => $sServerUrl.$arElement["PREVIEW_PICTURE"]["SRC"]
		);

		return true;
	}

	public function getForgottenBasket($dateFrom, $dateTo)
	{
		global $DB;

		if(!CModule::IncludeModule("sale"))
		{
			$this->arResult["status"] = 400;
			return false;
		}

		$dateFrom = trim($dateFrom);
		$dateTo = trim($dateTo);

		if(strlen($dateTo) > 0)
		{
			if ($arDate = ParseDateTime($dateTo, CSite::GetDateFormat("FULL", SITE_ID)))
			{
				if (strlen($dateTo) < 11)
				{
					$arDate["HH"] = 23;
					$arDate["MI"] = 59;
					$arDate["SS"] = 59;
				}

				$dateTo = date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL", SITE_ID)), mktime($arDate["HH"], $arDate["MI"], $arDate["SS"], $arDate["MM"], $arDate["DD"], $arDate["YYYY"]));
				$dateToTimeStamp = MakeTimeStamp($dateTo, CSite::GetDateFormat("FULL", SITE_ID));
				$arFilter["<=DATE_UPDATE"] = $dateTo;
			}
			else
			{
				$dateTo = "";
			}
		}

		if(strlen($dateTo) == 0)
		{
			$dateToTimeStamp = time();
		}

		if(strlen($dateFrom) == 0)
		{
			$dateFromTimeStamp = AddToTimeStamp(array(
				"MM" => -6
			), $dateToTimeStamp);
			$dateFrom = date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL", SITE_ID)), $dateFromTimeStamp);
		}
		else
		{
			if($arDate = ParseDateTime($dateFrom, CSite::GetDateFormat("FULL", SITE_ID)))
			{

				$dateFrom = date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL", SITE_ID)), mktime(0, 0, 0, $arDate["MM"], $arDate["DD"], $arDate["YYYY"]));
				//$arFilter[">=DATE_UPDATE"] = $dateFrom;
				$dateFromTimeStamp = MakeTimeStamp($dateFrom, CSite::GetDateFormat("FULL", SITE_ID));
				if($dateFromTimeStamp >= $dateToTimeStamp)
				{
					$dateFromTimeStamp = AddToTimeStamp(array(
						"DD" => -1
					), $dateToTimeStamp);
				}
				$dateFrom = date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL", SITE_ID)), $dateFromTimeStamp);
			}
			else
			{
				$dateFrom = "";
			}
		}

		$this->arResult["data"]["data"] = array(
			"count" => 0,
			"sum" => 0,
			"avg" => 0
		);

		$arSort = array();
		$arFilter = array(
			"ORDER_ID" => false,
		);
		if(strlen($dateFrom) > 0)
			$arFilter[">=DATE_UPDATE"] = $dateFrom;
		if(strlen($dateTo) > 0)
			$arFilter["<=DATE_UPDATE"] = $dateTo;

		$arGroup = array(
			"FUSER_ID",
			"SUM" => "PRICE"
		);
		$arSelect = array(
			"ID", "FUSER_ID"
		);
		$resBasket = CSaleBasket::GetList(
			$arSort,
			$arFilter,
			$arGroup,
			false,
			$arSelect
		);
		while($arBasket = $resBasket->Fetch()) {
			$this->arResult["data"]["data"]["count"]++;
			$this->arResult["data"]["data"]["sum"] += $arBasket["PRICE"];
		}

		if($this->arResult["data"]["data"]["count"] > 0)
			$this->arResult["data"]["data"]["avg"] = $this->arResult["data"]["data"]["sum"] / $this->arResult["data"]["data"]["count"];

		return true;
	}

	public function getSaleStat($dateFrom = "", $dateTo = "")
	{
		global $DB;

		if(!CModule::IncludeModule("sale"))
		{
			$this->arResult["status"] = 400;
			return false;
		}

		$dateFrom = trim($dateFrom);
		$dateTo = trim($dateTo);

		if(strlen($dateTo) > 0)
		{
			if ($arDate = ParseDateTime($dateTo, CSite::GetDateFormat("FULL", SITE_ID)))
			{
				if (strlen($dateTo) < 11)
				{
					$arDate["HH"] = 23;
					$arDate["MI"] = 59;
					$arDate["SS"] = 59;
				}

				$dateTo = date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL", SITE_ID)), mktime($arDate["HH"], $arDate["MI"], $arDate["SS"], $arDate["MM"], $arDate["DD"], $arDate["YYYY"]));
				$dateToTimeStamp = MakeTimeStamp($dateTo, CSite::GetDateFormat("FULL", SITE_ID));
				$arFilter["<=DATE_UPDATE"] = $dateTo;
			}
			else
			{
				$dateTo = "";
			}
		}

		if(strlen($dateTo) == 0)
		{
			$dateToTimeStamp = time();
		}

		if(strlen($dateFrom) == 0)
		{
			$dateFromTimeStamp = AddToTimeStamp(array(
				"MM" => -6
			), $dateToTimeStamp);
			$dateFrom = date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL", SITE_ID)), $dateFromTimeStamp);
		}
		else
		{
			if($arDate = ParseDateTime($dateFrom, CSite::GetDateFormat("FULL", SITE_ID)))
			{

				$dateFrom = date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL", SITE_ID)), mktime(0, 0, 0, $arDate["MM"], $arDate["DD"], $arDate["YYYY"]));
				$dateFromTimeStamp = MakeTimeStamp($dateFrom, CSite::GetDateFormat("FULL", SITE_ID));
				if($dateFromTimeStamp >= $dateToTimeStamp)
				{
					$dateFromTimeStamp = AddToTimeStamp(array(
						"DD" => -1
					), $dateToTimeStamp);
				}
				$dateFrom = date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL", SITE_ID)), $dateFromTimeStamp);
			}
			else
			{
				$dateFrom = "";
			}
		}

		$this->arResult["data"]["data"] = array(
			"count" => 0,
			"sum" => 0
		);

		$arSort = array("ID" => "DESC");
		$arFilter = array(
			"LID" => SITE_ID,
			"PAYED" => "Y"
		);
		if(strlen($dateFrom) > 0)
			$arFilter[">=DATE_PAYED"] = $dateFrom;
		if(strlen($dateTo) > 0)
			$arFilter["<=DATE_PAYED"] = $dateTo;

		$arSelect = array(
			"ID", "PRICE", "PRICE_DELIVERY"
		);

		$resOrders = CSaleOrder::GetList(
			$arSort,
			$arFilter,
			false,
			false,
			$arSelect
		);
		while($arOrder = $resOrders->Fetch()) {
			$this->arResult["data"]["data"]["count"]++;
			$this->arResult["data"]["data"]["sum"] += $arOrder["PRICE"] - $arOrder["PRICE_DELIVERY"];
		}

		$this->arResult["data"]["data"]["sum"] = floatval($this->arResult["data"]["data"]["sum"]);

		return true;
	}

	public function getOrder($id)
	{
		$id = intval($id);

		if($id <= 0)
		{
			$this->arResult["status"] = 406;
			return false;
		}

		if(!CModule::IncludeModule("sale") || !CModule::IncludeModule("catalog") || !CModule::IncludeModule("iblock"))
		{
			$this->arResult["status"] = 400;
			return false;
		}

		$arOrder = CSaleOrder::GetByID($id);

		if(empty($arOrder) || !is_array($arOrder))
		{
			$this->arResult["status"] = 406;
			return false;
		}

		$resOrderProps = CSaleOrderPropsValue::GetOrderProps($arOrder["ID"]);
		$arOrderProps = array();
		$arProps = array();
		while($ar = $resOrderProps->Fetch())
		{
			$arProps[] = $ar;

			$arOrderProps[] = array(
				$ar["NAME"] => $ar["VALUE"]
			);
		}

		if(!empty($arProps))
			$arOrder["PROPS"] = $arProps;

		$arStatus = CSaleStatus::GetByID($arOrder["STATUS_ID"]);

		$resUser = CUser::GetByID($arOrder["USER_ID"]);
		$arUser = $resUser->Fetch();

		$this->arResult["data"]["data"] = array(
			"order_id" => $arOrder["ID"],
			"payed" => $arOrder["PAYED"],
			"date_payed" => $arOrder["DATE_PAYED"],
			"cancel" => $arOrder["CANCELED"],
			"status" => $arStatus["NAME"],
			"status_description" => $arStatus["DESCRIPTION"],
			"summ" => $arOrder["PRICE"],
			"user_id" => $arOrder["USER_ID"],
			"user_email" => $arUser["EMAIL"],
			"date_insert" => $arOrder["DATE_INSERT"],
			"date_update" => $arOrder["DATE_UPDATE"]
		);

		if(!empty($arOrderProps))
			$this->arResult["data"]["data"]["properties"] = $arOrderProps;

		// get basket items
		$resBasket = CSaleBasket::GetList(
			array("ID" => "DESC"),
			array(
				"ORDER_ID" => $arOrder["ID"]
			),
			false,
			false,
			array(
				"ID"
			)
		);
		$arBasketItems = array();
		while($ar = $resBasket->Fetch())
		{
			$arBasketItems[] = $ar["ID"];
		}

		if(!empty($arBasketItems))
			$this->arResult["data"]["data"]["cart"] = $arBasketItems;

		return true;
	}

	public function getOrdersByMail($email)
	{
		if(!check_email($email))
		{
			$this->arResult["status"] = 403;
			return false;
		}

		$arFilter = array(
			"ACTIVE" => "Y",
			"=EMAIL" => $email
		);

		$resUser = CUser::GetList(
			$by = "id",
			$order = "asc",
			$arFilter
		);
		if($arUser = $resUser->Fetch())
		{
			if(!CModule::IncludeModule("sale") || !CModule::IncludeModule("catalog") || !CModule::IncludeModule("iblock"))
			{
				$this->arResult["status"] = 400;
				return false;
			}

			$arOrderFilter = array(
				"USER_ID" => $arUser["ID"]
			);
			$resOrders = CSaleOrder::GetList(
				array("ID" => "DESC"),
				$arOrderFilter
			);
			$arOrders = array();
			while($arOrder = $resOrders->Fetch())
			{
				$arOrders[] = $arOrder["ID"];

				$arStatus = CSaleStatus::GetByID($arOrder["STATUS_ID"]);

				$this->arResult["data"]["data"][] = array(
					"order_id" => $arOrder["ID"],
					"payed" => $arOrder["PAYED"],
					"date_payed" => $arOrder["DATE_PAYED"],
					"cancel" => $arOrder["CANCELED"],
					"status" => $arStatus["NAME"],
					"status_description" => $arStatus["DESCRIPTION"],
					"summ" => $arOrder["PRICE"],
					"user_id" => $arOrder["USER_ID"],
					"user_email" => $arUser["EMAIL"],
					"date_insert" => $arOrder["DATE_INSERT"],
					"date_update" => $arOrder["DATE_UPDATE"]
				);
			}

			if(!empty($arOrders))
			{
				$this->arResult["status"] = 406;
				return false;
			}
		}
		else
		{
			$this->arResult["status"] = 404;
			return false;
		}

		return true;
	}

	public function getCurrentBasketByMail($email)
	{
		if(!check_email($email))
		{
			$this->arResult["status"] = 403;
			return false;
		}

		$arFilter = array(
			"ACTIVE" => "Y",
			"=EMAIL" => $email
		);

		$resUser = CUser::GetList(
			$by = "id",
			$order = "asc",
			$arFilter
		);
		if($arUser = $resUser->Fetch())
		{
			if(!CModule::IncludeModule("sale") || !CModule::IncludeModule("catalog") || !CModule::IncludeModule("iblock"))
			{
				$this->arResult["status"] = 400;
				return false;
			}

			$arFUser = CSaleUser::GetList(array("USER_ID" => $arUSer["ID"]));

			$resBasket = CSaleBasket::GetList(
				array(
					"NAME" => "ASC",
					"ID" => "ASC"
				),
				array(
					"FUSER_ID" => $arFUser["ID"],
					"ORDER_ID" => "NULL"
				)
			);

			$arBasketItems = array();

			while($arBasketItem = $resBasket->Fetch())
			{
				$arBasketItems[] = $arBasketItem;

				$resProduct = CIBlockElement::GetByID($arBasketItem["PRODUCT_ID"]);
				if($arProduct = $resProduct->GetNext())
				{
					// check catalog
					$arCatalog = CCatalog::GetByID($arProduct["IBLOCK_ID"]);
					if(is_array($arCatalog))
					{
						// check if it is offers iblock
						if($arCatalog["OFFERS"] == "Y")
						{
							// find product element
							$resProp = CIBlockElement::GetProperty(
								$arProduct["IBLOCK_ID"],
								$arProduct["ID"],
								"SORT",
								"ASC",
								array("ID" => $arCatalog["SKU_PROPERTY_ID"])
							);
							$arProp = $resProp->Fetch();
							if($arProp && $arProp["VALUE"] > 0)
							{
								$iElementId = $arProp["VALUE"];
								$iIBlockId = $arCatalog["PRODUCT_IBLOCK_ID"];
								$iOffersIBlockId = $arCatalog["IBLOCK_ID"];
								$iOffersPropertyId = $arCatalog["SKU_PROPERTY_ID"];

								$resProductSKUProp = CIBlockElement::GetProperty(
									$iOffersIBlockId,
									$arProduct["ID"],
									array(),
									array(
										"ID" => $iOffersPropertyId
									)
								);
								if($arProductSKUProp = $resProductSKUProp->Fetch())
								{
									$resElement = CIBlockElement::GetByID($arProductSKUProp["VALUE"]);
									$arElement = $resElement->GetNext();

									if($arProduct["PREVIEW_PICTURE"] > 0)
										$arElement["PREVIEW_PICTURE"] = $arProduct["PREVIEW_PICTURE"];
								}
							}
						}
						//or it's regular catalog
						else
						{
							$arElement = $arProduct;
						}
					}
				}

				if($arElement["PREVIEW_PICTURE"] > 0 && !is_array($arElement["PREVIEW_PICTURE"]))
				{
					$arElement["PREVIEW_PICTURE"] = CFile::GetFileArray($arElement["PREVIEW_PICTURE"]);
				}

				// get server url from module options

				$sServerUrl = "http://" . COption::GetOptionString("mailtrig.events", "server_name");
				$sServerUrl .= ($_SERVER["SERVER_PORT"] != 80)?':'.$_SERVER["SERVER_PORT"]:'';

				$this->arResult["data"]["data"][] = array(
					"cart_id" => $arBasketItem["ID"],
					"quantity" => $arBasketItem["QUANTITY"],
					"summ" => $arBasketItem["QUANTITY"] * $arBasketItem["PRICE"],
					"name" => $arBasketItem["NAME"],
					"detail_page_url" => $sServerUrl.$arElement["DETAIL_PAGE_URL"],
					"preview_text" => $arElement["PREVIEW_TEXT"],
					"preview_picture" => $sServerUrl.$arElement["PREVIEW_PICTURE"]["SRC"]
				);
			}

			if(empty($arBasketItems))
			{
				$this->arResult["status"] = 405;
				return false;
			}
		}
		else
		{
			$this->arResult["status"] = 404;
			return false;
		}

		return true;
	}

	public function getInfo()
	{
		$sServerName = COption::GetOptionString("mailtrig.events", "server_name");

		$this->arResult["data"]["data"] = array(
			"site_name" => COption::GetOptionString("mailtrig.events", "site_name"),
			"server_name" => $sServerName,
			"site_email" => COption::GetOptionString("mailtrig.events", "site_email"),
			"support_phone" => COption::GetOptionString("mailtrig.events", "support_phone"),
			"profile_url" => "http://" . $sServerName . COption::GetOptionString("mailtrig.events", "profile_url"),
			"basket_url" => "http://" . $sServerName . COption::GetOptionString("mailtrig.events", "basket_url"),
			"order_url" => "http://" . $sServerName . COption::GetOptionString("mailtrig.events", "order_url"),
		);

		return true;
	}

	public function getRequest()
	{
		$arRequest = $_GET;

		if(!array_key_exists("method", $arRequest) || !array_key_exists("data", $arRequest))
		{
			$this->arResult["status"] = 440;
			return false;
		}
		$method = $arRequest["method"];

		if(!$this->checkAvailableMethod($arRequest["method"]))
		{
			return false;
		}

		$arData = json_decode($arRequest["data"], true);
		if(empty($arData) || !is_array($arData))
		{
			$this->arResult["status"] = 440;
			return false;
		}

		if(!$this->checkAppId($arData["app_id"]))
		{
			return false;
		}

		if($method == "get_user")
		{
			$this->getUser($arData["email"]);
			return true;
		}

		if($method == "get_last_login_user")
		{
			$this->getLastLoginUser();
			return true;
		}

		if($method == "get_basket")
		{
			$this->getBasketItem($arData["cart_id"]);
			return true;
		}

		if($method == "get_forgotten_basket")
		{
			$this->getForgottenBasket($arData["date_from"], $arData["date_to"]);
			return true;
		}

		if($method == "get_sale_stat")
		{
			$this->getSaleStat($arData["date_from"], $arData["date_to"]);
			return true;
		}

		if($method == "get_order")
		{
			$this->getOrder($arData["order_id"]);
			return true;
		}

		if($method == "get_current_cart")
		{
			$this->getCurrentBasketByMail($arData["email"]);
			return true;
		}

		if($method == "get_orders")
		{
			$this->getOrdersByMail($arData["email"]);
			return true;
		}

		if($method == "get_info")
		{
			$this->getInfo();
			return true;
		}
	}

	public function getResult()
	{
		$this->setStatus();

		return $this->arResult;
	}

	public function showResult()
	{
		$this->setStatus();

		print(json_encode($this->arResult));
	}
}
?>