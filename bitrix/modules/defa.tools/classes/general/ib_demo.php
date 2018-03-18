<?
/**
 * @author DEFA
 * @package demo
 */

IncludeModuleLangFile(__FILE__);

/**
 * меню/диалоги в файле /interface/get_menu.php
 * обработка кастомных действий в файле /controller.php
 *
 * Class DefaToolsDemo
 */
class DefaToolsDemo
{
	private $iblock_id = null;

	private $elementsCount;
	private $sectionsCount;

	private function __construct($iblock_id)
	{
		if (!CModule::IncludeModule("iblock")) {
			throw new DefaToolsException(GetMessage("CAT_ERROR_IBLOCK_NOT_INSTALLED"));
		}

		$this->iblock_id  = intval($iblock_id);
		$this->tmpFiles   = array();
		$this->text       = "";
		$this->textName   = "";
		$this->sourcePath = $_SERVER['DOCUMENT_ROOT'] . "/upload/defatools/demo_files";
		$this->tmpPath    = $this->sourcePath . "/tmp";
		$this->isUTF      = defined("BX_UTF") && BX_UTF == true;
	}

	private function __destruct()
	{
		foreach ($this->tmpFiles as $file) {
			if (self::IsInTmpDir($file))
				unlink($file);
		}
	}

	/**
	 * Добавляет демо-данные в инфоблок
	 * @param $iblock_id
	 * @param array $params
	 * @return bool
	 */
	public static function AddToIBlock($iblock_id, array $params)
	{
		$instance = new self($iblock_id);
		return $instance->doAddDemoContent($params);
	}

	/**
	 * Удаляет демо-данные из инфоблока
	 * @param $iblock_id
	 * @return bool
	 */
	public static function DeleteFromIBlock($iblock_id)
	{
		$deleter = new self($iblock_id);
		return $deleter->DeleteDemoContent();
	}


	private function DeleteDemoContent()
	{
		/** @global $APPLICATION CMain */
		global $APPLICATION;
		/** @global $DB CDatabase */
		global $DB;

		$DB->StartTransaction();

		/** @noinspection PhpDynamicAsStaticMethodCallInspection */
		$rs = CIBlockElement::GetList(array(), array("IBLOCK_ID" => $this->iblock_id, "XML_ID" => "DEFADEMO_%"));

		while ($res = $rs->Fetch()) {
			/** @noinspection PhpDynamicAsStaticMethodCallInspection */
			if (!CIBlockElement::Delete($res["ID"]))
				break;
		}

		/** @noinspection PhpDynamicAsStaticMethodCallInspection */
		$rs = CIBlockSection::GetList(array(), array("IBLOCK_ID" => $this->iblock_id, "XML_ID" => "DEFADEMO_%"));

		while ($res = $rs->Fetch()) {
			/** @noinspection PhpDynamicAsStaticMethodCallInspection */
			if (!CIBlockSection::Delete($res["ID"]))
				break;
		}

		if ($ex = $APPLICATION->GetException())
			$strError = $ex->GetString();

		if (!empty($strError)) {
			$DB->Rollback();
			throw new Exception($strError);
		} else {
			$DB->Commit();
		}

		return true;
	}

	private function doAddDemoContent($params = array())
	{
		/** @global $APPLICATION CMain */
		global $APPLICATION;
		/** @global $DB CDatabase */
		global $DB;

		if (count($params)) {
			foreach ($params as $code => $value)
				/** @noinspection PhpDynamicAsStaticMethodCallInspection */
			COption::SetOptionString(DefaTools::MODULE_ID, "_demo_content_" . $code, $value);
		}

		$APPLICATION->RestartBuffer();

		/** @noinspection PhpDynamicAsStaticMethodCallInspection */
		$this->elementsCount = COption::GetOptionString(DefaTools::MODULE_ID, "_demo_content_cnt", 10);
		/** @noinspection PhpDynamicAsStaticMethodCallInspection */
		$this->sectionsCount = COption::GetOptionString(DefaTools::MODULE_ID, "_demo_content_sections_cnt", 10);
		/** @noinspection PhpDynamicAsStaticMethodCallInspection */
		$depthLevel = COption::GetOptionString(DefaTools::MODULE_ID, "_demo_content_create_sections_depth_level", "2");
		/** @noinspection PhpDynamicAsStaticMethodCallInspection */
		$elements2last_depth_level = COption::GetOptionString(DefaTools::MODULE_ID, "elements2last_depth_level", "Y") == "Y";

		/** @var $rs CDBResult */
		/** @noinspection PhpDynamicAsStaticMethodCallInspection */
		$rs        = CIBlock::GetById($this->iblock_id);
		$resIblock = $rs->GetNext();
		/** @noinspection PhpDynamicAsStaticMethodCallInspection */
		$resIblockType = CIBlockType::GetById($resIblock["IBLOCK_TYPE_ID"])->GetNext();

		if ($resIblockType['SECTIONS'] != "Y") {
			$this->sectionsCount       = 0;
			$depthLevel                = 0;
			$elements2last_depth_level = "N";
		}

		if (intval($this->elementsCount) <= 0) {
			/** @noinspection PhpDynamicAsStaticMethodCallInspection */
			COption::RemoveOption(DefaTools::MODULE_ID, "_demo_content_cnt");
			$this->elementsCount = 15;
		}

		if (intval($this->sectionsCount) < 0) {
			/** @noinspection PhpDynamicAsStaticMethodCallInspection */
			COption::RemoveOption(DefaTools::MODULE_ID, "_demo_content_sections_cnt");
			$this->sectionsCount = 15;
		}

		if (!is_numeric($this->sectionsCount)) {
			/** @noinspection PhpDynamicAsStaticMethodCallInspection */
			COption::RemoveOption(DefaTools::MODULE_ID, "_demo_content_sections_cnt");
			$this->sectionsCount = 15;
		}

		$DB->StartTransaction();

		$el          = new CIBlockElement;
		$se          = new CIBlockSection;
		$sNumSuccess = $eNumSuccess = array();
		$arrSections = array();

		$numSections = $this->sectionsCount;
		for ($i = 1; $i <= $depthLevel; $i++) {
			if ($i > 1)
				$numSections *= ceil($numSections / $i / 2);
			for ($k = 0; $k < $numSections; $k++) {
				if ($arSection = $this->GenerateSection($arrSections[$i - 1])) {
					if ($sID = $se->Add($arSection)) {
						$arrSections[$i][] = $sID;
						$this->__destruct();
					} else {
						/** @noinspection PhpUndefinedFieldInspection */
						$APPLICATION->ThrowException(GetMessage("DEFATOOLS_IB_DEMO_SEC_CREATE_ERR") . ': ' . $el->LAST_ERROR);
						break;
					}
				}
			}
		}

		/** @noinspection PhpDynamicAsStaticMethodCallInspection */
		$arIblockFields = CIBlock::GetFields($this->iblock_id);

		if ($elements2last_depth_level)
			$arrSections = array_pop($arrSections);

		/** @noinspection PhpDynamicAsStaticMethodCallInspection */
		if (COption::GetOptionString(DefaTools::MODULE_ID, "_demo_content_add_els_in_existed_sections", "Y") == "Y"
			|| $arIblockFields["IBLOCK_SECTION"]["IS_REQUIRED"] == "Y"
		) {

			/** @noinspection PhpDynamicAsStaticMethodCallInspection */
			$rs = CIBlockSection::GetList(array(), array("IBLOCK_ID" => $this->iblock_id));

			while ($res = $rs->Fetch()) {
				if ($elements2last_depth_level) {
					if ($res["RIGHT_MARGIN"] - $res["LEFT_MARGIN"] == 1)
						$arrSections[] = $res["ID"];
				} else {
					$arrSections[] = $res["ID"];
				}
			}
		}


		if (empty($arrSections) && $arIblockFields["IBLOCK_SECTION"]["IS_REQUIRED"] == "Y") {


			if ($arSection = $this->GenerateSection()) {
				if ($sID = $se->Add($arSection)) {
					$sNumSuccess[] = $sID;
					$arrSections[] = $sID;
					$this->__destruct();
				} else {
					/** @noinspection PhpUndefinedFieldInspection */
					$APPLICATION->ThrowException(GetMessage("DEFATOOLS_IB_DEMO_SEC_CREATE_ERR") . ': ' . $el->LAST_ERROR);

					return false;
				}
			}
		}


		for ($i = 0; $i < $this->elementsCount; $i++) {
			if ($arElement = $this->GenerateElement($arrSections)) {

				if ($eID = $el->Add($arElement, false, true, true)) {
					$eNumSuccess[] = $eID;
					$this->__destruct();
				} else {
					/** @noinspection PhpUndefinedFieldInspection */
					$APPLICATION->ThrowException(GetMessage("DEFATOOLS_IB_DEMO_EL_CREATE_ERR") . ': ' . $el->LAST_ERROR);

					return false;
				}
			}
		}


		$arCatInitData = self::GetInitDataForCatalog();

		/** @noinspection PhpDynamicAsStaticMethodCallInspection */
		if (
			!empty($eNumSuccess)
			&& CModule::IncludeModule("catalog")
			&& CCatalog::GetByID($this->iblock_id)
			&& is_array($arCatInitData)
			&& !empty($arCatInitData["PRICE_TYPES"])
		) {

			$arVats        = $arCatInitData["VATS"];
			$arCurrency    = $arCatInitData["CURRENCY"];
			$arExtra       = $arCatInitData["EXTRA"];
			$arPriceTypes  = $arCatInitData["PRICE_TYPES"];
			$base_price_id = $arCatInitData["BASE_PRICE_ID"];

			$arConfirm = array("Y", "N");

			foreach ($eNumSuccess as $PRODUCT_ID) {

				$arProdFields = array(
					"ID" => $PRODUCT_ID,
					"QUANTITY" => rand(1, 100000),
					"QUANTITY_TRACE" => $arConfirm[rand(0, 1)],
					"WEIGHT" => rand(100, 10000),
					"VAT_INCLUDED" => $arConfirm[rand(0, 1)],
				);

				if (!empty($arVats)) {
					$arProdFields["VAT_ID"] = $arVats[rand(0, count($arVats) - 1)];
				}

				/** @noinspection PhpDynamicAsStaticMethodCallInspection */
				CCatalogProduct::Add($arProdFields);

				$prices_count = rand(2, 6);

				$FROM       = 1;
				$TO         = rand(2, 10);
				$arDiapazon = array();

				for ($i = 1; $i <= $prices_count; $i++) {
					$arPriceFields = Array(
						"PRODUCT_ID" => $PRODUCT_ID,
						"CATALOG_GROUP_ID" => $base_price_id,
						"PRICE" => rand(1, 99999),
						"CURRENCY" => $arCurrency[rand(0, count($arCurrency) - 1)],
						"QUANTITY_FROM" => $FROM,
						"QUANTITY_TO" => ($i == $prices_count) ? "" : $TO
					);

					$arDiapazon[$i] = array("FROM" => $FROM, "TO" => ($i == $prices_count) ? "" : $TO);

					$FROM = $arPriceFields["QUANTITY_TO"] + 1;
					$TO   = rand($FROM + 1, $i * 10);

					/** @noinspection PhpDynamicAsStaticMethodCallInspection */
					CPrice::Add($arPriceFields);
				}

				foreach ($arPriceTypes as $arPriceType) {
					if ($arPriceType["BASE"] == "Y")
						continue;

					foreach ($arDiapazon as $arFromTo) {
						$arPriceFields = Array(
							"PRODUCT_ID" => $PRODUCT_ID,
							"CATALOG_GROUP_ID" => $arPriceType["ID"],
							"PRICE" => rand(1, 99999),
							"CURRENCY" => $arCurrency[rand(0, count($arCurrency) - 1)],
							"QUANTITY_FROM" => $arFromTo["FROM"],
							"QUANTITY_TO" => $arFromTo["TO"]
						);

						if (!empty($arExtra)) {
							$arPriceFields["EXTRA_ID"] = $arExtra[rand(0, count($arExtra) - 1)];
						}

						/** @noinspection PhpDynamicAsStaticMethodCallInspection */
						CPrice::Add($arPriceFields);
					}
				}

			}

		}


		if ($ex = $APPLICATION->GetException())
			$strError = $ex->GetString();

		if (!empty($strError)) {
			ShowError($strError);
			$DB->Rollback();
		} else {
			$DB->Commit();
			/** @noinspection PhpDynamicAsStaticMethodCallInspection */
			CAdminMessage::ShowMessage(array("TYPE" => "OK", "MESSAGE" => GetMessage("DEFATOOLS_IB_DEMO_EL_CREATED_NUM")
			. ": " . count($eNumSuccess) . (count($sNumSuccess) > 0 ? "<br>"
				. GetMessage("DEFATOOLS_IB_DEMO_SECTIONS") . ": " . count($sNumSuccess) : "")));
			echo "<div align=\"center\"><a style=\"font-size: 20px\" href=\"javascript:window.location=window.location\">"
				. GetMessage("DEFATOOLS_IB_DEMO_REFRESH_PAGE") . "</a></div>";
		}
		die();
	}

	private function IsInTmpDir($file)
	{
		if (substr($file, 0, strlen($this->tmpPath)) == $this->tmpPath)
			return true;

		return false;
	}

	private function CheckTmpDirPath()
	{
		CheckDirPath($this->tmpPath . "/");
	}

	private function Copy($file)
	{
		$fileinfo = pathinfo($file);
		$newFile  = $this->sourcePath . "/tmp/" . $fileinfo["filename"] . "_" . md5(uniqid(mt_rand(), true)) . "." . $fileinfo["extension"];

		if (copy($file, $newFile)) {
			$this->tmpFiles[] = $newFile;

			return $newFile;
		}

		return false;
	}

	private function _GetDate($time = "now")
	{
		if ($time == "past")
			$year = rand(2000, date("Y") - 1);
		elseif ($time == "future")
			$year = rand(intval(date("Y")) + 1, intval(date("Y")) + 10); else
			$year = rand(intval(date("Y")) - 10, intval(date("Y")) + 10);

		return ConvertTimeStamp(
			mktime(
				0, 0, 0,
				rand(1, 28), rand(1, 12), $year
			), "SHORT", SITE_ID
		);
	}

	private function _GetMapPoint()
	{

		$precision = 1000000;

		return implode(
			",",
			array(
				rand(50 * $precision, 60 * $precision) / $precision,
				rand(30 * $precision, 50 * $precision) / $precision
			)
		);
	}

	private function _GetFile($type = "", $count = 1)
	{
		/** @global $APPLICATION CMain */
		global $APPLICATION;

		$arImageTypes = array("jpeg", "jpg", "png", "gif", "bmp");

		if ($type == "image")
			$type = "jpg,gif,png";
		elseif ($type == "video")
			$type = "flv";

		if (empty($type)) {
			$types       = "*";
			$arFileTypes = array("doc", "xls", "pdf", "jpg", "png", "mp3", "flv");
		} elseif (strpos($type, ",") > 0) {
			$types       = explode(",", $type);
			$arFileTypes = $types;
			TrimArr($types, true);
			$types = "[" . implode("|", $types) . "]+";
		} else {
			$types       = $type;
			$arFileTypes = array($type);
		}

		$files = array();

		self::CheckTmpDirPath();

		foreach (glob($this->sourcePath . "/*") as $file) {
			if (preg_match("/.*\." . $types . "$/", $file)) {
				if ($_tmpFile = self::Copy($file)) {
					/** @noinspection PhpDynamicAsStaticMethodCallInspection */
					$files[] = CFile::MakeFileArray($_tmpFile);
				}
			}
		}

		if (empty($files)) {
			foreach ($arFileTypes as $file_type) {
				$demo_file_name     = "demo_file." . trim($file_type);
				$abs_demo_file_name = $this->tmpPath . "/" . $demo_file_name;

				if (in_array(trim($file_type), $arImageTypes)) {

					$arImgSize = array(
						"w" => mt_rand(150, 600),
						"h" => mt_rand(150, 600)
					);

					$image_handle = @imagecreate($arImgSize["w"], $arImgSize["h"]);
					if ($image_handle) {
						imagecolorallocate($image_handle, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));
						imagestring($image_handle, 5, 50, 50, $arImgSize["w"] . 'x' . $arImgSize["h"], imagecolorallocate($image_handle, 0, 0, 0));
						if ($file_type == "jpeg" || $file_type == "jpg") {
							imagejpeg($image_handle, $abs_demo_file_name);
						} elseif ($file_type == "png") {
							imagepng($image_handle, $abs_demo_file_name);
						} elseif ($file_type == "gif") {
							imagegif($image_handle, $abs_demo_file_name);
						} elseif ($file_type == "bmp") {
							imagewbmp($image_handle, $abs_demo_file_name);
						}
						imagedestroy($image_handle);
					}
				} elseif (!file_exists($abs_demo_file_name)) {
					mt_srand((double)microtime() * 1000000);
					$put_content = str_pad(" ", mt_rand(10000, 50000));
					fputs(fopen($abs_demo_file_name, "w"), $put_content);
				}

				/** @noinspection PhpDynamicAsStaticMethodCallInspection */
				$files[] = CFile::MakeFileArray($abs_demo_file_name);
			}
		}

		shuffle($files);
		$files = array_slice($files, 0, $count);

		if (empty($files)) {
			$APPLICATION->ThrowException(GetMessage("DEFATOOLS_IB_DEMO_FILES_NOT_FOUND", array("#TYPES#" => $types)));

			return false;
		}

		return count($files) == 1 ? $files[0] : $files;
	}

	private function _GetTextPart($length = 0, $renew = false, $type = "text")
	{

		$this->GetText($renew);

		$text = $this->text;

		if ($type == "text" || $length > 0)
			$text = strip_tags($text);

		if ($length > 0) {

			if ($length >= strlen($text))
				return $text;

			$text = $this->ucfirst(trim(substr($text, strpos($text, " ", rand(0, strlen($text) - $length)), $length * 2)));
		}

		return $text;

	}

	private function ucfirst($str)
	{

		if ($this->isUTF) {
			$str = mb_ereg_replace('^[\ ]+', '', $str);
			$str = mb_strtoupper(mb_substr($str, 0, 1, "UTF-8"), "UTF-8") . mb_substr($str, 1, mb_strlen($str), "UTF-8");
		} else {
			$str[0] = ToUpper($str[0]);
		}

		return $str;
	}

	private function GetText($renew = false)
	{
		$error_number = 0;
		$error_text   = "";
		if (empty($this->text) || empty($this->textName) || $renew) {
			$res = str_replace("\n", "", QueryGetData("vesna.yandex.ru", 80, "/all.xml", "mix="
				. urlencode("astronomy,geology,gyroscope,literature,marketing,mathematics,music,polit,agrobiologia,law,psychology,geography,physics,philosophy,chemistry,estetica"),
				$error_number, $error_text, "GET"));

			if (!$this->isUTF)
				$res = utf8win1251($res);

			preg_match("/\<h1 [^\>]+\>([^\<]+)\<\/h1\>/", $res, $name);
			$this->textName = substr($name[1], 7, -1);

			preg_match("/<\/h1\>(.*)/is", $res, $text);
			$text = substr($text[1], 0, strpos($text[1], "</div>"));

			$this->text = $text;
		}

		return array($this->textName, $this->text);
	}

	private function GenerateSection($parentsArray = array())
	{

		if (!is_array($parentsArray))
			$parentsArray = array();

		$this->GetText(true);

		$active = "Y";

		/** @noinspection PhpDynamicAsStaticMethodCallInspection */
		if (COption::GetOptionString(DefaTools::MODULE_ID, "_demo_content_notactive", "Y") == "Y")
			$active = (rand(0, 100) > 20) ? "Y" : "N";

		/** @noinspection PhpDynamicAsStaticMethodCallInspection */
		$section = array(
			"IBLOCK_SECTION_ID" => $parentsArray[array_rand($parentsArray)],
			"SORT" => rand(1, 1000) * 10,
			"IBLOCK_ID" => $this->iblock_id,
			"XML_ID" => "DEFADEMO_" . RandString(5),
			"CODE" => CUtil::translit($this->textName, "ru"),
			"ACTIVE" => $active,
			"NAME" => $this->textName,
			"DESCRIPTION" => $this->_GetTextPart(0, false, "html"),
			"DESCRIPTION_TYPE" => "html",
			"PICTURE" => $this->_GetFile("image"),
		);

		return $section;
	}

	private function GenerateElement($parentsArray = array())
	{
		/** @global $APPLICATION CMain */
		global $APPLICATION;

		/** @global $DB CDatabase */
		global $DB;

		if (!is_array($parentsArray))
			$parentsArray = array();

		$this->GetText(true);

		$active = "Y";

		/** @noinspection PhpDynamicAsStaticMethodCallInspection */
		if (COption::GetOptionString(DefaTools::MODULE_ID, "_demo_content_notactive", "Y") == "Y")
			$active = (rand(0, 10) > 2) ? "Y" : "N";

		/** @noinspection PhpDynamicAsStaticMethodCallInspection */
		$element = array(
			"IBLOCK_SECTION_ID" => $parentsArray[array_rand($parentsArray)],
			"SORT" => rand(1, 1000) * 10,
			"IBLOCK_ID" => $this->iblock_id,
			"XML_ID" => "DEFADEMO_" . RandString(5),
			"CODE" => CUtil::translit($this->textName, "ru"),
			"TAGS" => ToLower(str_replace(array(" ", ".", ":", ";", "?", "!", ",,"), ",", $this->textName)),
			"ACTIVE" => $active,
			"ACTIVE_FROM" => $this->_GetDate("past"),
			"ACTIVE_TO" => $this->_GetDate("future"),
			"NAME" => $this->textName,
			"PREVIEW_TEXT_TYPE" => "text",
			"PREVIEW_TEXT" => $this->_GetTextPart(200, false),
			"DETAIL_TEXT_TYPE" => "html",
			"DETAIL_TEXT" => $this->_GetTextPart(0, false, "html"),
			"DETAIL_PICTURE" => $this->_GetFile("image"),
			"PREVIEW_PICTURE" => $this->_GetFile("image"),
		);


		/** @noinspection PhpDynamicAsStaticMethodCallInspection */
		$rsProps = CIBlock::GetProperties($this->iblock_id);

		while ($resProp = $rsProps->Fetch()) {
			$rsTopicID = $rsElementXmlID = $rsUser = $val = false;

			$count = 1;
			if ($resProp["MULTIPLE"] == "Y")
				$count = rand(0, 5);

			switch ($resProp["PROPERTY_TYPE"]) {

				case "S":
					for ($i = 0; $i < $count; $i++) {

						switch ($resProp["USER_TYPE"]) {

							case "":
								$val[] = $this->_GetTextPart(30);
								break;

							case "DateTime":
								$val[] = $this->_GetDate();
								$val   = array_unique($val);
								break;

							case "ElementXmlID":
								if (!$rsElementXmlID)
									/** @noinspection PhpDynamicAsStaticMethodCallInspection */
								$rsElementXmlID = CIBlockElement::GetList(array("rand" => ""), array(), false, false, array("XML_ID"));

								if ($_val = $rsElementXmlID->Fetch())
									$val[] = $_val["XML_ID"];

								$val = array_unique($val);
								break;

							case "TopicID":
								if (CModule::IncludeModule("forum")) {

									if (!$rsTopicID) {
										/** @noinspection PhpDynamicAsStaticMethodCallInspection */
										$rsTopicID = CForumTopic::GetList(array("rand" => ""), array());
									}

									if ($_val = $rsTopicID->Fetch())
										$val[] = $_val["ID"];

									shuffle($val);
									$val = array_unique($val);
								}
								break;

							case "UserID":
								if (!$rsUser) {
									$by    = '';
									$order = 'asc';
									/** @noinspection PhpDynamicAsStaticMethodCallInspection */
									$rsUser = CUser::GetList($by, $order, array("!ID" => "1"));
								}

								if ($res = $rsUser->GetNext())
									$val[] = $res["ID"];

								$val = array_unique($val);
								break;

							case "map_yandex":
							case "map_google":
								$val[] = $this->_GetMapPoint();
								break;

							case "HTML":
								$val[] = array("VALUE" => array("TEXT" => "<strong>" . $this->_GetTextPart(100) . "</strong>", "TYPE" => "html"));
								break;

							case "DefaToolsFileManEx":
								if (CModule::IncludeModule("fileman")) {

									$arFilter["MIN_PERMISSION"] = "R";
									/** @noinspection PhpDynamicAsStaticMethodCallInspection */
									CFileMan::GetDirList(array(($site_id = ''), ($path = '')), $arDirs, $arFiles, $arFilter, array(), "DF");

									$arAll  = array_merge($arDirs, $arFiles);
									$values = array();
									foreach ($arAll as $v) {
										if (
											($v["TYPE"] == "D" && in_array($v["ABS_PATH"], array("/bitrix", "/upload")))
											||
											($v["TYPE"] == "F" && substr($v["ABS_PATH"], 0, 2) == "/.")
											||
											($v["TYPE"] == "F" && in_array($v["NAME"], array("404.php", "urlrewrite.php")))
											||
											($v["TYPE"] == "F" && substr($v["NAME"], -4) != ".php")
											||
											($v["TYPE"] == "F" && substr($v["NAME"], -8) == "_inc.php")
											||
											($v["TYPE"] == "D" && !file_exists($v["PATH"] . "/index.php"))
										)
											continue;

										$values[] = $v["TYPE"] == "D" ? $v["ABS_PATH"] . "/index.php" : $v["ABS_PATH"];
									}

									$val[] = $values[rand(0, count($values) - 1)];

									$val = array_unique($val);
								}
								break;

							case "video":
								// doesn't work in bitirx
								break;

							default:
								$APPLICATION->ThrowException(GetMessage("DEFATOOLS_IB_DEMO_PROP_TYPE")
								. " &laquo;" . $resProp["PROPERTY_TYPE"] . ":" . $resProp["USER_TYPE"] . "&raquo; "
								. GetMessage("DEFATOOLS_IB_DEMO_DONT_SUPPORT"));
								break;

						}
					}
					break;

				case "N":
					for ($i = 0; $i < $count; $i++)
						$val[] = rand(0, 10000);
					break;

				case "L":
					$values = array();

					/** @var $enums CDBResult */
					/** @noinspection PhpDynamicAsStaticMethodCallInspection */
					$enums = CIBlockPropertyEnum::GetList(array(), Array("IBLOCK_ID" => $resProp["IBLOCK_ID"], "PROPERTY_ID" => $resProp["ID"]));
					while ($e = $enums->Fetch()) {
						$values[] = $e["ID"];
					}

					for ($i = 0; $i < $count; $i++) {
						$val[] = $values[rand(0, count($values) - 1)];
					}
					break;

				case "E":
					/** @noinspection PhpDynamicAsStaticMethodCallInspection */
					$linkRs = CIBlockElement::GetList(array("rand" => ""), array("IBLOCK_ID" => $resProp["LINK_IBLOCK_ID"]), false, false, array("ID"));

					for ($i = 0; $i < $count; $i++) {
						if ($_val = $linkRs->Fetch())
							$val[] = $_val["ID"];
					}
					break;

				case "G":
					$linkRs = $DB->Query("SELECT * FROM b_iblock_section WHERE IBLOCK_ID = '" . $resProp["LINK_IBLOCK_ID"] . "' ORDER BY RAND()");

					for ($i = 0; $i < $count; $i++) {
						if ($_val = $linkRs->Fetch())
							$val[] = $_val["ID"];
					}
					break;

				case "F":
					// TODO: проверить наполнения свойства множественной загрузки файлов. сейчас лезут ошибки:
					// Warning: copy(): The first argument to copy() function cannot be a directory
					for ($i = 0; $i < $count; $i++) {
						$val = $this->_GetFile($resProp["FILE_TYPE"], $count);
					}
					break;

				default:
					$APPLICATION->ThrowException(GetMessage("DEFATOOLS_IB_DEMO_PROP_TYPE") . " &laquo;" . $resProp["PROPERTY_TYPE"] . "&raquo; " . GetMessage("DEFATOOLS_IB_DEMO_DONT_SUPPORT"));

					return false;
					break;
			}

			$element["PROPERTY_VALUES"][$resProp["ID"]] = (count($val) == 1) ? $val[0] : $val;

		}

		return $element;

	}

	private function GetInitDataForCatalog()
	{
		if (!CModule::IncludeModule("catalog"))
			return false;

		$arVats = array();
		/** @var $dbResVat CDBResult */
		/** @noinspection PhpDynamicAsStaticMethodCallInspection */
		$dbResVat = CCatalogVat::GetList(array(), array("ACTIVE" => "Y"));
		while ($arVat = $dbResVat->Fetch()) {
			$arVats[] = $arVat["ID"];
		}

		$arCurrency = array();
		/** @var $dbCurrency CDBResult */
		/** @noinspection PhpUndefinedClassInspection */
		$dbCurrency = CCurrency::GetList(($b = "sort"), ($order = "asc"), LANGUAGE_ID);
		while ($arCurr = $dbCurrency->Fetch()) {
			$arCurrency[] = $arCurr["CURRENCY"];
		}

		$arExtra = array();
		/** @noinspection PhpDynamicAsStaticMethodCallInspection */
		$dbExtra = CExtra::GetList(($b = "sort"), ($order = "asc"));
		while ($arOneExtra = $dbExtra->Fetch()) {
			$arExtra[] = $arOneExtra["ID"];
		}

		$arPriceTypes = $arPriceTypesID = array();
		/** @noinspection PhpDynamicAsStaticMethodCallInspection */
		$dbPriceType = CCatalogGroup::GetList(array("SORT" => "ASC"));

		$base_price_id = null;

		while ($arPriceType = $dbPriceType->Fetch()) {
			$arPriceTypes[$arPriceType["ID"]] = $arPriceType;

			if ($arPriceType["BASE"] == "Y") {
				$base_price_id = $arPriceType["ID"];
			}
		}

		return array(
			"VATS" => $arVats,
			"CURRENCY" => $arCurrency,
			"EXTRA" => $arExtra,
			"PRICE_TYPES" => $arPriceTypes,
			"BASE_PRICE_ID" => $base_price_id
		);
	}

	/**
	 * Проверяет права пользователя на модификацию демо инфоблока.
	 * Модификация разрешена только создателю демо элемента.
	 *
	 * @param $arFields
	 * @return bool
	 */
	public function CheckElementModifyPermissions(&$arFields)
	{
		/** @global $USER CUser */
		global $USER;
		/** @global $APPLICATION CMain */
		global $APPLICATION;

		/** @noinspection PhpDynamicAsStaticMethodCallInspection */
		$element = CIBlockElement::GetList(array(), array("ID" => $arFields['ID']), false, false, array("XML_ID", "CREATED_BY"))->Fetch();

		if (
			!strncmp($element['XML_ID'], 'DEFADEMO', 8)
			&& !($USER->GetID() == $element['CREATED_BY'])
		) {
			$APPLICATION->throwException(GetMessage("DEFATOOLS_IB_DEMO_ELEMENT_OWNER_CHECK_FAILS"));

			return false;
		}

		return true;
	}

	/**
	 * Проверяет права пользователя на модификацию демо раздела.
	 * Модификация разрешена только создателю демо раздела.
	 *
	 * @param $arFields
	 * @return bool
	 */
	public function CheckSectionModifyPermissions(&$arFields)
	{
		/** @noinspection PhpDynamicAsStaticMethodCallInspection */
		$element = CIBlockSection::GetList(array(), array("ID" => $arFields['ID']), false)->Fetch();

		/** @global $USER CUser */
		global $USER;
		/** @global $APPLICATION CMain */
		global $APPLICATION;

		if (
			!strncmp($element['XML_ID'], 'DEFADEMO', 8)
			&& !($USER->GetID() == $element['CREATED_BY'])
		) {
			$APPLICATION->throwException(GetMessage("DEFATOOLS_IB_DEMO_SECTION_OWNER_CHECK_FAILS"));

			return false;
		}

		return true;
	}
}
