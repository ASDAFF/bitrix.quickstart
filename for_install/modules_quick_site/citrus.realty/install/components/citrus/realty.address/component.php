<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

// set default value for missing parameters, simple param check
$componentParams = CComponentUtil::GetComponentProps($this->getName());
if (is_array($componentParams))
{
	foreach ($componentParams["PARAMETERS"] as $paramName => $paramArray)
	{
		if (!is_set($arParams, $paramName) && is_set($paramArray, "DEFAULT"))
			$arParams[$paramName] = $paramArray["DEFAULT"];

		$paramArray["TYPE"] = ToUpper(is_set($paramArray, "TYPE") ? $paramArray["TYPE"] : "STRING");
		switch ($paramArray["TYPE"]) {
			case 'INT':
				$arParams[$paramName] = IntVal($arParams[$paramName]);
				break;

			case 'LIST':
				if ($paramArray['MULTIPLE'] == "Y")
				{
					foreach ($arParams[$paramName] as $key=>$value)
					{
						if (!($value && array_key_exists($value, $paramArray['VALUES'])))
							unset($arParams[$paramName][$key]);
					}
					if (empty($arParams[$paramName]))
						$arParams[$paramName] = $paramArray["DEFAULT"];
				}
				elseif (!($arParams[$paramName] && array_key_exists($arParams[$paramName], $paramArray['VALUES'])))
					$arParams[$paramName] = $paramArray["DEFAULT"];
				break;

			case 'CHECKBOX':
				$arParams[$paramName] = ($arParams[$paramName] == (is_set($paramArray, 'VALUE') ? $paramArray['VALUE'] : 'Y'));
				break;

			default:
				// string etc.
				break;
		}
	}
}

$arParams['MAP_ID'] =
	(strlen($arParams["MAP_ID"])<=0 || !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["MAP_ID"])) ? 
	'MAP_'.RandString() : $arParams['MAP_ID'];

$arParams['MAP_WIDTH'] = trim($arParams['MAP_WIDTH']);
if (ToUpper($arParams['MAP_WIDTH']) != 'AUTO' && substr($arParams['MAP_WIDTH'], -1, 1) != '%')
{
	$arParams['MAP_WIDTH'] = intval($arParams['MAP_WIDTH']);
	if ($arParams['MAP_WIDTH'] <= 0) $arParams['MAP_WIDTH'] = 600;
	$arParams['MAP_WIDTH'] .= 'px';
}

$arParams['MAP_HEIGHT'] = trim($arParams['MAP_HEIGHT']);
if (substr($arParams['MAP_HEIGHT'], -1, 1) != '%')
{
	$arParams['MAP_HEIGHT'] = intval($arParams['MAP_HEIGHT']);
	if ($arParams['MAP_HEIGHT'] <= 0) $arParams['MAP_HEIGHT'] = 500;
	$arParams['MAP_HEIGHT'] .= 'px';
}

$this->IncludeComponentTemplate();
?>