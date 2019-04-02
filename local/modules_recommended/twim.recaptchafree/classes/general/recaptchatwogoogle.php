<?
use Bitrix\Main\Page\Asset,
	Bitrix\Main\Application,
    Bitrix\Main\SystemException; 
require_once 'browser.php';

class ReCaptchaTwoGoogle{
/* 
	ReCaptcha 2.0 Google
	modul bitrix
	Shevtcoff S.V. 
	date 24.03.17
	time 12:01
*/
	public function OnAddContentCaptcha(&$content) 
	{	
		if (!defined("ADMIN_SECTION")){
			$arSettings = self::getParamSite();
			if($arSettings["act"] == "Y" && self::checkBrowser() && !self::checkMask($arSettings["mask_exclusion"])){
				$theme = isset($arSettings["theme"]) ? $arSettings["theme"] : 'light';
                $size = isset($arSettings["size"]) ? $arSettings["size"] : 'normal';
                $badge = isset($arSettings["badge"]) ? $arSettings["badge"] : 'bottomright';
				$search = array (
					'/<img src="\/bitrix\/tools\/captcha\.php\?captcha_sid=(.+?(?=>))>/is',
					'/<img src="\/bitrix\/tools\/captcha\.php\?captcha_code=(.+?(?=>))>/is',
					'/name="captcha_word"/is'
					);
				$replace = array (
					'<div class="g-recaptcha" data-theme="' . $theme . '" data-sitekey="'. $arSettings["key"] .'"  data-size="'. $size .'"  data-badge="'. $badge .'" data-callback="RecaptchafreeSubmitForm"></div>',
					'<div class="g-recaptcha" data-theme="' . $theme . '" data-sitekey="'. $arSettings["key"] .'" data-size="'. $size .'"  data-badge="'. $badge .'" data-callback="RecaptchafreeSubmitForm"></div>',
					'name="captcha_word" style="display:none" value="'. substr($arSettings["key"], 0, 5).'"'
					);
				$content = preg_replace($search, $replace, $content);
			}
		} 
	} 
	
	public function OnVerificContent(){
 		if (defined("ADMIN_SECTION")){ return; }
		$arSettings = self::getParamSite();
        if($arSettings["act"] !== "Y" && !self::checkBrowser()){return;}
            Asset::getInstance()->addJs('/bitrix/js/twim.recaptchafree/script.js');
            Asset::getInstance()->addJs('https://www.google.com/recaptcha/api.js?onload=onloadRecaptchafree&render=explicit&hl='. LANGUAGE_ID);
            $arRequest =  self::getParamRequest();
            if(empty($arRequest["captcha_sid"]) && empty($arRequest["g-recaptcha-response"])){return;}
            $res = json_decode(self::getOutData($arRequest, $arSettings), true);
            try {
                if($res['success']){ // success unswer from google
                    global $DB;
                    $DB->PrepareFields("b_captcha");
                    $arFields = array("CODE" => "'".$DB->ForSQL(strtoupper($arRequest["captcha_word"]), 5)."'");
                    $DB->StartTransaction();
                    $DB->Update("b_captcha", $arFields, "WHERE ID='".$DB->ForSQL($arRequest["captcha_sid"], 32)."'", $err_mess.__LINE__);
                    $DB->Commit();
                } else{
                    throw new SystemException("Please verify your reCAPTCHA");
                }
            } catch (SystemException $exception) {
                $exception->getMessage();
            } 
            try {
                if(!is_array($res)){
                    throw new SystemException("Not work \"curl_init\" or \"file_get_contents\" functions");
                }
            } catch (SystemException $exception) {
                $exception->getMessage();
            } 
	}
    /**
     * getParamRequest
     * @return $arRequest
     */
	private function getParamRequest(){
        $arRequest = array();
        $context = Application::getInstance()->getContext(); 
        $request = $context->getRequest(); 
		if($request->isPost()){
            $captcha_sid = $request->getPost("captcha_sid");
            $captcha_code = $request->getPost("captcha_code");
            $arRequest["captcha_sid"] = (!empty($captcha_sid)) ? $captcha_sid : $captcha_code;
            $arRequest["captcha_word"] = $request->getPost("captcha_word");
            $arRequest["g-recaptcha-response"] = $request->getPost("g-recaptcha-response");
        } else{
            $captcha_sid = $request->getQuery("captcha_sid");
            $captcha_code = $request->getQuery("captcha_code");
            $arRequest["captcha_sid"] = (!empty($captcha_sid)) ? $captcha_sid : $captcha_code;
            $arRequest["captcha_word"] = $request->getQuery("captcha_word");
            $arRequest["g-recaptcha-response"] = $request->getQuery("g-recaptcha-response"); 
        }
		return $arRequest;
	}
	/**
     * getParamSite
     * @return array params module
     */
	private function getParamSite(){
		$settings = COption::GetOptionString("twim.recaptchafree", "settings", false, SITE_ID);
		return  unserialize($settings);
	}
    /**
     * checkMask
     * @param string $mask_exc 
     * @return boolean
     */
    private function checkMask($mask_exc){
		$request = Application::getInstance()->getContext()->getServer(); 
        $mask = explode(";", $mask_exc);
		$arMask = array_map(function($n){return trim($n);}, $mask); // trim space in arrat items
        $reg = '%^' . implode('|', $arMask) . '%i'; // set reg
        if($request["REAL_FILE_PATH"]){ // real page
            $url = $request["REAL_FILE_PATH"];
        } else {
            $url = $request->getScriptName(); 
        }
        if (!preg_match($reg, $url)){ 
            return false; // no page in mask
        } else {
            return true; // page in mask
        }
	}
    /**
     * check support browser
     * @return boolean
     */
    private function checkBrowser(){
		$browser = new Browser(); // param brouser
        $name = $browser->getBrowser();
        $version = $browser->getVersion();
        $mobile = $browser->isMobile();
        $table = $browser->isTablet();
        if($name === 'Android' && $version < '4.0'){ // Android native < 4  not support
            return false;		
        } elseif($name === 'Opera' && $version < '13' && ($mobile || $table)){ // Opera Mobile not support
            return false;		
        } else {
            return true;		
        }  
	}
	/**
     * get data
     * @return json or false
     */
	private function getOutData($arRequest, $arSettings)
	{
        $context = Application::getInstance()->getContext(); 
        $server = $context->getServer();
		$curlData = false; 
        $google_url="https://www.google.com/recaptcha/api/siteverify";
		$url = $google_url."?secret=".$arSettings["secretkey"]."&response=".$arRequest["g-recaptcha-response"]."&remoteip=".$server->get('REMOTE_ADDR');
		if (function_exists('curl_init')) { // support curl
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_TIMEOUT, 10);
			curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.16) Gecko/20110319 Firefox/3.6.16");
			$curlData = curl_exec($curl);
			curl_close($curl);
		} else { // support file_get_contents
			$curlData = file_get_contents($url);
		}
		return $curlData;
	}
}