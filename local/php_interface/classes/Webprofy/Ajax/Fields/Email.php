<?
	namespace Webprofy\Ajax\Fields;

	class Email extends \Webprofy\Ajax\Field{
		public function check($value){
			$value = htmlspecialchars(trim($value));
			if(!filter_var($value, FILTER_VALIDATE_EMAIL)){
				$this->setError('filter');
				return false;
			}
			$this->value = $value;
			return true;
		}
	}