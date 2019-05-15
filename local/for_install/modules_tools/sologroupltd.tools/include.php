<?
IncludeModuleLangFile(__FILE__);
Class CSologroupltdTools 
{
	function OnBuildGlobalMenu(&$aGlobalMenu, &$aModuleMenu)
	{

	}
}

  $SOLO_GETIBC_IBLOCK_CODE_ARRAY = array();
  $SOLO_GETIBC_IBLOCK_CODE_ARRAY_GETIBC = array();

Class CSoloTools{

  function dump($var, $die = false, $all = false)
  {
  	global $USER;
    //($USER->GetID() == 1)
  	if($USER->IsAdmin() || ($all == true))
  	{
  		?>
  		<font style="text-align: left; font-size: 10px"><pre><?print_r($var)?></pre></font><br>
  		<?
  	}
  	if($die)
  	{
  		die;
  	}
  }



  //Возвращает ID подскойства у свойства в инфоблоке (если задано XML_ID)
  //  или ID свойства у инфоблока
  //$IBLOCK_ID - число либо мнемонический код инфоблока
  function get_id_of_list_property($IBLOCK_ID, $CODE = false, $XML_ID = false){
      //У множественного свойства типа список (СИМВОЛЬНЫЙ КОД="TYPE") есть елемент (XML_ID="OUT")
      //Необходимо получить ID элемента OUT
    global $SOLO_GETIBC_IBLOCK_CODE_ARRAY;
    if (!CModule::IncludeModuleEx('iblock')) return false;//Возможно это будет снижать производитльность
      if(!is_numeric($IBLOCK_ID)){//Если это не ID
          if(!isset($SOLO_GETIBC_IBLOCK_CODE_ARRAY[$IBLOCK_ID][-1])){
              $res = CIBlock::GetList(Array(),Array("CODE"=>$IBLOCK_ID), true);
              if ($ar_res = $res->Fetch()){
                  $SOLO_GETIBC_IBLOCK_CODE_ARRAY[$IBLOCK_ID][-1] = $ar_res['ID'];//Сохраняем для кеша чтобы несколько раз не делать запрос по API
                  $IBLOCK_ID = $ar_res['ID'];
              }else return false;
          }else $IBLOCK_ID = $SOLO_GETIBC_IBLOCK_CODE_ARRAY[$IBLOCK_ID][-1];//-1 - означает что мы сохраням номер по коду

      }
      if($XML_ID){//Если указано-ищем элемент списка по коду
          $arFilter = Array("IBLOCK_ID"=>$IBLOCK_ID, "CODE"=>$CODE, "XML_ID"=> $XML_ID);
          $property_enums = CIBlockPropertyEnum::GetList(Array(), $arFilter);
          if($enum_fields = $property_enums->Fetch()){
              $SOLO_GETIBC_IBLOCK_CODE_ARRAY[$IBLOCK_ID][$CODE] = $enum_fields['ID'];
              return $enum_fields['ID'];
          }
      }elseif($CODE){//Если не указано - то значит мы ищем id свойства инфоблока по XML_ID свойства
          $properties = CIBlockProperty::GetList(Array(), Array("CODE"=>$CODE, "IBLOCK_ID"=>$IBLOCK_ID));
          if ($prop_fields = $properties->GetNext()){
            return $prop_fields["ID"];
          }
      }else{//Значит нам нужен ID инфоблока
          return $IBLOCK_ID;
      }
      return false;
  }

  //Get ID By CODE
  function getibc($IBLOCK_ID, $CODE = false, $XML_ID = false){
      global $SOLO_GETIBC_IBLOCK_CODE_ARRAY_GETIBC;
      $SOLO_GETIBC_IBLOCK_CODE_ARRAY_GETIBC = array($IBLOCK_ID,$CODE);
      return self::get_id_of_list_property($IBLOCK_ID, $CODE, $XML_ID);
  }

  function getibc2($CODE, $XML_ID = false){
    global $SOLO_GETIBC_IBLOCK_CODE_ARRAY_GETIBC;
    $a = $SOLO_GETIBC_IBLOCK_CODE_ARRAY_GETIBC;
    return self::getibc($a[0], $CODE, $XML_ID);
  }

  function getibc3($XML_ID){
    global $SOLO_GETIBC_IBLOCK_CODE_ARRAY_GETIBC;
    $a = $SOLO_GETIBC_IBLOCK_CODE_ARRAY_GETIBC;
    return self::getibc($a[0], $a[1], $XML_ID);
  }

  /*Преобразует html код в строку, удаляя все теги и переносы строки*/
  function html2line($text_desc){
      $search = array ("'<script[^>]*?>.*?</script>'si",  // Вырезает javaScript
                   "'<[\/\!]*?[^<>]*?>'si",           // Вырезает HTML-теги
                   "'([\r\n])[\s]+'",                 // Вырезает пробельные символы
                   "'&(quot|#34);'i",                 // Заменяет HTML-сущности
                   "'&(amp|#38);'i",
                   "'&(lt|#60);'i",
                   "'&(gt|#62);'i",
                   "'&(nbsp|#160);'i",
                   "'&(iexcl|#161);'i",
                   "'&(cent|#162);'i",
                   "'&(pound|#163);'i",
                   "'&(copy|#169);'i",
                   "'&#(\d+);'e");                    // интерпретировать как php-код

      $replace = array ("",
                        "",
                        "\\1",
                        "\"",
                        "&",
                        "<",
                        ">",
                        " ",
                        chr(161),
                        chr(162),
                        chr(163),
                        chr(169),
                        "chr(\\1)");

          $text_desc = preg_replace($search, $replace, $text_desc);
          $text_desc = str_replace("\r\n", " ", $text_desc);
          $text_desc = str_replace("\r", " ", $text_desc);
          $text_desc = str_replace("\n", " ", $text_desc);
          $text_desc = preg_replace("/  +/", " ", trim($text_desc));
          return $text_desc;
  }

      //Функция обновляет свойства инфоблока, оставляя текущие существующие свойства
      //Функция работает только с инфоблоками, которые размещены в отдельной таблице в Битриксе
      //При успешном завершении возвразает true, иначе "NOT_FIND" (не найден элемент) или "NOT_UPDATE" (не удалось обновить)
      //ADDITION_PARAMS - Если нужно изменить не только свойства, но и обычные значения инфоблока, типа NAME или PREVIEW_TEXT
      function update_eiblock_properties($arFilter, $PROP_UPDATE, $ADDITION_PARAMS = array()){
          /* Пример вызова
              $arFilter=array(
                  'IBLOCK_ID'=>getibc('FUND_USERS'), // ID = 13
                  'CREATED_BY' =>$USER->GetID(),
                  'ID' => 330,
              );
              $PROP_UPDATE = array(
                  getibc('FUND_USERS','POSITION') => getibc3('PRIVATE_4'),
              );
              $ADDITIONAL_PARAMS = array(
                  'PREVIEW_TEXT' => $fund_info,//Записываем описание  - необязательный параметр
                );
              if ($res = update_eiblock_properties($arFilter,$PROP_UPDATE,$ADDITIONAL_PARAMS) === true) echo "Ok";
              elseif($res == 'NOT_UPDATE')echo "Ошибка при обновлении ";
              elseif($res == 'NOT_FIND')echo 'Не найден элемент';
          */
          global $USER;
          if(!CModule::IncludeModule('iblock'))return false;

              $arSelect = array();
              foreach ($arFilter as $key => $value) {
                  if($key == 'IBLOCK_ID') continue;
                  $arSelect[] = $key;
              }
              $arSelect[] = 'PROPERTY_*';

              $rsItems = CIBlockElement::GetList(array(),$arFilter,false,false,$arSelect);

              if ($arItem = $rsItems->Fetch()){//Если же есть счет в инфоблоке - то сменим статус на архивный

                  //Подготовим для сохранения изменения статуса
                  $el = new CIBlockElement;

                  $PROP = array();
                  foreach ($arItem as $key => $value) {
                      $pos = strpos($key, 'PROPERTY_');
                      if ($pos === false) continue;
                      $key = substr($key, $pos+9);//Извлекаем ID из имени PROPERTY_40
                      $PROP[$key] = $value;
                  }
                  //Теперь сделаем подмену статуса
                  foreach ($PROP_UPDATE as $key => $value) {
                      //$PROP[$key] = $value;
                      CIBlockElement::SetPropertyValues($arItem['ID'], $arFilter['IBLOCK_ID'], $value, $key);
                  }


                  $arLoadProductArray = Array(
                    "MODIFIED_BY"    => $USER->GetID(), // элемент изменен текущим пользователем
                    //"PROPERTY_VALUES"=> $PROP,
                    );
                  $arLoadProductArray = array_merge($arLoadProductArray, $ADDITION_PARAMS);
                  $res = $el->Update($arItem['ID'], $arLoadProductArray);
                  if ($res) {
                      //Все Ок! Ничего не делаем
                      return true;
                  }else{
                      return "NOT_UPDATE";
                      //return  "Error: ".$el->LAST_ERROR;//Для тестирования
                  }
              }else{
                  return "NOT_FIND";
              }
      }


    // Параметр $number - сообщает число
    // символов в пароле
    function generate_password($number)
    {
      $arr = array('a','b','c','d','e','f',
                   'g','h','i','j','k','l',
                   'm','n','o','p','r','s',
                   't','u','v','x','y','z',
                   'A','B','C','D','E','F',
                   'G','H','I','J','K','L',
                   'M','N','O','P','R','S',
                   'T','U','V','X','Y','Z',
                   '1','2','3','4','5','6',
                   '7','8','9','0',/*'.',',',
                   '(',')','[',']','!','?',
                   '&','^','%','@','*','$',
                   '<','>','/','+','-',
                   '{','}','`','~'*/

                   );
      // Генерируем пароль
      $pass = "";
      for($i = 0; $i < $number; $i++)
      {
        // Вычисляем случайный индекс массива
        $index = rand(0, count($arr) - 1);
        $pass .= $arr[$index];
      }
      return $pass;
    }

    //Функция позволяет добавлять доп свойства в инфоблок, предварительно проверив такое свойство на существование
    //Пример вызова:
        // if(CModule::IncludeModuleEx('sologroupltd.tools')){
        //   $arFields = Array(
        //     "NAME" => "Размещение",
        //     "PROPERTY_TYPE" => "L",//L - список
        //     "LIST_TYPE" => "C",//C - флажки, L - список
        //     "MULTIPLE" => "Y",
        //   );
        //   $arFields["VALUES"][] = Array(
        //     "VALUE" => "Самара",
        //     "XML_ID" => "smr",
        //   );
        //   $arFields["VALUES"][] = Array(
        //     "VALUE" => "Питер",
        //     "XML_ID" => "spb",
        //   );
        //   CSoloTools::AddNewProperty('news','CITY_PLACE',$arFields);
        //   CSoloTools::AddNewProperty('STATES','CITY_PLACE',$arFields);
        // }
    //коды свойств брать отсюда: http://dev.1c-bitrix.ru/api_help/iblock/fields.php#fproperty
    //Возвращает ID данного свойства(не важно, добавили мы его или нет)
    //Debug = true - будет означать, что будет выводиться на экран текстовое описание
    //Если = get_text - то функция будет возвращать текст
    function AddNewProperty($IBLOCK_CODE,$CODE,$arFields,$Debug=false){
      $arFields['IBLOCK_ID'] = self::getibc($IBLOCK_CODE);
      $arFields['CODE'] = $CODE;
      $ibp = new CIBlockProperty;
      $PropID = false;
      $txt_message = '';//сообщение, выводимое на экран
      if(!self::getibc($IBLOCK_CODE)) $txt_message .= GetMessage("SOLOGROUPLTD_TOOLS_INFOBLOK").'<br>';
      else{
        $txt_message .= GetMessage("SOLOGROUPLTD_TOOLS_INFOBLOK1").'<br>';
        if($PropID = self::getibc($IBLOCK_CODE,$arFields['CODE'])){
          $txt_message .= GetMessage("SOLOGROUPLTD_TOOLS_V_INFOBLOKE").'<br>';
        }
        else{
          $txt_message .= GetMessage("SOLOGROUPLTD_TOOLS_V_INFOBLOKE1").'<br>';
          $PropID = $ibp->Add($arFields);
          if($PropID) {
            $txt_message .= GetMessage("SOLOGROUPLTD_TOOLS_SVOYSTVO_USPESNO_DOB").'<br>';
          }
          else $txt_message .= GetMessage("SOLOGROUPLTD_TOOLS_NE_UDALOSQ_DOBAVITQ").'<br>';
        }
      }
      if($Debug) {
        $txt_message = str_replace('#IBLOCK_CODE#', $IBLOCK_CODE, $txt_message);
        $txt_message = str_replace('#FIELD_NAME#', $arFields['NAME'], $txt_message);
        $txt_message = str_replace('#FIELD_CODE#', $arFields['CODE'], $txt_message);
        $txt_message = str_replace('#FIELD_ID#', $PropID, $txt_message);
        if($Debug === true){echo $txt_message;}
        elseif($Debug == 'get_text'){return $txt_message;}
      }
      return $PropID;
    }

    //$arSelectProperty - Это свойства без префикса PROPERTY_ !!!
    //Удобная функция получения элементов инфоблока только с нужнными нам свойствами
    function GetElList_WithSelect($IBlockCode, $arFilter, $arSelectProperty = array()){
      foreach ($arSelectProperty as $key => $value) {
        $arSelectProperty[$key] = 'PROPERTY_'.$arSelectProperty[$key];
      }
      $arSelectProperty = array_merge(array('CREATED_BY','ID'),$arSelectProperty);
      $arFilter["IBLOCK_ID"] = getibc($IBlockCode);
      $rsItems = CIBlockElement::GetList($arOrder=array('SORT'=>'ASC'),$arFilter,false,false,$arSelectProperty);
      while($arItem = $rsItems->Fetch()){
        $arElList[] = $arItem;
      }
      return $arElList;
    }
    
}

$pls_not_include_module = true;

//Добавим init.php в битрикс
if (file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sologroupltd.tools/init.php")){require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sologroupltd.tools/init.php");}

?>