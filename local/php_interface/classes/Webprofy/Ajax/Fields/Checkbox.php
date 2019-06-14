<?
	namespace Webprofy\Ajax\Fields;

	class Checkbox extends \Webprofy\Ajax\Field{
		private static $lastID = 0;
		protected
			$template = '<input %ID type="checkbox" name="%NAME" %CHECKED/>%LABEL',
			$label = null;

		function getValue(){
			if($this->valueSet == false){
				$this->setValue(isset($_POST[$this->name]));
			}
			return $this->value ? true : false;
		}

		function setLabel($label){
			$this->label = $label;
		}

		function html(){
			$label = '';
			$id = '';

			if($this->label){
				$id_ = 'Webprofy_Ajax_Field_File_'.self::$lastID++;
				$id = 'id="'.$id_.'"';
				$label = '<label for="'.$id_.'">'.$this->label.'</label>';
			}
			return strtr($this->template, array(
				'%NAME' => $this->name,
				'%CHECKED' => $this->getValue() ? 'checked="checked"' : '',
				'%LABEL' => $label,
				'%ID' => $id
			));
		}
	}