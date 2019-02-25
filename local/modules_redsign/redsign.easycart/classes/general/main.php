<?
/************************************
*
* general class
* last update 24.07.2014
*
************************************/

IncludeModuleLangFile(__FILE__);

class CRSEasyCartMain
{
	static function OnBeforeLocalRedirect(&$url)
	{
		$service_url = COption::GetOptionString('redsign.easycart', 'service_url', '');
		if($_REQUEST['rsec_ajax_post']=='Y' && $_REQUEST['rsec_mode']=='basket' && $service_url!='')
		{
			$url = $service_url.'?rsec_ajax_post=Y&rsec_mode=basket';
		}
	}
}