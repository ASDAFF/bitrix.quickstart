<?
class CComponentEngine
{
	function CheckComponentName($componentName)
	{
		return ($componentName <> '' && preg_match("#^([A-Za-z0-9_.-]+:)?([A-Za-z0-9_-]+\\.)*([A-Za-z0-9_-]+)$#i", $componentName));
	}

	function MakeComponentPath($componentName)
	{
		if(!CComponentEngine::CheckComponentName($componentName))
			return "";

		return "/".str_replace(":", "/", $componentName);
	}

	function __CheckPath4Template($pageTemplate, $currentPageUrl, &$arVariables)
	{
		$pageTemplateReg = preg_replace("'#[^#]+?#'", "([^/]+?)", $pageTemplate);
		if (substr($pageTemplateReg, -1, 1) == "/")
			$pageTemplateReg .= "index\\.php";

		$arValues = array();
		if (preg_match("'^".$pageTemplateReg."$'", $currentPageUrl, $arValues))
		{
			$arMatches = array();
			if (preg_match_all("'#([^#]+?)#'", $pageTemplate, $arMatches))
			{
				for ($i = 0, $cnt = count($arMatches[1]); $i < $cnt; $i++)
					$arVariables[$arMatches[1][$i]] = $arValues[$i + 1];
			}
			return True;
		}

		return False;
	}

	function ParseComponentPath($folder404, $arUrlTemplates, &$arVariables, $requestURL = False)
	{
		global $APPLICATION;

		if (!isset($arVariables) || !is_array($arVariables))
			$arVariables = array();

		if ($requestURL === False)
			$requestURL = $APPLICATION->GetCurPage(true);

		$folder404 = str_replace("\\", "/", $folder404);
		if ($folder404 != "/")
			$folder404 = "/".Trim($folder404, "/ \t\n\r\0\x0B")."/";

		//SEF base URL must match curent URL (several components on the same page)
		if(strpos($requestURL, $folder404) !== 0)
			return false;

		$currentPageUrl = SubStr($requestURL, StrLen($folder404));
		foreach ($arUrlTemplates as $pageID => $pageTemplate)
		{
			$pos = StrPos($pageTemplate, "?");
			if ($pos !== False)
				$pageTemplate = SubStr($pageTemplate, 0, $pos);

			if (StrPos($pageTemplate, "#") !== False)
				continue;

			if (CComponentEngine::__CheckPath4Template($pageTemplate, $currentPageUrl, $arVariables))
				return $pageID;
		}

		foreach ($arUrlTemplates as $pageID => $pageTemplate)
		{
			$pos = StrPos($pageTemplate, "?");
			if ($pos !== False)
				$pageTemplate = SubStr($pageTemplate, 0, $pos);

			if (StrPos($pageTemplate, "#") === False)
				continue;

			if (CComponentEngine::__CheckPath4Template($pageTemplate, $currentPageUrl, $arVariables))
				return $pageID;
		}

		return False;
	}

	function InitComponentVariables($componentPage, $arComponentVariables, $arVariableAliases, &$arVariables)
	{
		if (!isset($arVariables) || !is_array($arVariables))
			$arVariables = array();

		if ($componentPage)
		{
			if (array_key_exists($componentPage, $arVariableAliases) && is_array($arVariableAliases[$componentPage]))
			{
				foreach ($arVariableAliases[$componentPage] as $variableName => $aliasName)
					if (!array_key_exists($variableName, $arVariables))
						$arVariables[$variableName] = $_REQUEST[$aliasName];
			}
		}
		else
		{
			foreach ($arVariableAliases as $variableName => $aliasName)
				if (!array_key_exists($variableName, $arVariables))
					if (is_string($aliasName) && array_key_exists($aliasName, $_REQUEST))
						$arVariables[$variableName] = $_REQUEST[$aliasName];
		}

		for ($i = 0, $cnt = count($arComponentVariables); $i < $cnt; $i++)
			if (!array_key_exists($arComponentVariables[$i], $arVariables)
				&& array_key_exists($arComponentVariables[$i], $_REQUEST))
			{
				$arVariables[$arComponentVariables[$i]] = $_REQUEST[$arComponentVariables[$i]];
			}
	}

	function MakeComponentUrlTemplates($arDefaultUrlTemplates, $arCustomUrlTemplates)
	{
		if (!is_array($arCustomUrlTemplates))
			$arCustomUrlTemplates = array();

		return array_merge($arDefaultUrlTemplates, $arCustomUrlTemplates);
	}

	function MakeComponentVariableAliases($arDefaultVariableAliases, $arCustomVariableAliases)
	{
		if (!is_array($arCustomVariableAliases))
			$arCustomVariableAliases = array();

		return array_merge($arDefaultVariableAliases, $arCustomVariableAliases);
	}

	function MakePathFromTemplate($template, $arParams = array())
	{
		$arPatterns = array("#SITE_DIR#", "#SITE#", "#SERVER_NAME#");
		$arReplace = array(SITE_DIR, SITE_ID, SITE_SERVER_NAME);
		foreach ($arParams as $key => $value)
		{
			$arPatterns[] = "#".$key."#";
			$arReplace[] = $value;
		}

		return str_replace($arPatterns, $arReplace, $template);
	}
}
?>