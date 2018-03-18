<?
IncludeModuleLangFile(__FILE__);

class DSocialMediaPoster {

	static $MODULE_ID = "defa.socialmediaposter";

	function GetHost($IBLOCK_ID, $withoutProto = false)
	{

		$arSites = array();
		$rs = CIBlock::GetSite($IBLOCK_ID);
		while ($res = $rs->Fetch()) {
			$arSites[] = $res["LID"];
			if (!empty($res["SERVER_NAME"])) {
				$host = $res["SERVER_NAME"];
				break;
			}
		}
		
		if (empty($host)) {
			foreach ($arSites as $siteID) {
				$rsSites = CSite::GetByID($siteID);
				$arSite = $rsSites->Fetch();
				if (!empty($arSite["DOMAINS"])) {
					if (substr_count($arSite["DOMAINS"], "\n") > 0) {
						$arSite["DOMAINS"] = explode("\n", $arSite["DOMAINS"]);
						$host = trim(array_shift($arSite["DOMAINS"]));
					}
					else
						$host = trim($arSite["DOMAINS"]);
				}
			}
		}
		
		if (empty($host) && !empty($_SERVER["HTTP_HOST"]))
			$host = $_SERVER["HTTP_HOST"];

		if (empty($host)) {
			return false;
		}

		// get domain punycode 
		$IDN = new idna_convert(array("idn_version" => 2008)); 
		$host = $IDN->encode($host);
		// get domain punycode 

		if ($withoutProto)
			return $host;
		else
			return "http://".$host;
		
	}

}

?>