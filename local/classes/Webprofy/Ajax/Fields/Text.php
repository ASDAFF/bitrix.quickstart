<?
	namespace Webprofy\Ajax\Fields;

	class Text extends \Webprofy\Ajax\Field{
		private $pattern = null;

		public function addPattern($pattern){
			$this->pattern = $pattern;
			return $this;
		}

		public function check($value){
			$value = htmlspecialchars(trim($value));
			if(
				$this->pattern !== null &&
				!preg_match($this->pattern, $value)
			){
				return false;
			}
			$this->value = $value;
			return true;
		}


	}