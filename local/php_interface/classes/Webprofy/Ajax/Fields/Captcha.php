<?
	namespace Webprofy\Ajax\Fields;

	class Captcha extends \Webprofy\Ajax\Field{
		protected
			$template = '
				<img
					src="/bitrix/tools/captcha.php?captcha_sid=%CODE"
					alt="captcha"
					width="110"
					height="33"
					class="js-captcha-image"
				/><br/>
				<input type="text" class="js-captcha-text" name="%NAME" value="%VALUE">
				<input type="hidden" class="js-captcha-code" name="%NAME_check" value="%CODE">
			';

		public function check($value){
			global $APPLICATION;
			$checkValue = $_POST[$this->name.'_check'];
			if(!$APPLICATION->CaptchaCheckCode(
				$value,
				$checkValue
			)){
				$this->setError('captcha');
				return false;
			}
			return true;
		}

		public function html(){
			global $APPLICATION;
			$code = $APPLICATION->CaptchaGetCode();
			return strtr(parent::html(), array(
				'%CODE' => $code
			));
		}
	}