<?php
/**
 * Individ module
 *
 * @category	Individ
 * @link		http://individ.ru
 * @revision	$Revision$
 * @date		$Date$
 */

namespace Indi\Main;

/**
 * Класс для организации нестандартного поиска
 */
class Search
{
	// Абсолютный путь до индексируемого файла
	private static $filePath = "";
	// Абсолютный путь до папки сайта
	private static $fileDocRoot = "";

	/**
	 * Обработчик, вызываемый перед переиндексацией документа
	 *
	 * @param array $arFields Поля индексируемого документа
	 * @return array Модифицированный или исходный $arFields
	 */
	public static function onBeforeIndex($arFields)
	{
//		$arFields = self::onIblockIndex($arFields);
		$arTmpFields = self::onPageIndex($arFields);
		return array_merge($arFields, $arTmpFields);
	}


	/**
	 * Обработчик индексации каждого из инфоблоков ао-отдельности
	 *
	 * @param $arFields
	 * @return array
	 */
	public static function onIblockIndex($arFields){
		try {
			if (is_array($arFields)) {
				//Для инфоблоков логика индексации реализуется в обслуживающем инфоблок классе
				if ($arFields['MODULE_ID'] == 'iblock') {
					$iblock = Iblock\Prototype::getInstance($arFields['PARAM2']);
					$isSection = substr($arFields['ITEM_ID'], 0, 1) == 'S';
					$methodName = 'onBeforeSearchIndex' . ($isSection ? 'Section' : 'Element');
					if (method_exists($iblock, $methodName)) {
						return call_user_func(array($iblock, $methodName), $arFields);
					}
				}
			}
		} catch(\Exception $e) {}

		return $arFields;
	}


	/**
	 * Обработчик, добавляющий включаемые области в поисковый индекс документа
	 *
	 * @param array $arFields Поля индексируемого документа
	 * @return array Модифицированный массив $arFields
	 */
	public static function onPageIndex($arFields)
	{
		$obIo = \CBXVirtualIo::GetInstance();
		if( $arFields["MODULE_ID"] == "main" ){
			list($siteId, $fileRelPath) = explode("|", $arFields["ITEM_ID"]);
			$fileDocRoot = \CSite::GetSiteDocRoot($siteId);
			if( substr($fileDocRoot, -1) == '/' ){
				$fileDocRoot = substr($fileDocRoot, 0, -1);
			}
			$filePath = $fileDocRoot . $fileRelPath;
			// Получаем объект файла
			$obFile = $obIo->GetFile($filePath);

			// Получаем содержимое файла в строку
			$sFile = $obFile->GetContents();
			if( !empty($sFile) ) {
				self::$filePath = $filePath;
				self::$fileDocRoot = $fileDocRoot;
				preg_match_all('/\/includes.*\.php/', $sFile, $arMatches);
				foreach( $arMatches[0] as $arMatch ){
					$obIncFile = $obIo->GetFile(self::$fileDocRoot . $arMatch);
					$arFields['BODY'] .= $obIncFile->GetContents();
				}

				$arFields["BODY"] = \CSearch::KillTags($arFields["BODY"]);
			}
		}

		return $arFields;
	}
}