<?
	namespace MHT\Forms;

	use \Webprofy\Ajax as Ajax;
	use \Webprofy\Ajax\Fields as Fields;
	
	class warehouse extends Ajax\Form{
		function __construct(){
			parent::__construct('warehouse');

			foreach(array(
				'name',
				'email',
				'phone',
				'address',
				'square',
			) as $name){
				$field = new Fields\Text($name, true);
				$this->addField($field);
			}

			$field = new Fields\Text('advice', true);
			$field->setTemplate('
				<input type="radio" name="%NAME" value="rent" id="%NAME_rent" checked="checked"><label for="%NAME_rent">Аренда</label>&nbsp;&nbsp;&nbsp;
				<input type="radio" name="%NAME" value="sell" id="%NAME_sell"><label for="%NAME_sell">Продажа</label>	
			');
			$this->addField($field);

			$field = new Fields\Text('text', true);
			$field->setTemplate('<textarea name="%NAME"></textarea>');
			$this->addField($field);
		}

		function execute($f){
			\WP::addElement(array(
				'f' => array(
					'NAME' => $f['name'],
					'IBLOCK_ID' => 442,
					'PREVIEW_TEXT' => $f['text'],
				),
				'p' => array(
					'PHONE' => $f['phone'],
					'EMAIL' => $f['email'],
					'SQUARE' => $f['square'],
					'ADDRESS' => $f['address'],
				)
			));
		}
	}