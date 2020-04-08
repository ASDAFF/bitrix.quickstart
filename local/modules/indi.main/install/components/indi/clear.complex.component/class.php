<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
	die();
}
use \Bitrix\Main\Localization\Loc;

class IndiDirectionsComplex extends \CBitrixComponent
{
	/**
	 * Подключает языковые файлы
	 */

	public function onIncludeComponentLang()
	{
		$this->includeComponentLang(basename(__FILE__));
		Loc::loadMessages(__FILE__);
	}

	/**
	 * Шаблоны путей по умолчанию
	 *
	 * @var array
	 */
	protected $arDefaultUrlTemplates404 = array();

	/**
	 * Переменные шаблонов путей компонента
	 */
	protected $componentVariables = array();
	protected $arDefaultVariableAliases404 = array();
	protected $arDefaultVariableAliases = array();
	protected $arVariables = array();
	protected $arComponentVariables = array();
	protected $arUrlTemplates = array();
	protected $arVariableAliases = array();
	protected $arUrlTemplatesReplaced = array();

	/**
	 * Ошибки на сайте
	 */
	private $errors = array();

	/**
	 * Проверка входных параметров
	 */
	private function checkInputParams()
	{
		// проверка на включенность режима ЧПУ
		if ($this->arParams['SEF_MODE'] != 'Y') {
			$this->errors[] = "";
		}
	}

	/**
	 * Вывод ошибок
	 */
	private function showErrors()
	{
		foreach ($this->errors as $error) {
			ShowError($error);
		}
	}

	/**
	 * Определяет шаблоны путей
	 */
	protected function setSefDefaultParams()
	{
		$this->arDefaultUrlTemplates404 = array(
			"parent_page_index" => "/parent_page_index/",
			"parent_page_detail" => "/parent_page_index/#ELEMENT_CODE#/",
			"child_page_index" => "#ELEMENT_CODE#/child_page_index/",
			"child_page_detail" => "#ELEMENT_CODE#/child_page_index/#SUB_ELEMENT_CODE#/",
		);
	}

	/**
	 * Определяет переменные путей
	 */
	protected function setSefComponentVariables()
	{
		$this->componentVariables = array(
			"ELEMENT_ID",
			"ELEMENT_CODE",
			"SUB_ELEMENT_ID",
			"SUB_ELEMENT_CODE",
		);
	}

	/**
	 * Вытаскивает данные родительского раздела
	 */
	private function getPageParams()
	{
		$pageTypeCode = $this->arVariables["ELEMENT_CODE"];

		if ($pageTypeCode) {
			$arFilter = array(
				'IBLOCK_ID' => \Indi\Main\Iblock\ID_,
				'CODE' => $pageTypeCode,
			);

			$arServices = array();
			$rs = \CIBlockElement::GetList(array(), $arFilter, false, false, array('NAME'));
			while ($element = $rs->GetNext()) {
				$arServices['DATA'] = $element;
			}

			$curPageTypeName = $arServices['DATA']['NAME'];

			return $curPageTypeName;
		}
	}

	/**
	 * Задает настройки в зависимости от страницы
	 * помимо крошек можно задать заголовок, описание и т. д.
	 */
	private function setPageParams()
	{
		global $APPLICATION;

		// получаем название раздела
		$curPageName = $this->getPageParams();

		// в зависимости от страницы добавляем пункт в крошки
		switch ($this->arResult['COMPONENT_PAGE']) {
			case 'child_page_index':
			case 'child_page_detail': {
				$APPLICATION->AddChainItem($curPageName, $this->arUrlTemplatesReplaced['parent_page_detail']);
				break;
			}
		}
	}

	/**
	 * Получение результатов
	 */
	protected function getResult()
	{
		global $APPLICATION;

		$this->arUrlTemplates = CComponentEngine::MakeComponentUrlTemplates($this->arDefaultUrlTemplates404, $this->arParams["SEF_URL_TEMPLATES"]);
		$this->arVariableAliases = CComponentEngine::MakeComponentVariableAliases($this->arDefaultVariableAliases404, $this->arParams["VARIABLE_ALIASES"]);

		// Разбор URL
		$engine = new CComponentEngine($this);
		if (CModule::IncludeModule('iblock')) {
			$engine->addGreedyPart("#SECTION_CODE_PATH#");
			$engine->setResolveCallback(array("CIBlockFindTools", "resolveComponentEngine"));
		}

		$this->arResult['COMPONENT_PAGE'] = $engine->guessComponentPath(
			$this->arParams['SEF_FOLDER'],
			$this->arUrlTemplates,
			$this->arVariables
		);

		$b404 = false;
		if (!$this->arResult['COMPONENT_PAGE']) {
			$this->arResult['COMPONENT_PAGE'] = "parent_page_index";
			$b404 = true;
		}

		if ($b404 && $this->arParams["SET_STATUS_404"] === "Y") {
			$folder404 = str_replace("\\", "/", $this->arParams["SEF_FOLDER"]);
			if ($folder404 != "/") {
				$folder404 = "/" . trim($folder404, "/ \t\n\r\0\x0B") . "/";
			}
			if (substr($folder404, -1) == "/") {
				$folder404 .= "index.php";
				if ($folder404 != $APPLICATION->GetCurPage(true)) {
					CHTTP::SetStatus("404 Not Found");
					$this->arResult['COMPONENT_PAGE'] = "404";
				}
			}
		}

		CComponentEngine::InitComponentVariables($this->arResult['COMPONENT_PAGE'], $this->arComponentVariables, $this->arVariableAliases, $this->arVariables);

		$this->arResult = array_merge(
			array(
				"FOLDER" => $this->arParams['SEF_FOLDER'],
				"URL_TEMPLATES" => $this->arUrlTemplates,
				"VARIABLES" => $this->arVariables,
				"ALIASES" => $this->arVariableAliases,
			),
			$this->arResult
		);

		$folder = $this->arParams['SEF_FOLDER'];
		$this->arUrlTemplatesReplaced = array_map(
			function($template) use ($folder) {
				$path = $folder . \CComponentEngine::MakePathFromTemplate($template, $this->arVariables, $folder);

				return $path;
			},
			$this->arUrlTemplates
		);

		$this->arResult["URL_TEMPLATES_REPLACED"] = $this->arUrlTemplatesReplaced;
	}

	/**
	 * Выполняет логику работы компонента
	 */
	public function executeComponent()
	{
		try {
			$this->checkInputParams();
			if (!empty($this->errors)) {
				$this->showErrors();
			} else {
				$this->setSefDefaultParams();
				$this->setSefComponentVariables();
				$this->getResult();
				$this->setPageParams();

				$this->includeComponentTemplate($this->arResult['COMPONENT_PAGE']);
			}
		} catch (Exception $e) {
			ShowError($e->getMessage());
		}
	}
}