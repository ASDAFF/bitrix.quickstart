<?
	namespace Webprofy\Ajax\Fields;

	class Select extends \Webprofy\Ajax\Field{
		private static $lastID = 0, 
				$selected = '',
				$options = '';
		protected
			$template = '<select %ID name="%NAME">%OPTIONS</select>';
			
		function getValue(){
			if($this->valueSet == false){
				$this->setValue(isset($_POST[$this->name]));
			}
			return $this->value;
		}
				
		function addOption($name, $value) {
			
			if ($this->getValue() == $value) {
				$selected = ' selected';
			} else {
				$selected = '';
			}			
			$this->options .= '<option value="'.$value.'"'.$selected.'>'.$name.'</option>';
			
		}

		function html(){
			
			return strtr($this->template, array(
				'%NAME' => $this->name,
				'%OPTIONS' => $this->options,
				'%ID' => $id
			));
		}
	}