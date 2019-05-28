<?
	namespace Webprofy\Ajax;

	class Captcha{
		private
			$inputField,
			$checkField;

		function __construct($inputField, $checkField){
			$this->inputField = $inputField;
			$this->checkField = $checkField;
		}

		function check(){
			global $APPLICATION;
			if($APPLICATION->CaptchaCheckCode(
				$inputField->getValue(),
				$checkField->getValue()
			)){
				return true;
			}

			return false;
		}
	}