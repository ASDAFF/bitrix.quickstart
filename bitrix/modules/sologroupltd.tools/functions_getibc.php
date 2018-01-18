<?


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
      return get_id_of_list_property($IBLOCK_ID, $CODE, $XML_ID);
  }

  function getibc2($CODE, $XML_ID = false){
    global $SOLO_GETIBC_IBLOCK_CODE_ARRAY_GETIBC;
    $a = $SOLO_GETIBC_IBLOCK_CODE_ARRAY_GETIBC;
    return getibc($a[0], $CODE, $XML_ID);
  }

  function getibc3($XML_ID){
    global $SOLO_GETIBC_IBLOCK_CODE_ARRAY_GETIBC;
    $a = $SOLO_GETIBC_IBLOCK_CODE_ARRAY_GETIBC;
    return getibc($a[0], $a[1], $XML_ID);
  }
?>