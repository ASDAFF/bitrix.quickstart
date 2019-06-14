<?
	namespace Webprofy\Ajax\Fields;

	class File extends \Webprofy\Ajax\Field{
		//protected $template = '<input type="file" name="%NAME" id="file_field_%NAME" /><label for="file_field_%NAME">Файл не выбран.</label>';
		protected $template = '<input type="file" name="%NAME" id="file_field_%NAME" />';
		function getValue(){
			return $_FILES[$this->name];
		}
	}