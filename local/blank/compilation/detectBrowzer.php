<?
/**
	 * user_browser()
	 */
	function UserBrowser($agent) 
	{
		preg_match("/(MSIE|Opera|Firefox|Chrome|Version|Opera Mini|Netscape|Konqueror|SeaMonkey|Camino|Minefield|Iceweasel|K-Meleon|Maxthon)(?:\/| )([0-9.]+)/", $agent, $browser_info);
		list(,$browser,$version) = $browser_info;
		if (preg_match("/Opera ([0-9.]+)/i", $agent, $opera)) return 'Opera '.$opera[1];
		if ($browser == 'MSIE') {
			preg_match("/(Maxthon|Avant Browser|MyIE2)/i", $agent, $ie);
			if ($ie) return $ie[1].' based on IE '.$version;
			return 'IE '.$version;
		}
	    if ($browser == 'Firefox') {
			preg_match("/(Flock|Navigator|Epiphany)\/([0-9.]+)/", $agent, $ff);
			if ($ff) return $ff[1].' '.$ff[2];
		}
		if ($browser == 'Opera' && $version == '9.80') return 'Opera '.substr($agent,-5);
		if ($browser == 'Version') return 'Safari '.$version;
		if (!$browser && strpos($agent, 'Gecko')) return 'Browser based on Gecko';
		return $browser.' '.$version;
	}
	
	/**
	 * user_min_browser()
	 */
	function UserMinBrowser($agent) 
	{
		preg_match("/(MSIE|Opera|Firefox|Chrome|Version)(?:\/| )([0-9.]+)/", $agent, $browser_info);
		list(,$browser,$version) = $browser_info;
		if ($browser == 'Opera' && $version == '9.80') return 'Opera '.substr($agent,-5);
		if ($browser == 'Version') return 'Safari '.$version;
		if (!$browser && strpos($agent, 'Gecko')) return 'Browser based on Gecko';
		return $browser.' '.$version;
	}
?>
