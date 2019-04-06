<?
$MODULE_ID='slobel.canonical';
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$MODULE_ID."/classes/main.php");
class SL_ChangeCanonical
{
	public static function Handler(&$content)
	{
		if (!defined('ADMIN_SECTION') || ADMIN_SECTION!==true)
			$content = preg_replace_callback('#\<head\>(.*?)\<\/head\>#is','self::changeCanonical',$content);
	}
	
	private function changeCanonical($matches){
		$params=array();
		$relUrl="";
		$baseFlag=true;
		$originURL=explode("?", $_SERVER["REQUEST_URI"]);
		$url=$originURL[0];
		$arOtherParams=explode("&",$originURL[1]);
		
		if(strpos($matches[0], "rel=\"canonical\"")===false){
			$rsData=SlobelCanonical::GetList(array(), array('ACTIVE'=>'Y'));
			while($arRes = $rsData->Fetch())
				$params[]=$arRes;
			
			foreach($params as $key => $val){
				
				if(preg_match(stripslashes($val['RULE']),$originURL[1], $preg) && (empty($val['FILE']) || $val['FILE']==$originURL[0]))
						unset($arOtherParams[array_search($preg[0],$arOtherParams)]);
				
				if(!empty($val['BASE']) && !empty($val['FILE'])){
							$baseFlag=false;
							$url=$val['BASE'];
				}
				elseif(!empty($val['BASE']) && $baseFlag) 
					$url=$val['BASE'];
			}
			
			$relUrl=$url;
			if(count($arOtherParams)>1)
				$relUrl.="?".implode("&",$arOtherParams);
			
			return "<head>$matches[1]\n<link rel=\"canonical\" href=\"$relUrl\"/></head>";
			
		}
		else return $matches[0];
	}
}
?>