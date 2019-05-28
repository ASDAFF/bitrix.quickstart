<?
	namespace MHT;

	class CatalogSort{
		private static $instance = null;
		public static function getInstance(){
			if(!self::$instance){
				self::$instance = new self();
			}
			return self::$instance;
		}

		private $types = array(
				5 => array('(выберите сортировку)', 'photo', false),
				1 => array('уменьшению цены', 'price', false),
				2 => array('увеличению цены', 'price', true),
				3 => array('названию', 'name', true),
				4 => array('популярности', 'pop', false),
			),
			$fields = array(
				'price' => 'catalog_PRICE_1',
				'name' => 'NAME',
				'pop' => 'SHOW_COUNTER',
				'photo' => 'HAS_DETAIL_PICTURE'
			),
			$default = 5,
			$current = null,
			$listId = '',
			$sessionIndex = 'MHT_CatalogSort_amount';

		private function __construct(){
			if(isset($_SESSION[$this->sessionIndex.$this->listId])){
				$this->current = $_SESSION[$this->sessionIndex.$this->listId];
			}
			else{
				$this->current = $this->default;
			}
			/* if($this->current !== $this->default){
				unset($this->types[$this->default]);
			} */
		}

		// ID списка позволяет хранить разные настройки сортировки для разных списков
		function setListId($id){
			$this->listId = $id;
			$this->current = $_SESSION[$this->sessionIndex.$this->listId];
			if(!$this->current){
				$this->current = $this->default;
			}
		}

		function isDefault(){
			return $this->current == $this->default;
		}

		function get($index = null){
			$a = $this->types[$this->current];
			$result = array(
				'name' => $a[0],
				'field' => $this->fields[$a[1]],
				'order' => $a[2] ? 'ASC' : 'DESC',
				'list-id' => $this->listId
			);
			if($index === null){
				return $result;
			}
			return $result[$index];
		}

		function set($n){
			if(!isset($this->types[$n])){
				$n = $this->default;
			}
			$this->current = $_SESSION[$this->sessionIndex.$this->listId] = $n;
		}

		function getOptions(){
			$html = '';
			foreach($this->types as $value => $name){
				// Пропустить пункт "(выберите сортировку)", если сортировка уже выбрана
				if($this->current !== $this->default && $value == $this->default)
					continue; 

				$html .= '<option value="'.$value.'"'.($value == $this->current ? ' selected="selected"' : '').'>'.$name[0].'</option>';
			}
			return $html;
		}
	}
?>