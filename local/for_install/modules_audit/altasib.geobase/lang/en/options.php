<?
/**
 * Company developer: ALTASIB
 * Developer: adumnov
 * Site: http://www.altasib.ru
 * E-mail: dev@altasib.ru
 * @copyright (c) 2006-2015 ALTASIB
 */ 

$CookiePX = COption::GetOptionString("main", "cookie_name", "BITRIX_SM");

$MESS['ALTASIB_IS'] = "Shop complete solutions for 1C-Bitrix";
$MESS['ALTASIB_GEOBASE_DESCR'] = 'The module receives the user\'s location by its IP-address and stores this data in the session and if it is established in the cookies.<br/><br/>
<b>Developer Information</b>
<br/>Data is stored in the cookies as a JSON-encoded array: '. $CookiePX .'_ ALTASIB_GEOBASE and'. $CookiePX .'_ ALTASIB_GEOBASE_CODE, and in the form of regular arrays: $_SESSION["ALTASIB_GEOBASE"] and $_SESSION["ALTASIB_GEOBASE_CODE"] - auto-sensing and user specified respectively.<br/><br/>
<div id = "altasib_description_open_btn"><span class = "altasib_description_open_text">Read more</span></div><div id = "altasib_description_full">Get information, you can:
<pre>
if(CModule::IncludeModule("altasib.geobase")) {
	$arData = CAltasibGeoBase::GetAddres();
	print_r($arData);
}
// to obtain data KLADR defined automatically by location:
if(CModule::IncludeModule("altasib.geobase")) {
	$arData = CAltasibGeoBase::GetCodeByAddr();
	print_r($arData);
}
// to obtain data KLADR user-defined:
if(CModule::IncludeModule("altasib.geobase")) {
	$arData = CAltasibGeoBase::GetDataKladr();
	print_r($arData);
}
// for the location of a <a href="/bitrix/admin/sale_location_admin.php">list of locations</a>, installed on site:
if(CModule::IncludeModule("altasib.geobase")) {
	$arData = CAltasibGeoBase::GetBXLocations();
	print_r($arData);
}
// Get data from cookies:
$arDataC = CAltasibGeoBase::deCodeJSON($APPLICATION->get_cookie("ALTASIB_GEOBASE_CODE"));
print_r($arDataC);
</pre>
Events module:<br/><br/>
<table class="internal" width="100%">
	<tbody>
		<tr>
			<th>Event</th>
			<th>Called</th>
			<th>Method</th>
			<th>From version</th>
		</tr>
		<tr>
			<td>OnAfterSetSelectCity</td>
			<td>after selecting the city of user</td>
			<td>CAltasibGeoBase::SetCodeKladr
			<br/>CAltasibGeoBase::SetCodeMM</td>
			<td>1.1.3</td>
		</tr>
	</tbody>
</table>
<br/>
There is also a js-event <b>onAfterSetCity</b>, is called after the selection (setting) of the city by the user.<br/>Input parameters for the event handler: 
(string name, string id, string full_name, string data);<br/>
<pre>// Example reload the page to select a city:
BX.addCustomEvent("onAfterSetCity", function(city, city_id, full_name){
    location.reload();
});
</pre><br/><br/>
<div id="altasib_description_close_btn">
	<span class="altasib_description_open_text">Collapse</span>
</div>
</div>
';

$MESS['ALTASIB_GEOBASE_SET_COOKIE'] = "Save to cookies location information";
$MESS['ALTASIB_GEOBASE_SET_TIMEOUT'] = "Script Execution time:";
$MESS['ALTASIB_TAB_BD_DATA'] = "Data";
$MESS['ALTASIB_TAB_TITLE_DATA'] = "Sources positioning module supports";
$MESS['ALTASIB_GEOBASE_DB_UPDATE_IPGEOBASE'] = "Updating the database module from the site <a href='http://ipgeobase.ru/' target='_blank'>ipgeobase.ru</a>";
$MESS['ALTASIB_TAB_BD_CITIES'] = "Favorites Cities";
$MESS['ALTASIB_TAB_TITLE_DB_CITIES'] = "Add and edit your favorites list module";
$MESS['ALTASIB_TITLE_LOAD_FILE'] = "Download archive:";
$MESS['ALTASIB_TITLE_UNPACK_FILE'] = "Unpacking:";
$MESS['ALTASIB_TITLE_DB_UPDATE'] = "Database Update (<a href='http://ipgeobase.ru/' target='_blank'>ipgeobase.ru</a>):";

$MESS['ALTASIB_NOTICE_UPDATE_AVAILABLE'] = "Available updated archive of data from the website <a href='http://ipgeobase.ru/' target='_blank'>ipgeobase.ru</a>.";
$MESS['ALTASIB_NOTICE_UPDATE_NOT_AVAILABLE'] = "No update is available on the website <a href='http://ipgeobase.ru/' target='_blank'>ipgeobase.ru</a>.";
$MESS['ALTASIB_NOTICE_DBUPDATE_SUCCESSFUL'] = "Database Update from site <a href='http://ipgeobase.ru/' target='_blank'>ipgeobase.ru</a> completed successfully.";
$MESS['ALTASIB_GEOBASE_GET_UPDATE'] = "Check for updates archives database locations on site <a href='http://ipgeobase.ru/' target='_blank'>ipgeobase.ru</a> automatically:";
$MESS["ALTASIB_NOTICE_UPDATE_MANUAL_MODE"] = "To check for updates from the site <a href='http://ipgeobase.ru/' target='_blank'>ipgeobase.ru</a> click \"Check for updates\"";

$MESS["ALTASIB_CHECK_UPDATES"] = "Check for updates online <a href='http://ipgeobase.ru/' target='_blank'>ipgeobase.ru</a>...";
$MESS["ALTASIB_GEOBASE_SOURCE"] = "Source location:";
$MESS["ALTASIB_GEOBASE_NOT_USING"] = "Do not use a local database";
$MESS["ALTASIB_GEOBASE_LOCAL_DB"] = "Use the local database ipgeobase.ru";
$MESS["ALTASIB_GEOBASE_STATISTIC"] = "Use Web analytics Bitrix";
$MESS["ALTASIB_GEOBASE_UPDATE"] = "Update";
$MESS["ALTASIB_GEOBASE_CHECK_UPDATE"] = "Check for updates";
$MESS["ALTASIB_GEOBASE_WIN_YOUR_CITY_ENABLE"] = "Enable <b>automatic show</b> a popup window \"Your city\":";
$MESS["ALTASIB_GEOBASE_ONLY_SELECT_CITIES"] = 'Use only city from <a title="Favorites Cities" onclick="tabControl.SelectTab(\'edit3\'); return false;"> list of favorites cities</a>, without a field Search:';

$MESS['ALTASIB_TITLE_CITIES_LIST'] = "List of cities";
$MESS['ALTASIB_TABLE_CITY_DELETE'] = "Delete";
$MESS['ALTASIB_TABLE_CITY_ADD'] = "Add";
$MESS['ALTASIB_INP_CITY_ADD'] = "Adding a city to the list of selected cities";
$MESS['ALTASIB_INP_ENTER_CITY'] = "Enter the name of the city";
$MESS['ALTASIB_TABLE_CITY_NAME'] = "The name of the city";
$MESS['ALTASIB_TABLE_CITY_CODE'] = "Code n/a";
$MESS['ALTASIB_TABLE_DISTRICT'] = "District";
$MESS['ALTASIB_TABLE_REGION'] = "Region";
$MESS['ALTASIB_TABLE_COUNTRY_CODE'] = "Country code";
$MESS['ALTASIB_TABLE_COUNTRY'] = "Country";
$MESS['ALTASIB_TABLE_CITY_ACT'] = "Action";
$MESS['ALTASIB_GEOBASE_AUTO_DISPLAY'] = "Automatic display";
$MESS['ALTASIB_GEOBASE_GLOBAL_COMPONENTS'] = "General Settings components";
$MESS['ALTASIB_GEOBASE_LOCATIONS'] = "Settings replacement location in the module Sale";
$MESS['ALTASIB_GEOBASE_YOUR_CITY_DESCR'] = "\"Your city\" - a component that displays a pop-up window with the possibility of confirming the city visitor defined by its IP address, as well as a link to change";
$MESS['ALTASIB_GEOBASE_YOUR_CITY_TEMPLATES'] = "Template component \"Your city\" connect automatically";
$MESS['ALTASIB_GEOBASE_POPUP_BACK'] = "Dimming background in the derivation of pop-ups";
$MESS['ALTASIB_GEOBASE_REGION_DISABLE'] = "Do not print the name of the region and the district";

$MESS['ALTASIB_GEOBASE_SELECT_CITY_DESCR'] = "\"Select the city\" - a component that displays a link to open a pop-up window to select and save the town visitor";
$MESS['ALTASIB_GEOBASE_SELECT_CITY_TEMPLATES'] = "Template component \"Select the city\" connected component in the call automatically";
$MESS['ALTASIB_GEOBASE_ONLINE_ENABLE'] = "Use online services <a href = 'http://ipgeobase.ru/' target = '_blank' title = 'Geography Russian and Ukrainian IP-addresses. Search for the location (city) IP-addresses '>ipgeobase.ru</a> and <a href='http://geoip.elib.ru/' target='_blank' title='Determination of geographic coordinates by IP address'>geoip.elib.ru</a>";
$MESS['ALTASIB_GEOBASE_SITES'] = "Websites, pages that use automatic display";
$MESS['ALTASIB_GEOBASE_TEMPLATE'] = "Website templates, which enable Auto Play";
$MESS['ALTASIB_GEOBASE_SECTION_LINK'] = "Which sections replace the \"Location\" page for ordering (comma separated list, a blank value means' display without restrictions')<br />Example: <i>'/personal/order/make/, /personal/'</i>";
$MESS['ALTASIB_GEOBASE_SALE_LOCATION'] = "Location of the country default:";
$MESS['ALTASIB_GEOBASE_URL_NOT_FOUND'] = "The requested URL address of the remote server not found.";
$MESS['ALTASIB_GEOBASE_SET_SQL'] = "Adding to a long SQL-queries line \"SET SQL_BIG_SELECTS=1\"";

$MESS['ALTASIB_GEOBASE_RUSSIA'] = "Russia";
$MESS['ALTASIB_GEOBASE_RF'] = "Russian Federation";
$MESS['ALTASIB_GEOBASE_JQUERY'] = "Connect jQuery:";
$MESS['ALTASIB_GEOBASE_JQUERY_NOT'] = "The site is already connected jQuery";
$MESS['ALTASIB_GEOBASE_JQUERY_YES'] = "Yes, connect";
$MESS['ALTASIB_GEOBASE_FIELD_LOC_IND'] = "The ID of the input field the location of an individual on the checkout page:";
$MESS['ALTASIB_GEOBASE_FIELD_LOC_LEG'] = "The ID of the input field location of the legal entity on the checkout page:";
$MESS['ALTASIB_NOTICE_MM_UPDATE_AVAILABLE'] = "Available updated GeoLite data archive site <a href='http://dev.maxmind.com/geoip/legacy/geolite/' target='_blank'>maxmind.com</a>. ";
$MESS['ALTASIB_NOTICE_MM_UPDATE_NOT_AVAILABLE'] = "An update is available on the website <a href='http://dev.maxmind.com/geoip/legacy/geolite/' target='_blank'>maxmind.com</a> found . ";
$MESS['ALTASIB_NOTICE_MM_DBUPDATE_SUCCESSFUL'] = "Update database file GeoLite site <a href='http://dev.maxmind.com/geoip/legacy/geolite/' target='_blank'>maxmind.com</a> completed successfully. ";
$MESS['ALTASIB_GEOBASE_MM_GET_UPDATE'] = "Check for updates GeoLite database online <a href='http://dev.maxmind.com/geoip/legacy/geolite/' target='_blank'>maxmind.com</a> automatically: ";
$MESS["ALTASIB_NOTICE_MM_UPDATE_MANUAL_MODE"] = "To check for updates from the site <a href='http://dev.maxmind.com/' target='_blank'>maxmind.com</a> click \" Check for Updates \"";
$MESS['ALTASIB_TITLE_MM_DB_UPDATE'] = "Updating the database (<a href='http://dev.maxmind.com/' target='_blank'>maxmind.com</a>):";
$MESS["ALTASIB_CHECK_MM_UPDATES"] = "Check for updates online <a href='http://dev.maxmind.com/' target='_blank'>maxmind.com</a> ...";

$MESS['ALTASIB_GEOBASE_DEMO_MODE'] = "The module works in demo mode. <a target='_blank' href='http://marketplace.1c-bitrix.ru/tobasket.php?ID=#MODULE#'>Buy a version without limitation</a>";
$MESS['ALTASIB_GEOBASE_DEMO_EXPIRED'] = "Demo period of the module ended. <a target='_blank' href='http://marketplace.1c-bitrix.ru/tobasket.php?ID=#MODULE#'>Buy module</a>";
$MESS['ALTASIB_GEOBASE_NF'] = "Module #MODULE# is not defined.";
$MESS['ALTASIB_GEOBASE_AUTODETECT_EN'] = "Automatically add a certain city to the list of selected cities:";
$MESS['ALTASIB_GEOBASE_CITIES_WORLD_ENABLE'] = "Show cities in the world in the search component \"Select city\":";
?>