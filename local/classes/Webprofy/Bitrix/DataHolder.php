<?
	namespace Webprofy\Bitrix;

	abstract class DataHolder{
		protected
			$data = null,
			$id;

		function getId(){
			return $this->id;
		}

		public function __construct($id = null){
			$this->id = $id;
		}

		function _log(){
			\WP::log($this->data);
		}

		function resetData(){
			$this->data = $this->createData();
			return $this;
		}
		
		function setData($data){
			$this->data = $data;
			return $this;
		}

		function f(/* ... */){
			if($this->data == null){
				$this->data = $this->createData();
			}

			$o = $this->data;
			foreach(func_get_args() as $i){
				if(!is_array($o)){
					return null;
				}
				$o = $o[$i];
			}
			return $o;
		}

		protected function createData(){
			return array();
		}
	}