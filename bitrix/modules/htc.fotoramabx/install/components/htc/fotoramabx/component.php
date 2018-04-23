<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var CFotoramaComponent $this */

/** @global CMain $APPLICATION */
global $APPLICATION;

/**
 *  Проверим валидность параметров
 */
if (!isset($arParams['CACHE_TIME']) || $arParams['CACHE_TIME'] < 0) //Время кеширования по умолчанию
{
	$arParams['CACHE_TIME'] = CFotoramaComponent::CACHE_TIME_DEFAULT;
}

if (!isset($arParams['SOURCE_TYPE']))
{
	ShowError(GetMessage('SOURCE_TYPE_NOT_SPECIFIED'));
	return;
}

if (!isset($arParams['SOURCE_ID']))
{
	ShowError(GetMessage('SOURCE_ID_NOT_SPECIFIED'));
	return;
}

if ($this->StartResultCache($arParams['CACHE_TIME']))
{
	/**
	 * В зависимости от того, что используется в качестве источника изображений
	 * вызовем метод из class.php
	 */
	switch ($arParams['SOURCE_TYPE'])
	{
		case CFotoramaComponent::SOURCE_TYPE_MEDIALIBRARY_COLLECTION:
			if (!CModule::IncludeModule('fileman'))
			{
				$this->AbortResultCache();
				ShowError(GetMessage('FILEMAN_MODULE_NOT_INSTALLED'));
				return;
			}
			$arResult['IMAGES'] = $this->getImagesFromMedialibraryCollection($arParams['SOURCE_ID']); 
			break;
		case CFotoramaComponent::SOURCE_TYPE_IBLOCK_SECTION:
			if (!CModule::IncludeModule('iblock'))
			{
				$this->AbortResultCache();
				ShowError(GetMessage('IBLOCK_MODULE_NOT_INSTALLED'));
				return;
			}
			$arResult['IMAGES'] = $this->getImagesFromIblockSection($arParams['SOURCE_ID']);
			break;
		default:
			$this->AbortResultCache();
			ShowError(GetMessage('SOURCE_TYPE_UNKNOWN'));
			return;
	}
	   
	if (empty($arResult['IMAGES']))
	{
		ShowError(GetMessage('NO_IMAGES'));
		return;
	}

	$parameters = array(); //Передаваемые в компонент параметры

	$parameters['RATIO'] = $arResult['IMAGES'][0]['WIDTH'] . '/' . $arResult['IMAGES'][0]['HEIGHT']; //Cоотношение сторон рассчитывается по первой картинке в списке
	
	$parameters['LOOP'] = false; //Зациклить навигацию по изображениям
	if (!empty($arParams['LOOP']) && $arParams['LOOP'] === 'Y')
	{
		$parameters['LOOP'] = true;
	}

	$parameters['CHANGE_HASH'] = false; //Изменять хеш в URL
	if (!empty($arParams['CHANGE_HASH']) && $arParams['CHANGE_HASH'] === 'Y')
	{
		$parameters['CHANGE_HASH'] = true;
	}

	$parameters['NAVIGATION_ON_TOP'] = false; //Показывать навигацию над изображениями
	if (!empty($arParams['NAVIGATION_POSITION']) && $arParams['NAVIGATION_POSITION'] === CFotoramaComponent::NAVIGATION_POSITION_TOP)
	{
		$parameters['NAVIGATION_ON_TOP'] = true;
	}
	
	$parameters['SHUFFLE'] = false; //Перемешивать изображения каждый раз
	if (!empty($arParams['SHUFFLE']) && $arParams['SHUFFLE'] === 'Y')
	{
		$parameters['SHUFFLE'] = true;
	}

	$parameters['NAVIGATION_STYLE'] = CFotoramaComponent::NAVIGATION_STYLE_DOTS; //Стиль навигации (точки по умолчанию)
	if (!empty($arParams['NAVIGATION_STYLE'])) 
	{
		$parameters['NAVIGATION_STYLE'] = $arParams['NAVIGATION_STYLE']; 
	}

	$parameters['SHOW_ARROWS'] = false; //Показывать стрелки навигации
	if (!empty($arParams['SHOW_ARROWS']) && $arParams['SHOW_ARROWS'] === 'Y') {
		$parameters['SHOW_ARROWS'] = true;
	}

	$parameters['ALLOW_FULLSCREEN'] = CFotoramaComponent::FULLSCREEN_MODE_DISABLED; //Полноэкранный режим (отключен по умолчанию)
	if (!empty($arParams['ALLOW_FULLSCREEN']))
	{
		$parameters['ALLOW_FULLSCREEN'] = $arParams['ALLOW_FULLSCREEN'];
	}

	$parameters['SHOW_CAPTION'] = false; //Показывать подписи
	if (!empty($arParams['SHOW_CAPTION']) && $arParams['SHOW_CAPTION'] === 'Y')
	{
		$parameters['SHOW_CAPTION'] = true;
	}

	$parameters['LAZY_LOAD'] = false; //Ленивая загрузка (игнорировать браузеры с отключенным JS)
	if (!empty($arParams['LAZY_LOAD']) && $arParams['LAZY_LOAD'] === 'Y') {
		$parameters['LAZY_LOAD'] = true;
	}

	$parameters['AUTOPLAY'] = false; //Автоматическое перелистывание изображений
	if (!empty($arParams['AUTOPLAY']) && $arParams['AUTOPLAY'] > 0) {
		$parameters['AUTOPLAY'] = $arParams['AUTOPLAY'];
	}

	$parameters['TRANSITION_EFFECT'] = false; //Визуальный эффект смены изображений
	if (!empty($arParams['TRANSITION_EFFECT'])) {
		$parameters['TRANSITION_EFFECT'] = $arParams['TRANSITION_EFFECT'];
	}

	$arResult['PARAMETERS'] = $parameters;

	$this->IncludeComponentTemplate();
}

/**
 * Так как без JS- и CSS-файлов Фоторамы компонент не имеет смысла, добавляем их тут
 */
$APPLICATION->SetAdditionalCSS('http://fotorama.s3.amazonaws.com/4.4.9/fotorama.css');
$APPLICATION->AddHeadString('<script>!window.jQuery && document.write(unescape(\'%3Cscript src="//code.jquery.com/jquery-1.10.2.min.js"%3E%3C/script%3E\'))</script>',true);
$APPLICATION->AddHeadString('<script src="http://fotorama.s3.amazonaws.com/4.4.9/fotorama.js"></script>');
