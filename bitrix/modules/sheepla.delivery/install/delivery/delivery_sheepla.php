<?
// Листинг файла /bitrix/php_interface/include/sale_delivery/delivery_sheepla.php

CModule::IncludeModule("sale");
CModule::IncludeModule("sheepla.delivery");

class CDeliverySheepla
{
  function Init()
  {
    
    $sheepla_profiles = array(
      /* Основное описание */
      "SID" => "sheepla", 
      "NAME" => "Sheepla",
      "DESCRIPTION" => "",
      "DESCRIPTION_INNER" => "Delivery description",
      "BASE_CURRENCY" => COption::GetOptionString("sale", "default_currency", "RUB"),

      "HANDLER" => __FILE__,
      
      /* Методы обработчика */
      "DBGETSETTINGS" => array("CDeliverySheepla", "GetSettings"),
      "DBSETSETTINGS" => array("CDeliverySheepla", "SetSettings"),
      "COMPABILITY" => array("CDeliverySheepla", "Compability"),      
      "CALCULATOR" => array("CDeliverySheepla", "Calculate")     
      
      
    );
    
    
    $sheepla_profiles["PROFILES"] = CSheepla::GetSheeplaCarriers();
     
    return $sheepla_profiles;
  }
  // подготовка настроек для занесения в базу данных
  function SetSettings($arSettings)
  {
    // Проверим список значений стоимости. Пустые значения удалим из списка.
    foreach ($arSettings as $key => $value) 
    {
      if (strlen($value) > 0)
        $arSettings[$key] = doubleval($value);
      else
        unset($arSettings[$key]);
    }
    // вернем значения в виде сериализованного массива.
    // в случае более простого списка настроек можно применить более простые методы сериализации.
    return serialize($arSettings);
  }

  // подготовка настроек, полученных из базы данных
  function GetSettings($strSettings)
  {
    // вернем десериализованный массив настроек
    return unserialize($strSettings);
  }
  // метод проверки совместимости в данном случае практически аналогичен рассчету стоимости
  function Compability($arOrder, $arConfig)
  {
    if($arOrder["LOCATION_TO"]<1){ return array(); }
    $arLocation = CSaleLocation::GetByID($arOrder["LOCATION_TO"], 'ru');		
	$dbZipList = CSaleLocation::GetLocationZIP($arOrder["LOCATION_TO"]);
    $arZip = $dbZipList->Fetch();
    if(@array_merge_recursive($arZip,$arLocation)){
        $arLocation = array_merge_recursive($arZip,$arLocation);    
    }
        $carriers_array = CSheepla::DynamicPrice($arLocation,$arOrder,true);                    
        if ($carriers_array !== false){            
            return $carriers_array; // в противном случае вернем массив, содержащий идентфиикатор единственного профиля доставки
        }else{
            return array(); // если стоимость не найдено, вернем пустой массив - не подходит ни один профиль    
        }
    
      
  }
    
  // собственно, рассчет стоимости
  function Calculate($profile, $arConfig, $arOrder, $STEP, $TEMP = false)
  {    
    if($arOrder["LOCATION_TO"]<1){
        return array(
          "RESULT" => "ERROR",
          "VALUE" => $price
        );
    }
    $arLocation = CSaleLocation::GetByID($arOrder["LOCATION_TO"], 'ru');		
	$dbZipList = CSaleLocation::GetLocationZIP($arOrder["LOCATION_TO"]);
    $arZip = $dbZipList->Fetch();    
    if(@array_merge_recursive($arZip,$arLocation)){
        $arLocation = array_merge_recursive($arZip,$arLocation);    
    }
    // служебный метод рассчета определён выше, нам достаточно переадресовать на выход возвращаемое им значение.    
    $price = CSheepla::DynamicPrice($arLocation,$arOrder,false,$profile);        
    return array(
      "RESULT" => "OK",
      "VALUE" => $price
    );   
    
  }
}
// установим метод CDeliverySheepla::Init в качестве обработчика события
AddEventHandler("sale", "onSaleDeliveryHandlersBuildList", array('CDeliverySheepla', 'Init')); 
?>