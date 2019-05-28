<?
	
	class CMLNameConnector{
		// STATIC
		private static $instances = array();
		static function get($type){
			if(isset(self::$instances[$type])){
				return self::$instances[$type];
			}
			$instance = null;
			switch($type){
				case 'prop':
					$instance = new self('mht_props_ids');
					break;

				case 'enum':
					$instance = new self('mht_enum_ids');
					break;
			}
			self::$instances[$type] = $instance;
			return $instance;
		}

		static function xid($type, $xid, $name = null){
			$o = self::get($type);
			if($o === null){
				return null;
			}
			return $o->getXID($xid, $name);
		}

		// DYNAMIC
		private
			$tableName = '',
			$xidByXid = array(),
			$xidByCode = array();

		function __construct($tableName){
			$this->tableName = $tableName;
			$this->initXIDs();
		}

		function translit($s){
			return CUtil::translit($s, LANGUAGE_ID, array(
				"max_len" => 50,
				"change_case" => 'U', // 'L' - toLower, 'U' - toUpper, false - do not change
				"replace_space" => '-',
				"replace_other" => '-',
				"delete_repeat_replace" => true,
			));
		}
		
		function getXID($xid, $name = null){
			if(isset($this->xidByXid[$xid])){
				return $this->xidByXid[$xid];
			}

			if($name === null){
				return $xid;
			}

			$code = $this->translit($name);

			if(isset($this->xidByCode[$code])){
				$_xid = $this->xidByCode[$code];
				$this->addXID($xid, $_xid, $code);
				return $_xid;
			}

			$this->addXID($xid, $xid, $code);
			return $xid;
		}

		function addXID($prev, $new, $code){
			global $DB;
			$DB->Query('
				INSERT INTO
					'.$this->tableName.'
				(`prev`, `new`, `code`)
					VALUES
				("'.$prev.'", "'.$new.'", "'.$code.'")
			');
			$this->xidByCode[$code] = $new;
			$this->xidByXid[$prev] = $new;
		}

		function initXIDs(){
			global $DB;
			$result = $DB->Query('
				SELECT
					new,
					prev,
					code
				FROM
					`'.$this->tableName.'`
			');

			$this->xidByXid = array();
			while(($row = $result->Fetch()) !== false){
				$this->xidByXid[$row['prev']] = $row['new'];
				$this->xidByCode[$row['code']] = $row['new'];
			}
		}
	}