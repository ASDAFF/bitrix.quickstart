<?
IncludeModuleLangFile(__FILE__);

class CComponentUtil
{
	function __IncludeLang($filePath, $fileName, $lang = False)
	{
		if ($lang === False)
			$lang = LANGUAGE_ID;

		if ($lang != "en" && $lang != "ru")
		{
			if (file_exists(($fname = $_SERVER["DOCUMENT_ROOT"].$filePath."/lang/".LangSubst($lang)."/".$fileName)))
				__IncludeLang($fname);
		}

		if (file_exists(($fname = $_SERVER["DOCUMENT_ROOT"].$filePath."/lang/".$lang."/".$fileName)))
			__IncludeLang($fname);
	}

	function PrepareVariables(&$arData)
	{
		UnSet($arData["NEW_COMPONENT_TEMPLATE"]);

		if ($arData["SEF_MODE"] == "Y")
		{
			UnSet($arData["VARIABLE_ALIASES"]);
			UnSet($arData["SEF_URL_TEMPLATES"]);

			foreach ($arData as $dataKey => $dataValue)
			{
				if (SubStr($dataKey, 0, StrLen("SEF_URL_TEMPLATES_")) == "SEF_URL_TEMPLATES_")
				{
					$arData["SEF_URL_TEMPLATES"][SubStr($dataKey, StrLen("SEF_URL_TEMPLATES_"))] = $dataValue;
					unset($arData[$dataKey]);

					if (preg_match_all("'(\?|&)(.+?)=#([^#]+?)#'is", $dataValue, $arMatches, PREG_SET_ORDER))
					{
						foreach ($arMatches as $arMatch)
							$arData["VARIABLE_ALIASES"][SubStr($dataKey, StrLen("SEF_URL_TEMPLATES_"))][$arMatch[3]] = $arMatch[2];
					}
				}
				elseif (SubStr($dataKey, 0, StrLen("VARIABLE_ALIASES_")) == "VARIABLE_ALIASES_")
				{
					unset($arData[$dataKey]);
				}
			}
		}
		else
		{
			UnSet($arData["VARIABLE_ALIASES"]);
			UnSet($arData["SEF_URL_TEMPLATES"]);

			foreach ($arData as $dataKey => $dataValue)
			{
				if (SubStr($dataKey, 0, StrLen("SEF_URL_TEMPLATES_")) == "SEF_URL_TEMPLATES_")
				{
					unset($arData[$dataKey]);
				}
				elseif (SubStr($dataKey, 0, StrLen("VARIABLE_ALIASES_")) == "VARIABLE_ALIASES_")
				{
					$arData["VARIABLE_ALIASES"][SubStr($dataKey, StrLen("VARIABLE_ALIASES_"))] = $dataValue;
					unset($arData[$dataKey]);
				}
			}
		}
	}

	function __ShowError($errorMessage)
	{
		if (StrLen($errorMessage) > 0)
			echo "<font color=\"#FF0000\">".$errorMessage."</font>";
	}

	function __BuildTree($arPath, &$arTree, &$arComponent, $level = 1)
	{
		$arBXTopComponentCatalogLevel = array("content", "service", "communication", "e-store", "utility");
		$arBXTopComponentCatalogLevelSort = array(600, 700, 800, 900, 1000);

		if (!is_array($arTree["#"]))
			$arTree["#"] = array();

		if (!array_key_exists($arPath["ID"], $arTree["#"]))
		{
			$arTree["#"][$arPath["ID"]] = array();
			$arTree["#"][$arPath["ID"]]["@"] = array();
			$arTree["#"][$arPath["ID"]]["@"]["NAME"] = "";
			$arTree["#"][$arPath["ID"]]["@"]["SORT"] = IntVal($arPath["SORT"]);
			if ($level == 1 && in_array($arPath["ID"], $arBXTopComponentCatalogLevel))
			{
				$arTree["#"][$arPath["ID"]]["@"]["NAME"] = GetMessage("VRT_COMP_CAT_".StrToUpper($arPath["ID"]));
				$arTree["#"][$arPath["ID"]]["@"]["SORT"] = IntVal($arBXTopComponentCatalogLevelSort[array_search($arPath["ID"], $arBXTopComponentCatalogLevel)]);
			}
			if (StrLen($arTree["#"][$arPath["ID"]]["@"]["NAME"]) <= 0)
				$arTree["#"][$arPath["ID"]]["@"]["NAME"] = $arPath["NAME"];
			if ($arTree["#"][$arPath["ID"]]["@"]["SORT"] <= 0)
				$arTree["#"][$arPath["ID"]]["@"]["SORT"] = 100;
		}

		if (array_key_exists("CHILD", $arPath))
		{
			CComponentUtil::__BuildTree($arPath["CHILD"], $arTree["#"][$arPath["ID"]], $arComponent, $level + 1);
		}
		else
		{
			if (!is_array($arTree["#"][$arPath["ID"]]["*"]))
				$arTree["#"][$arPath["ID"]]["*"] = array();

			$arTree["#"][$arPath["ID"]]["*"][$arComponent["NAME"]] = $arComponent;
		}
	}

	public static function isComponent($componentPath)
	{
		$bDirectoryExists = file_exists($_SERVER["DOCUMENT_ROOT"].$componentPath)
			&& is_dir($_SERVER["DOCUMENT_ROOT"].$componentPath);
		if(!$bDirectoryExists)
			return false;

		$bComponentExists = file_exists($_SERVER["DOCUMENT_ROOT"].$componentPath."/component.php")
			&& is_file($_SERVER["DOCUMENT_ROOT"].$componentPath."/component.php");
		if($bComponentExists)
			return true;

		$bClassExists = file_exists($_SERVER["DOCUMENT_ROOT"].$componentPath."/class.php")
			&& is_file($_SERVER["DOCUMENT_ROOT"].$componentPath."/class.php");
		if($bClassExists)
			return true;

		return false;
	}

	function __GetComponentsTree($filterNamespace = False, $arNameFilter = False)
	{
		$arTree = array();

		if ($handle = @opendir($_SERVER["DOCUMENT_ROOT"]."/bitrix/components"))
		{
			while (($file = readdir($handle)) !== false)
			{
				if ($file == "." || $file == "..")
					continue;

				if (is_dir($_SERVER["DOCUMENT_ROOT"]."/bitrix/components/".$file))
				{
					if (CComponentUtil::isComponent("/bitrix/components/".$file))
					{
						// It's component
						if ($filterNamespace !== False && StrLen($filterNamespace) > 0)
							continue;
						if ($arNameFilter !== False && !CComponentUtil::CheckComponentName($file, $arNameFilter))
							continue;

						if (file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/components/".$file."/.description.php"))
						{
							CComponentUtil::__IncludeLang("/bitrix/components/".$file, ".description.php");

							$arComponentDescription = array();
							$componentName = $file;
							include($_SERVER["DOCUMENT_ROOT"]."/bitrix/components/".$file."/.description.php");

							if (array_key_exists("PATH", $arComponentDescription) && array_key_exists("ID", $arComponentDescription["PATH"]))
							{
								$arComponent = array();
								$arComponent["NAME"] = $file;
								$arComponent["NAMESPACE"] = "";
								$arComponent["TITLE"] = Trim($arComponentDescription["NAME"]);
								$arComponent["DESCRIPTION"] = $arComponentDescription["DESCRIPTION"];
								if (array_key_exists("ICON", $arComponentDescription))
									$arComponent["ICON"] = "/bitrix/components/".$file.$arComponentDescription["ICON"];
								if (array_key_exists("COMPLEX", $arComponentDescription) && $arComponentDescription["COMPLEX"] == "Y")
									$arComponent["COMPLEX"] = "Y";
								else
									$arComponent["COMPLEX"] = "N";
								$arComponent["SORT"] = IntVal($arComponentDescription["SORT"]);
								if ($arComponent["SORT"] <= 0)
									$arComponent["SORT"] = 100;

								$arComponent["SCREENSHOT"] = array();
								if (array_key_exists("SCREENSHOT", $arComponentDescription))
								{
									if (!is_array($arComponentDescription["SCREENSHOT"]))
										$arComponentDescription["SCREENSHOT"] = array($arComponentDescription["SCREENSHOT"]);

									for ($i = 0, $cnt = count($arComponentDescription["SCREENSHOT"]); $i < $cnt; $i++)
										$arComponent["SCREENSHOT"][] = "/bitrix/components/".$file.$arComponentDescription["SCREENSHOT"][$i];
								}

								CComponentUtil::__BuildTree($arComponentDescription["PATH"], $arTree, $arComponent);
							}
						}
					}
					else
					{
						// It's not a component
						if ($filterNamespace !== False && (StrLen($filterNamespace) <= 0 || $filterNamespace != $file))
							continue;

						if ($handle1 = @opendir($_SERVER["DOCUMENT_ROOT"]."/bitrix/components/".$file))
						{
							while (($file1 = readdir($handle1)) !== false)
							{
								if ($file1 == "." || $file1 == "..")
									continue;

								if (is_dir($_SERVER["DOCUMENT_ROOT"]."/bitrix/components/".$file."/".$file1))
								{
									if (CComponentUtil::isComponent("/bitrix/components/".$file."/".$file1))
									{
										if ($arNameFilter !== False && !CComponentUtil::CheckComponentName($file1, $arNameFilter))
											continue;
										// It's component
										if (file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/components/".$file."/".$file1."/.description.php"))
										{

											CComponentUtil::__IncludeLang("/bitrix/components/".$file."/".$file1, ".description.php");

											$arComponentDescription = array();
											$componentName = $file.":".$file1;
											include($_SERVER["DOCUMENT_ROOT"]."/bitrix/components/".$file."/".$file1."/.description.php");

											if (array_key_exists("PATH", $arComponentDescription) && array_key_exists("ID", $arComponentDescription["PATH"]))
											{
												$arComponent = array();
												$arComponent["NAME"] = $file.":".$file1;
												$arComponent["NAMESPACE"] = $file;
												$arComponent["TITLE"] = Trim($arComponentDescription["NAME"]);
												$arComponent["DESCRIPTION"] = $arComponentDescription["DESCRIPTION"];
												if (array_key_exists("ICON", $arComponentDescription))
													$arComponent["ICON"] = "/bitrix/components/".$file."/".$file1.$arComponentDescription["ICON"];
												if (array_key_exists("COMPLEX", $arComponentDescription) && $arComponentDescription["COMPLEX"] == "Y")
													$arComponent["COMPLEX"] = "Y";
												else
													$arComponent["COMPLEX"] = "N";
												$arComponent["SORT"] = IntVal($arComponentDescription["SORT"]);
												if ($arComponent["SORT"] <= 0)
													$arComponent["SORT"] = 100;

												$arComponent["SCREENSHOT"] = array();
												if (array_key_exists("SCREENSHOT", $arComponentDescription))
												{
													if (!is_array($arComponentDescription["SCREENSHOT"]))
														$arComponentDescription["SCREENSHOT"] = array($arComponentDescription["SCREENSHOT"]);

													for ($i = 0, $cnt = count($arComponentDescription["SCREENSHOT"]); $i < $cnt; $i++)
														$arComponent["SCREENSHOT"][] = "/bitrix/components/".$file."/".$file1.$arComponentDescription["SCREENSHOT"][$i];
												}

												CComponentUtil::__BuildTree($arComponentDescription["PATH"], $arTree, $arComponent);
											}
										}
									}
								}
							}
							@closedir($handle1);
						}
					}
				}
			}
			@closedir($handle);
		}

		return $arTree;
	}

	function __TreeFolderCompare($a, $b)
	{
		if ($a["@"]["SORT"] < $b["@"]["SORT"] || $a["@"]["SORT"] == $b["@"]["SORT"] && StrToLower($a["@"]["NAME"]) < StrToLower($b["@"]["NAME"]))
			return -1;
		elseif ($a["@"]["SORT"] > $b["@"]["SORT"] || $a["@"]["SORT"] == $b["@"]["SORT"] && StrToLower($a["@"]["NAME"]) > StrToLower($b["@"]["NAME"]))
			return 1;
		else
			return 0;
	}

	function __TreeItemCompare($a, $b)
	{
		if ($a["COMPLEX"] == "Y" && $b["COMPLEX"] == "Y"
			|| $a["COMPLEX"] != "Y" && $b["COMPLEX"] != "Y")
		{
			if ($a["SORT"] < $b["SORT"] || $a["SORT"] == $b["SORT"] && StrToLower($a["TITLE"]) < StrToLower($b["TITLE"]))
				return -1;
			elseif ($a["SORT"] > $b["SORT"] || $a["SORT"] == $b["SORT"] && StrToLower($a["TITLE"]) > StrToLower($b["TITLE"]))
				return 1;
			else
				return 0;
		}
		else
		{
			if ($a["COMPLEX"] == "Y")
				return -1;
			if ($b["COMPLEX"] == "Y")
				return 1;
		}
	}

	function __SortComponentsTree(&$arTree)
	{
		uasort($arTree, array("CComponentUtil", "__TreeFolderCompare"));
		foreach ($arTree as $key => $value)
		{
			if (array_key_exists("#", $arTree[$key]))
				CComponentUtil::__SortComponentsTree($arTree[$key]["#"]);
			if (array_key_exists("*", $arTree[$key]))
				uasort($arTree[$key]["*"], array("CComponentUtil", "__TreeItemCompare"));
		}
	}

	function GetComponentsTree($filterNamespace = False, $arNameFilter = False)
	{
		$arTree = CComponentUtil::__GetComponentsTree($filterNamespace, $arNameFilter);

		CComponentUtil::__SortComponentsTree($arTree["#"]);

		return $arTree;
	}

	function GetNamespaceList()
	{
		$arNamespaces = array();

		if ($handle = @opendir($_SERVER["DOCUMENT_ROOT"]."/bitrix/components"))
		{
			while (($file = readdir($handle)) !== false)
			{
				if ($file == "." || $file == "..")
					continue;

				if (is_dir($_SERVER["DOCUMENT_ROOT"]."/bitrix/components/".$file)
					&& !CComponentUtil::isComponent("/bitrix/components/".$file))
					$arNamespaces[] = $file;
			}
			@closedir($handle);
		}

		return $arNamespaces;
	}

	function GetComponentDescr($componentName)
	{
		$componentName = Trim($componentName);

		static $cache = array();

		if(strLen($componentName) <= 0)
		{
			$arComponentDescription = false;
		}
		else
		{
			if(array_key_exists($componentName, $cache))
				return $cache[$componentName];

			$path2Comp = CComponentEngine::MakeComponentPath($componentName);
			if(strLen($path2Comp) <= 0)
			{
				$arComponentDescription = false;
			}
			else
			{
				$componentPath = "/bitrix/components".$path2Comp;
				if(CComponentUtil::isComponent($componentPath))
				{
					$arComponentDescription = array();
					if(file_exists($_SERVER["DOCUMENT_ROOT"].$componentPath."/.description.php"))
					{
						CComponentUtil::__IncludeLang($componentPath, ".description.php");
						include($_SERVER["DOCUMENT_ROOT"].$componentPath."/.description.php");
					}
				}
				else
				{
					$arComponentDescription = false;
				}
			}
		}

		$cache[$componentName] = $arComponentDescription;
		return $arComponentDescription;
	}

	function __GroupParamsCompare($a, $b)
	{
		if ($a["SORT"] < $b["SORT"])
			return -1;
		elseif ($a["SORT"] > $b["SORT"])
			return 1;
		else
			return 0;
	}

	function GetComponentProps($componentName, $arCurrentValues = array())
	{
		$arComponentParameters = array();

		$componentName = Trim($componentName);
		if (StrLen($componentName) <= 0)
			return False;

		$path2Comp = CComponentEngine::MakeComponentPath($componentName);
		if (StrLen($path2Comp) <= 0)
			return False;

		$componentPath = "/bitrix/components".$path2Comp;
		if(!CComponentUtil::isComponent($componentPath))
		{
			return False;
		}

		if (file_exists($_SERVER["DOCUMENT_ROOT"].$componentPath."/.parameters.php"))
		{
			CComponentUtil::__IncludeLang($componentPath, ".parameters.php");

			$arComponentParameters = array();
			include($_SERVER["DOCUMENT_ROOT"].$componentPath."/.parameters.php");

			if (!array_key_exists("PARAMETERS", $arComponentParameters) || !is_array($arComponentParameters["PARAMETERS"]))
				return False;

			if (!array_key_exists("GROUPS", $arComponentParameters) || !is_array($arComponentParameters["GROUPS"]))
				$arComponentParameters["GROUPS"] = array();

			$arParamKeys = array_keys($arComponentParameters["GROUPS"]);
			for ($i = 0, $cnt = count($arParamKeys); $i < $cnt; $i++)
			{
				if (!IsSet($arComponentParameters["GROUPS"][$arParamKeys[$i]]["SORT"]))
					$arComponentParameters["GROUPS"][$arParamKeys[$i]]["SORT"] = 1000+$i;
				$arComponentParameters["GROUPS"][$arParamKeys[$i]]["SORT"] = IntVal($arComponentParameters["GROUPS"][$arParamKeys[$i]]["SORT"]);
				if ($arComponentParameters["GROUPS"][$arParamKeys[$i]]["SORT"] <= 0)
					$arComponentParameters["GROUPS"][$arParamKeys[$i]]["SORT"] = 1000+$i;
			}

			$arParamKeys = array_keys($arComponentParameters["PARAMETERS"]);
			for ($i = 0, $cnt = count($arParamKeys); $i < $cnt; $i++)
			{
				if ($arParamKeys[$i] == "SET_TITLE")
				{
					$arComponentParameters["GROUPS"]["ADDITIONAL_SETTINGS"] = array(
						"NAME" => GetMessage("COMP_GROUP_ADDITIONAL_SETTINGS"),
						"SORT" => 700
					);

					$arComponentParameters["PARAMETERS"]["SET_TITLE"] = array(
						"PARENT" => "ADDITIONAL_SETTINGS",
						"NAME" => GetMessage("COMP_PROP_SET_TITLE"),
						"TYPE" => "CHECKBOX",
						"DEFAULT" => "Y",
						"ADDITIONAL_VALUES" => "N"
					);
				}
				elseif ($arParamKeys[$i] == "CACHE_TIME")
				{
					$arComponentParameters["GROUPS"]["CACHE_SETTINGS"] = array(
						"NAME" => GetMessage("COMP_GROUP_CACHE_SETTINGS"),
						"SORT" => 600
					);

					$arSavedParams = $arComponentParameters["PARAMETERS"];
					$arComponentParameters["PARAMETERS"] = array();
					foreach ($arSavedParams as $keyTmp => $valueTmp)
					{
						if ($keyTmp == "CACHE_TIME")
						{
							$arComponentParameters["PARAMETERS"]["CACHE_TYPE"] = array(
								"PARENT" => "CACHE_SETTINGS",
								"NAME" => GetMessage("COMP_PROP_CACHE_TYPE"),
								"TYPE" => "LIST",
								"VALUES" => array("A" => GetMessage("COMP_PROP_CACHE_TYPE_AUTO")." ".GetMessage("COMP_PARAM_CACHE_MAN"), "Y" => GetMessage("COMP_PROP_CACHE_TYPE_YES"), "N" => GetMessage("COMP_PROP_CACHE_TYPE_NO")),
								"DEFAULT" => "A",
								"ADDITIONAL_VALUES" => "N"
							);
							$arComponentParameters["PARAMETERS"]["CACHE_TIME"] = array(
								"PARENT" => "CACHE_SETTINGS",
								"NAME" => GetMessage("COMP_PROP_CACHE_TIME"),
								"TYPE" => "STRING",
								"MULTIPLE" => "N",
								"DEFAULT" => IntVal($arSavedParams["CACHE_TIME"]["DEFAULT"]),
								"COLS" => 5
							);
							$arComponentParameters["PARAMETERS"]["CACHE_NOTES"] = array(
								"PARENT" => "CACHE_SETTINGS",
								"TYPE" => "CUSTOM",
								"JS_FILE" => "/bitrix/js/main/comp_props.js",
								"JS_EVENT" => "BxShowComponentNotes",
								"JS_DATA" => GetMessage("COMP_PROP_CACHE_NOTE", array(
									"#LANG#" => LANGUAGE_ID,
									"#AUTO_MODE#" => (COption::GetOptionString("main", "component_cache_on", "Y") == "Y"? GetMessage("COMP_PARAM_CACHE_AUTO_ON"):GetMessage("COMP_PARAM_CACHE_AUTO_OFF")),
									"#MANAGED_MODE#" =>(defined("BX_COMP_MANAGED_CACHE")? GetMessage("COMP_PARAM_CACHE_MANAGED_ON"):GetMessage("COMP_PARAM_CACHE_MANAGED_OFF")),
								)),
							);
						}
						else
						{
							$arComponentParameters["PARAMETERS"][$keyTmp] = $valueTmp;
						}
					}
				}
				elseif ($arParamKeys[$i] == "SEF_MODE")
				{
					$arComponentParameters["GROUPS"]["SEF_MODE"] = array(
						"NAME" => GetMessage("COMP_GROUP_SEF_MODE"),
						"SORT" => 500
					);

					$arSEFModeSettings = $arComponentParameters["PARAMETERS"]["SEF_MODE"];

					$arComponentParameters["PARAMETERS"]["SEF_MODE"] = array(
						"PARENT" => "SEF_MODE",
						"NAME" => GetMessage("COMP_PROP_SEF_MODE"),
						"TYPE" => "CHECKBOX",
						/*"VALUES" => array("N" => GetMessage("COMP_PROP_SEF_MODE_NO"), "Y" => GetMessage("COMP_PROP_SEF_MODE_YES")),*/
						"DEFAULT" => "N",
						"ADDITIONAL_VALUES" => "N"
					);
					$arComponentParameters["PARAMETERS"]["SEF_FOLDER"] = array(
						"PARENT" => "SEF_MODE",
						"NAME" => GetMessage("COMP_PROP_SEF_FOLDER"),
						"TYPE" => "STRING",
						"MULTIPLE" => "N",
						"DEFAULT" => "",
						"COLS" => 30
					);

					if (is_array($arSEFModeSettings) && count($arSEFModeSettings) > 0)
					{
						foreach ($arSEFModeSettings as $templateKey => $arTemplateValue)
						{
							$arComponentParameters["PARAMETERS"]["SEF_URL_TEMPLATES_".$templateKey] = array(
								"PARENT" => "SEF_MODE",
								"NAME" => $arTemplateValue["NAME"],
								"TYPE" => "STRING",
								"MULTIPLE" => "N",
								"DEFAULT" => $arTemplateValue["DEFAULT"],
								"HIDDEN" => $arTemplateValue["HIDDEN"],
								"COLS" => 50,
								"VARIABLES" => array(),
							);
							if (is_array($arVariableAliasesSettings) && count($arVariableAliasesSettings) > 0)
							{
								foreach ($arTemplateValue["VARIABLES"] as $variable)
									$arComponentParameters["PARAMETERS"]["SEF_URL_TEMPLATES_".$templateKey]["VARIABLES"]["#".$variable."#"] = $arVariableAliasesSettings[$variable]["NAME"];
							}
						}
					}
				}
				elseif ($arParamKeys[$i] == "VARIABLE_ALIASES")
				{
					$arComponentParameters["GROUPS"]["SEF_MODE"] = array(
						"NAME" => GetMessage("COMP_GROUP_SEF_MODE"),
						"SORT" => 500
					);

					$arVariableAliasesSettings = $arComponentParameters["PARAMETERS"]["VARIABLE_ALIASES"];

					unset($arComponentParameters["PARAMETERS"]["VARIABLE_ALIASES"]);

					foreach ($arVariableAliasesSettings as $aliaseKey => $arAliaseValue)
					{
						$arComponentParameters["PARAMETERS"]["VARIABLE_ALIASES_".$aliaseKey] = array(
							"PARENT" => "SEF_MODE",
							"NAME" => $arAliaseValue["NAME"],
							"TYPE" => "STRING",
							"MULTIPLE" => "N",
							"DEFAULT" => $aliaseKey,
							"COLS" => 20,
						);
					}
				}
				elseif (IsSet($arComponentParameters["PARAMETERS"][$arParamKeys[$i]]["PARENT"]) && StrLen($arComponentParameters["PARAMETERS"][$arParamKeys[$i]]["PARENT"]) > 0)
				{
					if ($arComponentParameters["PARAMETERS"][$arParamKeys[$i]]["PARENT"] == "URL_TEMPLATES")
					{
						$arComponentParameters["GROUPS"]["URL_TEMPLATES"] = array(
							"NAME" => GetMessage("COMP_GROUP_URL_TEMPLATES"),
							"SORT" => 400
						);
					}
					elseif ($arComponentParameters["PARAMETERS"][$arParamKeys[$i]]["PARENT"] == "VISUAL")
					{
						$arComponentParameters["GROUPS"]["VISUAL"] = array(
							"NAME" => GetMessage("COMP_GROUP_VISUAL"),
							"SORT" => 300
						);
					}
					elseif ($arComponentParameters["PARAMETERS"][$arParamKeys[$i]]["PARENT"] == "DATA_SOURCE")
					{
						$arComponentParameters["GROUPS"]["DATA_SOURCE"] = array(
							"NAME" => GetMessage("COMP_GROUP_DATA_SOURCE"),
							"SORT" => 200
						);
					}
					elseif ($arComponentParameters["PARAMETERS"][$arParamKeys[$i]]["PARENT"] == "BASE")
					{
						$arComponentParameters["GROUPS"]["BASE"] = array(
							"NAME" => GetMessage("COMP_GROUP_BASE"),
							"SORT" => 100
						);
					}
					elseif ($arComponentParameters["PARAMETERS"][$arParamKeys[$i]]["PARENT"] == "ADDITIONAL_SETTINGS")
					{
						$arComponentParameters["GROUPS"]["ADDITIONAL_SETTINGS"] = array(
							"NAME" => GetMessage("COMP_GROUP_ADDITIONAL_SETTINGS"),
							"SORT" => 700
						);
					}
				}
				elseif ($arParamKeys[$i] == "AJAX_MODE")
				{
					$arComponentParameters["GROUPS"]["AJAX_SETTINGS"] = array(
						"NAME" => GetMessage("COMP_GROUP_AJAX_SETTINGS"),
						"SORT" => 550
					);

					$arComponentParameters["PARAMETERS"]["AJAX_MODE"] = array(
						"PARENT" => "AJAX_SETTINGS",
						"NAME" => GetMessage("COMP_PROP_AJAX_MODE"),
						"TYPE" => "CHECKBOX",
						"DEFAULT" => "N",
						"ADDITIONAL_VALUES" => "N"
					);

					// $arComponentParameters["PARAMETERS"]["AJAX_OPTION_SHADOW"] = array(
						// "PARENT" => "AJAX_SETTINGS",
						// "NAME" => GetMessage("COMP_PROP_AJAX_OPTIONS_SHADOW"),
						// "TYPE" => "CHECKBOX",
						// "MULTIPLE" => "N",
						// "DEFAULT" => "Y",
						// "ADDITIONAL_VALUES" => "N"
					// );

					$arComponentParameters["PARAMETERS"]["AJAX_OPTION_JUMP"] = array(
						"PARENT" => "AJAX_SETTINGS",
						"NAME" => GetMessage("COMP_PROP_AJAX_OPTIONS_JUMP"),
						"TYPE" => "CHECKBOX",
						"MULTIPLE" => "N",
						"DEFAULT" => "N",
						"ADDITIONAL_VALUES" => "N"
					);

					$arComponentParameters["PARAMETERS"]["AJAX_OPTION_STYLE"] = array(
						"PARENT" => "AJAX_SETTINGS",
						"NAME" => GetMessage("COMP_PROP_AJAX_OPTIONS_STYLE"),
						"TYPE" => "CHECKBOX",
						"MULTIPLE" => "N",
						"DEFAULT" => "Y",
						"ADDITIONAL_VALUES" => "N"
					);

					$arComponentParameters["PARAMETERS"]["AJAX_OPTION_HISTORY"] = array(
						"PARENT" => "AJAX_SETTINGS",
						"NAME" => GetMessage("COMP_PROP_AJAX_OPTIONS_HISTORY"),
						"TYPE" => "CHECKBOX",
						"MULTIPLE" => "N",
						"DEFAULT" => "N",
						"ADDITIONAL_VALUES" => "N"
					);

					$arComponentParameters["PARAMETERS"]["AJAX_OPTION_ADDITIONAL"] = array(
						"PARENT" => "AJAX_SETTINGS",
						"NAME" => GetMessage("COMP_PROP_AJAX_OPTIONS_ADDITIONAL"),
						"TYPE" => "STRING",
						"HIDDEN" => "Y",
						"MULTIPLE" => "N",
						"DEFAULT" => "",
						"ADDITIONAL_VALUES" => "N"
					);
				}
			}

			if(
				(CPageOption::GetOptionString("main","tips_creation","no")=="allowed")
				&& (strpos($componentPath, "/forum")!==false)
			)
			{
				//Create directories
				$help_lang_path = $_SERVER["DOCUMENT_ROOT"].$componentPath."/lang";
				if(!file_exists($help_lang_path))
					mkdir($help_lang_path);
				$help_lang_path .= "/ru";
				if(!file_exists($help_lang_path))
					mkdir($help_lang_path);
				$help_lang_path .= "/help";
				if(!file_exists($help_lang_path))
					mkdir($help_lang_path);
				if(is_dir($help_lang_path))
				{
					//Create files if none exists
					$lang_filename = $help_lang_path."/.tooltips.php";
					if(!file_exists($lang_filename))
					{
						$handle=fopen($lang_filename, "w");
						fwrite($handle, "<?\n?>");
						fclose($handle);
					}
					$handle=fopen($lang_filename, "r");
					$lang_contents = fread($handle, filesize($lang_filename));
					fclose($handle);
					$lang_file_modified = false;
					//Bug fix
					if(strpos($lang_contents, "\$MESS['")!==false)
					{
						$lang_contents = str_replace("\$MESS['", "\$MESS ['", $lang_contents);
						$lang_file_modified = true;
					}
					//Check out parameters
					foreach($arComponentParameters["PARAMETERS"] as $strName=>$arParameter)
					{
						if(strpos($lang_contents, "\$MESS ['${strName}_TIP'] = ")===false)
						{
							$lang_contents = str_replace("?>", "\$MESS ['${strName}_TIP'] = \"".str_replace("\$", "\\\$", str_replace('"','\\"',$arParameter["NAME"]))."\";\n?>", $lang_contents);
							$lang_file_modified = true;
						}
					}
					//Save the result of the work
					if($lang_file_modified)
					{
						$handle=fopen($lang_filename, "w");
						fwrite($handle, $lang_contents);
						fclose($handle);
					}
				}
				reset($arComponentParameters["PARAMETERS"]);
			}
			uasort($arComponentParameters["GROUPS"], array("CComponentUtil", "__GroupParamsCompare"));
		}

		return $arComponentParameters;
	}

	function GetTemplateProps($componentName, $templateName, $siteTemplate = "", $arCurrentValues = array())
	{
		$arTemplateParameters = array();

		$componentName = Trim($componentName);
		if (StrLen($componentName) <= 0)
			return $arTemplateParameters;

		if (StrLen($templateName) <= 0)
			$templateName = ".default";

		if(!preg_match("#[A-Za-z0-9_.-]#i", $templateName))
			return $arTemplateParameters;

		$path2Comp = CComponentEngine::MakeComponentPath($componentName);
		if (StrLen($path2Comp) <= 0)
			return $arTemplateParameters;

		$componentPath = "/bitrix/components".$path2Comp;

		if (!CComponentUtil::isComponent($componentPath))
		{
			return $arTemplateParameters;
		}

		if ($siteTemplate && StrLen($siteTemplate) > 0)
		{
			$siteTemplate = _normalizePath($siteTemplate);
			if (file_exists($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".$siteTemplate."/components".$path2Comp."/".$templateName))
			{
				if (is_dir($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".$siteTemplate."/components".$path2Comp."/".$templateName)
					&& file_exists($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".$siteTemplate."/components".$path2Comp."/".$templateName."/.parameters.php"))
				{
					CComponentUtil::__IncludeLang(BX_PERSONAL_ROOT."/templates/".$siteTemplate."/components".$path2Comp."/".$templateName, ".parameters.php");
					include($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".$siteTemplate."/components".$path2Comp."/".$templateName."/.parameters.php");
				}
				return $arTemplateParameters;
			}
		}

		if (file_exists($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/.default/components".$path2Comp."/".$templateName))
		{
			if (is_dir($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/.default/components".$path2Comp."/".$templateName)
				&& file_exists($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/.default/components".$path2Comp."/".$templateName."/.parameters.php"))
			{
				CComponentUtil::__IncludeLang(BX_PERSONAL_ROOT."/templates/.default/components".$path2Comp."/".$templateName, ".parameters.php");
				include($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/.default/components".$path2Comp."/".$templateName."/.parameters.php");
			}
			return $arTemplateParameters;
		}

		if (file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/components".$path2Comp."/templates/".$templateName))
		{
			if (is_dir($_SERVER["DOCUMENT_ROOT"]."/bitrix/components".$path2Comp."/templates/".$templateName)
				&& file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/components".$path2Comp."/templates/".$templateName."/.parameters.php"))
			{
				CComponentUtil::__IncludeLang("/bitrix/components".$path2Comp."/templates/".$templateName, ".parameters.php");
				include($_SERVER["DOCUMENT_ROOT"]."/bitrix/components".$path2Comp."/templates/".$templateName."/.parameters.php");
			}
			return $arTemplateParameters;
		}

		return $arTemplateParameters;
	}

	function GetTemplatesList($componentName, $currentTemplate = False)
	{
		$arTemplatesList = array();

		$componentName = Trim($componentName);
		if (StrLen($componentName) <= 0)
			return $arTemplatesList;

		$path2Comp = CComponentEngine::MakeComponentPath($componentName);
		if (StrLen($path2Comp) <= 0)
			return $arTemplatesList;

		$componentPath = "/bitrix/components".$path2Comp;

		if (!CComponentUtil::isComponent($componentPath))
		{
			return $arTemplatesList;
		}

		$arExists = array();

		if ($handle = @opendir($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates"))
		{
			while (($file = readdir($handle)) !== false)
			{
				if ($file == "." || $file == "..")
					continue;

				if ($currentTemplate !== False && $currentTemplate != $file || $file == ".default")
					continue;

				if (is_dir($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".$file))
				{
					if (file_exists($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".$file."/components"))
					{
						if (file_exists($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".$file."/components".$path2Comp))
						{
							if ($handle1 = @opendir($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".$file."/components".$path2Comp))
							{
								while (($file1 = readdir($handle1)) !== false)
								{
									if ($file1 == "." || $file1 == "..")
										continue;

									$arTemplate = array(
										"NAME" => $file1,
										"TEMPLATE" => $file
									);

									if (is_dir($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".$file."/components".$path2Comp."/".$file1))
									{
										if (file_exists($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".$file."/components".$path2Comp."/".$file1."/.description.php"))
										{
											CComponentUtil::__IncludeLang(BX_PERSONAL_ROOT."/templates/".$file."/components".$path2Comp."/".$file1, ".description.php");

											$arTemplateDescription = array();
											include($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".$file."/components".$path2Comp."/".$file1."/.description.php");

											$arTemplate["TITLE"] = $arTemplateDescription["NAME"];
											$arTemplate["DESCRIPTION"] = $arTemplateDescription["DESCRIPTION"];
										}
									}

									$arTemplatesList[] = $arTemplate;
									$arExists[] = $arTemplate["NAME"];
								}
								@closedir($handle1);
							}
						}
					}
				}
			}
			@closedir($handle);
		}

		if (is_dir($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/.default"))
		{
			if (file_exists($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/.default/components"))
			{
				if (file_exists($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/.default/components".$path2Comp))
				{
					if ($handle1 = @opendir($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/.default/components".$path2Comp))
					{
						while (($file1 = readdir($handle1)) !== false)
						{
							if ($file1 == "." || $file1 == "..")
								continue;

							if (in_array($file1, $arExists))
								continue;

							$arTemplate = array(
								"NAME" => $file1,
								"TEMPLATE" => ".default"
							);

							if (is_dir($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/.default/components".$path2Comp."/".$file1))
							{
								if (file_exists($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/.default/components".$path2Comp."/".$file1."/.description.php"))
								{
									CComponentUtil::__IncludeLang(BX_PERSONAL_ROOT."/templates/.default/components".$path2Comp."/".$file1, ".description.php");

									$arTemplateDescription = array();
									include($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/.default/components".$path2Comp."/".$file1."/.description.php");

									$arTemplate["TITLE"] = $arTemplateDescription["NAME"];
									$arTemplate["DESCRIPTION"] = $arTemplateDescription["DESCRIPTION"];
								}
							}

							$arTemplatesList[] = $arTemplate;
							$arExists[] = $arTemplate["NAME"];
						}
						@closedir($handle1);
					}
				}
			}
		}

		if ($handle1 = @opendir($_SERVER["DOCUMENT_ROOT"].$componentPath."/templates"))
		{
			while (($file1 = readdir($handle1)) !== false)
			{
				if ($file1 == "." || $file1 == "..")
					continue;

				if (in_array($file1, $arExists))
					continue;

				$arTemplate = array(
					"NAME" => $file1,
					"TEMPLATE" => ""
				);

				if (is_dir($_SERVER["DOCUMENT_ROOT"].$componentPath."/templates/".$file1))
				{
					if (file_exists($_SERVER["DOCUMENT_ROOT"].$componentPath."/templates/".$file1."/.description.php"))
					{
						CComponentUtil::__IncludeLang($componentPath."/templates/".$file1, ".description.php");

						$arTemplateDescription = array();
						include($_SERVER["DOCUMENT_ROOT"].$componentPath."/templates/".$file1."/.description.php");

						$arTemplate["TITLE"] = $arTemplateDescription["NAME"];
						$arTemplate["DESCRIPTION"] = $arTemplateDescription["DESCRIPTION"];
					}
				}

				$arTemplatesList[] = $arTemplate;
				$arExists[] = $arTemplate["NAME"];
			}
			@closedir($handle1);
		}

		return $arTemplatesList;
	}

	function CopyComponent($componentName, $newNamespace, $newName = False, $bRewrite = False)
	{
		$componentName = Trim($componentName);
		if (StrLen($componentName) <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("comp_util_err1"), "EMPTY_COMPONENT_NAME");
			return false;
		}

		$path2Comp = CComponentEngine::MakeComponentPath($componentName);
		if (StrLen($path2Comp) <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(str_replace("#NAME#", $componentName, GetMessage("comp_util_err2")), "ERROR_NOT_COMPONENT");
			return false;
		}

		$componentPath = "/bitrix/components".$path2Comp;

		if (!CComponentUtil::isComponent($componentPath))
		{
			$GLOBALS["APPLICATION"]->ThrowException(str_replace("#NAME#", $componentName, GetMessage("comp_util_err2")), "ERROR_NOT_COMPONENT");
			return false;
		}

		$newNamespace = Trim($newNamespace);
		if (StrLen($newNamespace) > 0)
		{
			$newNamespaceTmp = preg_replace("#[^A-Za-z0-9_.-]#i", "", $newNamespace);
			if ($newNamespace != $newNamespaceTmp)
			{
				$GLOBALS["APPLICATION"]->ThrowException(str_replace("#NAME#", $newNamespace, GetMessage("comp_util_err3")), "ERROR_NEW_NAMESPACE");
				return false;
			}
		}

		if (StrLen($newName) <= 0)
			$newName = False;

		if ($newName !== False)
		{
			if (!preg_match("#^([A-Za-z0-9_-]+\\.)*([A-Za-z0-9_-]+)$#i", $newName))
			{
				$GLOBALS["APPLICATION"]->ThrowException(str_replace("#NAME#", $newName, GetMessage("comp_util_err4")), "ERROR_NEW_NAME");
				return false;
			}
		}

		$namespace = "";
		$name = $componentName;
		if (($pos = StrPos($componentName, ":")) !== False)
		{
			$namespace = SubStr($componentName, 0, $pos);
			$name = SubStr($componentName, $pos + 1);
		}

		if ($namespace == $newNamespace
			&& ($newName === False || $newName !== False && $name == $newName))
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("comp_util_err5"), "ERROR_DUPL1");
			return false;
		}

		if ($newName !== False)
			$componentNameNew = $newNamespace.":".$newName;
		else
			$componentNameNew = $newNamespace.":".$name;

		$path2CompNew = CComponentEngine::MakeComponentPath($componentNameNew);
		if (StrLen($path2CompNew) <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(str_replace("#NAME#", $componentNameNew, GetMessage("comp_util_err2")), "ERROR_NOT_COMPONENT");
			return false;
		}

		$componentPathNew = "/bitrix/components".$path2CompNew;

		if (file_exists($_SERVER["DOCUMENT_ROOT"].$componentPathNew))
		{
			if (!$bRewrite)
			{
				$GLOBALS["APPLICATION"]->ThrowException(str_replace("#NAME#", $componentNameNew, GetMessage("comp_util_err6")), "ERROR_EXISTS");
				return false;
			}
			else
			{
				DeleteDirFilesEx($componentPathNew);
			}
		}

		CheckDirPath($_SERVER["DOCUMENT_ROOT"].$componentPathNew);

		CopyDirFiles($_SERVER["DOCUMENT_ROOT"].$componentPath, $_SERVER["DOCUMENT_ROOT"].$componentPathNew, True, True, False);
	}

	function CopyTemplate($componentName, $templateName, $siteTemplate, $newSiteTemplate, $newName = False, $bRewrite = False)
	{
		$componentName = Trim($componentName);
		if (StrLen($componentName) <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("comp_util_err1"), "EMPTY_COMPONENT_NAME");
			return false;
		}

		$path2Comp = CComponentEngine::MakeComponentPath($componentName);
		if (StrLen($path2Comp) <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(str_replace("#NAME#", $componentName, GetMessage("comp_util_err2")), "ERROR_NOT_COMPONENT");
			return false;
		}

		$componentPath = "/bitrix/components".$path2Comp;

		if (!CComponentUtil::isComponent($componentPath))
		{
			$GLOBALS["APPLICATION"]->ThrowException(str_replace("#NAME#", $componentName, GetMessage("comp_util_err2")), "ERROR_NOT_COMPONENT");
			return false;
		}

		if (StrLen($templateName) <= 0)
			$templateName = ".default";

		$templateNameTmp = preg_replace("#[^A-Za-z0-9_.-]#i", "", $templateName);
		if ($templateNameTmp != $templateName)
		{
			$GLOBALS["APPLICATION"]->ThrowException(str_replace("#NAME#", $templateName, GetMessage("comp_util_err7")), "ERROR_BAD_TEMPLATE_NAME");
			return false;
		}

		if (StrLen($siteTemplate) <= 0)
			$siteTemplate = False;

		if ($siteTemplate != False)
		{
			if (!file_exists($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".$siteTemplate)
				|| !is_dir($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".$siteTemplate))
			{
				$GLOBALS["APPLICATION"]->ThrowException(str_replace("#NAME#", $siteTemplate, GetMessage("comp_util_err8")), "ERROR_NO_SITE_TEMPL");
				return false;
			}
		}

		if ($siteTemplate != False)
			$path = BX_PERSONAL_ROOT."/templates/".$siteTemplate."/components".$path2Comp."/".$templateName;
		else
			$path = "/bitrix/components".$path2Comp."/templates/".$templateName;

		if (!file_exists($_SERVER["DOCUMENT_ROOT"].$path))
		{
			$GLOBALS["APPLICATION"]->ThrowException(str_replace("#C_NAME#", $componentName, str_replace("#T_NAME#", $templateName, GetMessage("comp_util_err9"))), "ERROR_NO_TEMPL");
			return false;
		}

		if (StrLen($newSiteTemplate) <= 0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("comp_util_err10"), "ERROR_EMPTY_SITE_TEMPL");
			return false;
		}

		if (!file_exists($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".$newSiteTemplate)
			|| !is_dir($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".$newSiteTemplate))
		{
			$GLOBALS["APPLICATION"]->ThrowException(str_replace("#NAME#", $newSiteTemplate, GetMessage("comp_util_err8")), "ERROR_NO_SITE_TEMPL");
			return false;
		}

		if ($siteTemplate !== False
			&& $siteTemplate == $newSiteTemplate
			&& ($newName === False || $newName !== False && $templateName == $newName))
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("comp_util_err11"), "ERROR_DUPL1");
			return false;
		}

		if ($newName !== False)
			$templateNameNew = $newName;
		else
			$templateNameNew = $templateName;

		$templateNameNewTmp = preg_replace("#[^A-Za-z0-9_.-]#i", "", $templateNameNew);
		if ($templateNameNewTmp != $templateNameNew)
		{
			$GLOBALS["APPLICATION"]->ThrowException(str_replace("#NAME#", $templateNameNew, GetMessage("comp_util_err7")), "ERROR_BAD_TEMPLATE_NAME");
			return false;
		}

		$pathNew = BX_PERSONAL_ROOT."/templates/".$newSiteTemplate."/components".$path2Comp."/".$templateNameNew;

		if (file_exists($_SERVER["DOCUMENT_ROOT"].$pathNew))
		{
			if (!$bRewrite)
			{
				$GLOBALS["APPLICATION"]->ThrowException(str_replace("#NAME#", $templateNameNew, GetMessage("comp_util_err12")), "ERROR_EXISTS");
				return false;
			}
			else
			{
				DeleteDirFilesEx($pathNew);
			}
		}

		CopyDirFiles($_SERVER["DOCUMENT_ROOT"].$path, $_SERVER["DOCUMENT_ROOT"].$pathNew, True, True, False);

		return True;
	}

	function CheckComponentName($name, $arFilter)
	{
		foreach ($arFilter as $pattern)
			if (preg_match($pattern, $name))
				return true;
		return false;
	}

	function GetDefaultNameTemplates()
	{
		return array(
			'#LAST_NAME# #NAME#' => GetMessage('COMP_NAME_TEMPLATE_SMITH_JOHN'),
			'#LAST_NAME# #NAME# #SECOND_NAME#' => GetMessage('COMP_NAME_TEMPLATE_SMITH_JOHN_LLOYD'),
			'#LAST_NAME#, #NAME# #SECOND_NAME#' => GetMessage('COMP_NAME_TEMPLATE_SMITH_COMMA_JOHN_LLOYD'),
			'#NAME# #SECOND_NAME# #LAST_NAME#' => GetMessage('COMP_NAME_TEMPLATE_JOHN_LLOYD_SMITH'),
			'#NAME_SHORT# #SECOND_NAME_SHORT# #LAST_NAME#' => GetMessage('COMP_NAME_TEMPLATE_J_L_SMITH'),
			'#NAME_SHORT# #LAST_NAME#' => GetMessage('COMP_NAME_TEMPLATE_J_SMITH'),
			'#LAST_NAME# #NAME_SHORT#' => GetMessage('COMP_NAME_TEMPLATE_SMITH_J'),
			'#LAST_NAME# #NAME_SHORT# #SECOND_NAME_SHORT#' => GetMessage('COMP_NAME_TEMPLATE_SMITH_J_L'),
			'#LAST_NAME#, #NAME_SHORT#' => GetMessage('COMP_NAME_TEMPLATE_SMITH_COMMA_J'),
			'#LAST_NAME#, #NAME_SHORT# #SECOND_NAME_SHORT#' => GetMessage('COMP_NAME_TEMPLATE_SMITH_COMMA_J_L'),
			'#NAME# #LAST_NAME#' => GetMessage('COMP_NAME_TEMPLATE_JOHN_SMITH'),
			'#NAME# #SECOND_NAME_SHORT# #LAST_NAME#' => GetMessage('COMP_NAME_TEMPLATE_JOHN_L_SMITH'),
			'' => GetMessage('COMP_PARAM_NAME_FORMAT_SITE')
		);
	}

	function GetDateFormatField($name="", $parent="", $no_year = false)
	{
		$timestamp = mktime(0,0,0,2,6,2010);
		return array(
			"PARENT" => $parent,
			"NAME" => $name,
			"TYPE" => "LIST",
			"VALUES" => $no_year ?
				array(
					"d-m" => FormatDate("d-m", $timestamp),//"22-02",
					"m-d" => FormatDate("m-d", $timestamp),//"02-22",
					"m-d" => FormatDate("m-d", $timestamp),//"02-22",
					"d.m" => FormatDate("d.m", $timestamp),//"22.02",
					"d.M" => FormatDate("d.M", $timestamp),//"22.���",
					"m.d" => FormatDate("m.d", $timestamp),//"02.22",
					"j M" => FormatDate("j M", $timestamp),//"22 Feb",
					"M j" => FormatDate("M j", $timestamp),//"Feb 22",
					"j F" => FormatDate("j F", $timestamp),//"22 February",
					"f j" => FormatDate("f j", $timestamp),//"February 22"
					CComponentUtil::GetDateFormatDefault($no_year) => GetMessage('COMP_PARAM_DATE_FORMAT_SITE')
				):
				array(
					"d-m-Y" => FormatDate("d-m-Y", $timestamp),//"22-02-2007",
					"m-d-Y" => FormatDate("m-d-Y", $timestamp),//"02-22-2007",
					"Y-m-d" => FormatDate("Y-m-d", $timestamp),//"2007-02-22",
					"d.m.Y" => FormatDate("d.m.Y", $timestamp),//"22.02.2007",
					"d.M.Y" => FormatDate("d.M.Y", $timestamp),//"22.���.2007",
					"m.d.Y" => FormatDate("m.d.Y", $timestamp),//"02.22.2007",
					"j M Y" => FormatDate("j M Y", $timestamp),//"22 Feb 2007",
					"M j, Y" => FormatDate("M j, Y", $timestamp),//"Feb 22, 2007",
					"j F Y" => FormatDate("j F Y", $timestamp),//"22 February 2007",
					"f j, Y" => FormatDate("f j, Y", $timestamp),//"February 22",
					"SHORT" => GetMessage('COMP_PARAM_DATE_FORMAT_SITE')
				),
			"DEFAULT" => CComponentUtil::GetDateFormatDefault($no_year),
			"ADDITIONAL_VALUES" => "Y",
		);
	}

	function GetDateFormatDefault($no_year = false)
	{
		return $GLOBALS["DB"]->DateFormatToPHP($no_year ? preg_replace('/[\-\.\/]*[Y]{2,4}[\-\.\/]*/', '', CSite::GetDateFormat('SHORT')) : CSite::GetDateFormat("SHORT"));
	}

	function GetDateTimeFormatField($name="", $parent="")
	{
		$timestamp = mktime(16,10,45,2,6,2010);
		return array(
			"PARENT" => $parent,
			"NAME" => $name,
			"TYPE" => "LIST",
			"VALUES" => array(
				"d-m-Y H:i:s" => FormatDate("d-m-Y H:i:s", $timestamp),//"22-02-2007 7:30",
				"m-d-Y H:i:s" => FormatDate("m-d-Y H:i:s", $timestamp),//"02-22-2007 7:30",
				"Y-m-d H:i:s" => FormatDate("Y-m-d H:i:s", $timestamp),//"2007-02-22 7:30",
				"d.m.Y H:i:s" => FormatDate("d.m.Y H:i:s", $timestamp),//"22.02.2007 7:30",
				"m.d.Y H:i:s" => FormatDate("m.d.Y H:i:s", $timestamp),//"02.22.2007 7:30",
				"j M Y H:i:s" => FormatDate("j M Y H:i:s", $timestamp),//"22 Feb 2007 7:30",
				"M j, Y H:i:s" => FormatDate("M j, Y H:i:s", $timestamp),//"Feb 22, 2007 7:30",
				"j F Y H:i:s" => FormatDate("j F Y H:i:s", $timestamp),//"22 February 2007 7:30",
				"f j, Y H:i:s" => FormatDate("f j, Y H:i:s", $timestamp),//"February 22, 2007",
				"d.m.y g:i:s A" => FormatDate("d.m.y g:i:s A", $timestamp),//"22.02.07 1:30 PM",
				"d.M.y g:i:s a" => FormatDate("d.M.y g:i:s a", $timestamp),//"22.���.07 1:30 pm",
				"d.M.Y g:i:s a" => FormatDate("d.M.Y g:i:s a", $timestamp),//"22.���.2007 1:30 pm",
				"d.m.y G:i" => FormatDate("d.m.y G:i", $timestamp),//"22.02.07 7:30",
				"d.m.Y H:i:s" => FormatDate("d.m.Y H:i:s", $timestamp),//"22.02.2007 07:30",
				"j F Y G:i" => FormatDate("j F Y G:i", $timestamp),//"ZHL cool RUS",
				"j F Y g:i a" => FormatDate("j F Y g:i a", $timestamp),//"ZHL cool Burzh",
				"FULL" => GetMessage('COMP_PARAM_DATETIME_FORMAT_SITE')
			),
			"DEFAULT" => CComponentUtil::GetDateTimeFormatDefault(),
			"ADDITIONAL_VALUES" => "Y",
		);
	}

	function GetDateTimeFormatDefault()
	{
		return $GLOBALS["DB"]->DateFormatToPHP(CSite::GetDateFormat("FULL"));
	}

}
?>