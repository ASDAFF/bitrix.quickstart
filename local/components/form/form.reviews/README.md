# bitrix.component.reviews
Компонент для отзывов или комментариев для Bitrix. Полезен на редакциях сайта менее "Стандарт", где отсутствует модуль form
<br>
Параметры:<br>
EMAIL_EVENT - почтовое событие<br>
EMAIL_EVENT_TEMPLATET - шаблон почтового события<br>
IBLOCK_ID - ID инфоблока, в который созранять результат<br>
FIELDS - массив с описанием полей.<br>
<br>
Возможные значения:<br>
input<br>
select<br>
textarea<br>
rating - поле для оценки (полезно для отзыва)<br>
<br>
Пример работы:
<pre>
$formParams = [
      'EMAIL_EVENT' => 'MYO_NEW_ORDER',
      'EMAIL_EVENT_TEMPLATE' => 13,
      'IBLOCK_ID' => 6,
      'FIELDS' => array(
      array(
          'TYPE' => 'input',
          'NAME'=>'phone',
          'PLACEHOLDER' => 'Телефон',
          'REQUEST' => "Y"
      ),
      array(
          'TYPE' => 'hidden',
          'NAME'=>'PRICE',
          'VALUE' => $initPrice
      ),

      array('TYPE' => 'hidden', 'NAME' => 'ID', 'VALUE' => $arResult["ID"]),
      array('TYPE' => 'hidden', 'NAME' => 'NAME', 'VALUE' => $arResult["NAME"]),
    )
];
</pre>
Добавляем форму еще параметры, например при каком-то условии
<pre>
$formParams['FIELDS'][] = [
  'NAME'=>'MATERIAL',
  'TYPE' => 'select',
  'VALUE' => $arResult['MATERIALS'],
  'DEFAULT' => $arResult['DEFAULT_MATERIAL'],
];

$APPLICATION->IncludeComponent("reviews:form.reviews", ".default", $formParams);
</pre>
