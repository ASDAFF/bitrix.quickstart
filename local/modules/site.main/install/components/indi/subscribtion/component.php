<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

global $DB;
global $USER;
global $APPLICATION;

$arResult = array();

if(!CModule::IncludeModule('subscribe'))
{
	ShowError('Не установлен модуль подписки!');
	return false;
}

// заголовки
if($arParams['SET_TITLE'] == 'Y') {
	if($_REQUEST["unsubscribe"] == "Y")
		$APPLICATION->SetTitle('Отписаться от рассылки');
	else
		$APPLICATION->SetTitle('Подписаться и узнать первым!');
}

// перешли по ссылке "отписаться"
if($_REQUEST["unsubscribe"] == "Y")
	$arResult["UNSUBSCRIBE"] = "Y";

if($_REQUEST["SENT"] == "Y")	// форма отправлена	
{
	////////////////////////////////////
	// Отписываем (перешли по ссылке) //
	////////////////////////////////////
	
	if($_REQUEST["unsubscribe"] == "Y")
	{
		$EMAIL = strtolower(trim(htmlspecialcharsback($_REQUEST["EMAIL"])));
		if(!preg_match('/.+@.+\..+/i', $EMAIL))
		{
			$arResult["ERRORS"][] = "Указан неправильный email для удаления подписки!";	
		}
		else
		{
			// получаем подписки по email-у
			$subscription = CSubscription::GetByEmail($EMAIL);
			while($arSubscribtion = $subscription->GetNext())
			{
				$arSubscribtions[] = $arSubscribtion;
			}
		
			// удаляем подписки
			if(is_array($arSubscribtions))
			{
				foreach($arSubscribtions as $arItem)
				{
					if(!CSubscription::Delete($arItem["ID"]))
					{
						$delete_errors++;
					}
				}
				if($delete_errors > 0)
				{
					$arResult["ERRORS"][] = "Не удалось удалить подписку для ".$EMAIL;
				}
				else
				{
					$arResult["NOTES"][] = "Подписка отменена.<br />Вы больше не будете получать нашу рассылку.<br />Спасибо, что были с нами.";
				}
			}
			else
			{
				$arResult["ERRORS"][] = "Не найдено подписки для ".$EMAIL;
			}
		}
	}
	else	
	{		
		/////////////////
		// Подписываем //
		/////////////////
		
		$EMAIL = strtolower(trim($_REQUEST["EMAIL"]));
		if(!preg_match('/.+@.+\..+/i', $EMAIL))
		{
			$arResult["ERRORS"][] = "Укажите правильный email!";	
		}

		// проверяем на наличие подписки на этот email
		if($obSubscription = CSubscription::GetByEmail($EMAIL))
			if($obSubscription->GetNext())
				$arResult["ERRORS"][] = "Подписка на ".$EMAIL." уже оформлена.";
			
		// получаем рубрики
		if(!$arResult["ERRORS"])
		{
			$arOrder = Array("SORT"=>"ASC", "NAME"=>"ASC"); 
			$arFilter = Array("ACTIVE"=>"Y", "LID"=>LANG); 
			$rsRubric = CRubric::GetList($arOrder, $arFilter); 
			$arRubrics = array(); 
			while($arRubric = $rsRubric->GetNext()) 
				$arRubricIDs[] = $arRubric["ID"];
			
			if(!is_array($arRubricIDs) || !count($arRubricIDs) > 0)
				$arResult["ERRORS"][] = "Не найдены рубрики подписки!";
		}
		
		// добавляем подписку на все рубрики 	
		if(!$arResult["ERRORS"])
		{
			$arFields = Array(
				"USER_ID" => ($USER->IsAuthorized() ? $USER->GetID() : false),
				"FORMAT" => "text",
				"EMAIL" => $EMAIL,
				"ACTIVE" => "Y",
				"RUB_ID" => $arRubricIDs,
				"SEND_CONFIRM" => "N",
				"ALL_SITES" => "Y"
			);
			$subscr = new CSubscription;

			$ID = $subscr->Add($arFields);
			if($ID>0)
			{
				// и сразу подтверждаем 
				$query = "UPDATE b_subscription SET CONFIRMED='Y' WHERE ID='".$ID."';";
				if($DB->Query($query))
					$arResult["NOTES"][] = "Спасибо, что подписались.<br />Не забывайте проверять почту.";
				else
					$arResult["ERRORS"][] = "Не удалось подтвердить подписку!";
			}
			else
				$arResult["ERRORS"][] = "Не удалось подписаться. ".$subscr->LAST_ERROR;
		}
	}

}

if ($this->StartResultCache()) {
	$this->IncludeComponentTemplate();
}