<?


  //���������� ID ����������� � �������� � ��������� (���� ������ XML_ID)
  //  ��� ID �������� � ���������
  //$IBLOCK_ID - ����� ���� ������������� ��� ���������
  function get_id_of_list_property($IBLOCK_ID, $CODE = false, $XML_ID = false){
      //� �������������� �������� ���� ������ (���������� ���="TYPE") ���� ������� (XML_ID="OUT")
      //���������� �������� ID �������� OUT
    global $SOLO_GETIBC_IBLOCK_CODE_ARRAY;
    if (!CModule::IncludeModuleEx('iblock')) return false;//�������� ��� ����� ������� �����������������
      if(!is_numeric($IBLOCK_ID)){//���� ��� �� ID
          if(!isset($SOLO_GETIBC_IBLOCK_CODE_ARRAY[$IBLOCK_ID][-1])){
              $res = CIBlock::GetList(Array(),Array("CODE"=>$IBLOCK_ID), true);
              if ($ar_res = $res->Fetch()){
                  $SOLO_GETIBC_IBLOCK_CODE_ARRAY[$IBLOCK_ID][-1] = $ar_res['ID'];//��������� ��� ���� ����� ��������� ��� �� ������ ������ �� API
                  $IBLOCK_ID = $ar_res['ID'];
              }else return false;
          }else $IBLOCK_ID = $SOLO_GETIBC_IBLOCK_CODE_ARRAY[$IBLOCK_ID][-1];//-1 - �������� ��� �� �������� ����� �� ����

      }
      if($XML_ID){//���� �������-���� ������� ������ �� ����
          $arFilter = Array("IBLOCK_ID"=>$IBLOCK_ID, "CODE"=>$CODE, "XML_ID"=> $XML_ID);
          $property_enums = CIBlockPropertyEnum::GetList(Array(), $arFilter);
          if($enum_fields = $property_enums->Fetch()){
              $SOLO_GETIBC_IBLOCK_CODE_ARRAY[$IBLOCK_ID][$CODE] = $enum_fields['ID'];
              return $enum_fields['ID'];
          }
      }elseif($CODE){//���� �� ������� - �� ������ �� ���� id �������� ��������� �� XML_ID ��������
          $properties = CIBlockProperty::GetList(Array(), Array("CODE"=>$CODE, "IBLOCK_ID"=>$IBLOCK_ID));
          if ($prop_fields = $properties->GetNext()){
            return $prop_fields["ID"];
          }
      }else{//������ ��� ����� ID ���������
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