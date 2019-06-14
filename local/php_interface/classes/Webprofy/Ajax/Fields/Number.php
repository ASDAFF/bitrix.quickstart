<?
	namespace Webprofy\Ajax\Fields;

	class Number extends \Webprofy\Ajax\Field{
		public function check($value){
			$this->value = intval($value);
			return true;
		}
	}