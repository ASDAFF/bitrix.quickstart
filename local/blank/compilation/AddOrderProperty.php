<?php
function AddOrderProperty($prop_id, $value, $order) {
  if (!strlen($prop_id)) {
    return false;
  }
  if (CModule::IncludeModule('sale')) {
    if ($arOrderProps = CSaleOrderProps::GetByID($prop_id)) {
      $db_vals = CSaleOrderPropsValue::GetList(array(), array('ORDER_ID' => $order, 'ORDER_PROPS_ID' => $arOrderProps['ID']));
      if ($arVals = $db_vals -> Fetch()) {
        return CSaleOrderPropsValue::Update($arVals['ID'], array(
          'NAME' => $arVals['NAME'],
          'CODE' => $arVals['CODE'],
          'ORDER_PROPS_ID' => $arVals['ORDER_PROPS_ID'],
          'ORDER_ID' => $arVals['ORDER_ID'],
          'VALUE' => $value,
        ));
      } else {
        return CSaleOrderPropsValue::Add(array(
          'NAME' => $arOrderProps['NAME'],
          'CODE' => $arOrderProps['CODE'],
          'ORDER_PROPS_ID' => $arOrderProps['ID'],
          'ORDER_ID' => $order,
          'VALUE' => $value,
        ));
      }
    }
  }
}
?>
