<?

	namespace Webprofy;
	use Bitrix\Highloadblock\HighloadBlockTable as Table;

	class SiteVariables{
		private static
			$instance = null,
			$settings = array(
				'highload' => 3
			);

		static function getInstance(){
			if(self::$instance == null){
				self::$instance = new self;
			}
			return self::$instance;
		}

		private function __construct(){}

		private $cache = array();

		function one($name){
			return $this->get($name, 0);
		}

		function phone($name){
	        return \WP::parsePhone($this->one('main-phone'));
		}

		function get($name, $index = null){
			if(isset($this->cache[$name.'_'.$index])){
				return $this->cache[$name.'_'.$index];
			}
			\CModule::IncludeModule('highloadblock');
			 
			$data = Table::getById(self::$settings['highload'])->fetch();
			Table::compileEntity($data);

			$class = $data['NAME'].'Table';
			 
			$listData = array(
			     'select' => array('ID', 'UF_VALUE'),
			     'order' => array('UF_PRIORITY' =>'ASC'),
			     'filter' => array('UF_CODE' => $name),
			);

			if(is_int($index)){
				$listData['limit'] = $index.',1';
			}

			$result = $class::getList($listData);
			 
			$values = array();
			while(($element = $result->fetch()) !== false){
				$value = $element['UF_VALUE'];
				if(is_int($index)){
					$this->cache[$name.'_'.$index] = $value;
					return $value;
				}
				$values[] = $value;
			}
			if(is_int($index)){
				return null;
			}
			$this->cache[$name.'_'.$index] = $values;
			return $values;
		}
	}