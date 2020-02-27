<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var CBitrixComponent $this */
/** @var array $arParams */
/** @var array $arResult */
/** @var string $componentPath */
/** @var string $componentName */
/** @var string $componentTemplate */
/** @global CDatabase $DB */
/** @global CUser $USER */
/** @global CMain $APPLICATION */


// if($arParams['FIELDS'][0])
// {
//    $arParams['FIELDS'] = json_decode($arParams['~FIELDS'][0], true);
// } 

// Проверка и инициализация входных параметров
// if ($arParams["ID"] <= 0)
//    $arParams["ID"] = 10;

// Если нет валидного кеша (то есть нужно запросить
// данные и сделать валидный кеш)
if ($this->StartResultCache())
{
   // Запрос данных и заполнение $arResult
   $arResult = array();

// d($arParams);
	foreach ($arParams['~FIELDS'] as $fieldKey => $field) 
	{
		if(is_array($field['VALUE']))
		{
			// TODO тут что то не так
			$activeID = $field['DEFAULT'] ?: key($arParams['~FIELDS'][$fieldKey]['VALUE']);

			if($field['TYPE'] == 'images')
			{
				$field['VALUE'][$activeID]['ACTIVE'] = 'Y';
			}
			
			$field['DEFAULT'] = $activeID;

		}
		$arResult['FIELDS'][$field['NAME']] = $field;
	}

   // Если выполнилось какое-то условие, то кешировать данные не надо
   if(empty($arParams['~FIELDS']))
   {
      $this->AbortResultCache();
   }

   // Подключить шаблон вывода
   $this->IncludeComponentTemplate();
}


?>

<?
$_SESSION['COMPONENT_FORM']['PARAMS'] = $arParams;
?>

