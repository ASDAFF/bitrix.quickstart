<?
/**
 * This class is for internal use only, not a part of public API.
 * It can be changed at any time without notification.
 *
 * @access private
 */

namespace Bitrix\Sale\Location;

use Bitrix\Main;

class DBBlockInserter
{
	protected $tableName = 		'';
	protected $tableMap = 		array();
	protected $fldVector = 		array();

	protected $insertHead = 	'';
	protected $insertTail = 	'';
	protected $index = 			0;
	protected $bufferSize = 	0;
	protected $buffer = 		'';
	protected $map = 			array();

	protected $autoIncFld = 	false;
	protected $dbType = 		false;
	protected $mtu = 			0;

	protected $dbConnection = 	null;
	protected $dbHelper = 		null;
	protected $sqName = 		false;

	const RED_LINE = 			100;
	const DB_TYPE_MYSQL = 		'mysql';
	const DB_TYPE_MSSQL = 		'mssql';
	const DB_TYPE_ORACLE = 		'oracle';

	public function __construct($parameters = array())
	{
		$this->dbConnection = Main\HttpApplication::getConnection();
		$this->dbHelper = $this->dbConnection->getSqlHelper();

		$map = array();
		if(strlen($parameters['entityName']))
		{
			$table = $parameters['entityName'];

			$this->tableName = $table::getTableName();
			$this->tableMap = $table::getMap();

			// filter map throught $parameters['exactFields']
			if(is_array($parameters['exactFields']) && !empty($parameters['exactFields']))
			{
				foreach($parameters['exactFields'] as $fld)
				{
					if(!isset($this->tableMap[$fld]))
						throw new Main\SystemException('Field does not exist in ORM class, but present in "exactFields" parameter: '.$fld, 0, __FILE__, __LINE__);

					$map[] = $fld;
					$this->fldVector[$fld] = true;
				}
			}
			else
			{
				foreach($this->tableMap as $fld => $params)
				{
					$map[] = $fld;
					$this->fldVector[$fld] = true;
				}
			}
		}
		elseif(strlen($parameters['tableName']))
		{
			$this->tableName = $this->dbHelper->forSql($parameters['tableName']);
			$this->tableMap = $parameters['exactFields'];

			// $this->tableMap as $fld => $params - is the right way!
			/*
			required for

				$loc2site = new DBBlockInserter(array(
					'tableName' => 'b_sale_loc_2site',
					'exactFields' => array(
						'LOCATION_ID' => array('data_type' => 'integer'),
						'SITE_ID' => array('data_type' => 'string')
					),
					'parameters' => array(
						'mtu' => 9999,
						'autoIncrementFld' => 'ID'
					)
				));
			*/
			foreach($this->tableMap as $fld => $params)
			{
				$map[] = $fld;
				$this->fldVector[$fld] = true;
			}
		}

		// automatically insert to this field an auto-increment value
		// beware of TransactSQL`s IDENTITY_INSERT when setting autoIncrementFld to a database-driven auto-increment field
		if(strlen($parameters['parameters']['autoIncrementFld']))
		{
			$this->autoIncFld = $this->dbHelper->forSql($this->autoIncFld);

			$this->autoIncFld = $parameters['parameters']['autoIncrementFld'];
			if(!isset($this->fldVector[$this->autoIncFld]))
			{
				$map[] = $this->autoIncFld;
				$this->fldVector[$this->autoIncFld] = true;
				$this->tableMap[$this->autoIncFld] = array('data_type' => 'integer');
			}

			$this->initIndexFromField();
		}

		$this->dbType = Main\HttpApplication::getConnection()->getType();

		if(!($this->mtu = intval($parameters['parameters']['mtu'])))
			$this->mtu = 9999;

		$dbMtu = $this->getMaxTransferUnit();
		if($this->mtu > $dbMtu)
			$this->mtu = $dbMtu;

		if($this->dbType == self::DB_TYPE_ORACLE)
		{
			$this->insertHead = 'insert all ';
			$this->insertTail = ' select * from dual';
		}
		else
		{
			$this->insertHead = 'insert into '.$this->tableName.' ('.implode(',', $map).') values ';
			$this->insertTail = '';
		}

		$this->map = $map;
	}

	// this method is buggy when table is empty
	public function initIndexFromField($fld = 'ID')
	{
		if(!strlen($fld))
			throw new Main\SystemException('Field is not set');

		$fld = $this->dbHelper->forSql($fld);


		$sql = 'select MAX('.$fld.') as VAL from '.$this->tableName;

		$res = $this->dbConnection->query($sql)->fetch();
		$this->index = intval($res['VAL']);
		/*
		$sql = 'select '.$fld.' from '.$this->tableName.' order by '.$fld.' desc';
		$sql = $this->dbHelper->getTopSql($sql, 1);

		$res = $this->dbConnection->query($sql)->fetch();
		$this->index = intval($res[$this->autoIncFld]);
		*/

		return $this->index;
	}

	public function getIndex()
	{
		return $this->index;
	}

	public function dropAutoIncrementRestrictions()
	{
		if($this->autoIncFld === false)
			return false;

		if($this->dbType == self::DB_TYPE_MSSQL)
		{
			// for mssql, set IDENTITY_INSERT to Y, if needed
			$this->dbConnection->query('SET IDENTITY_INSERT '.$this->tableName.' ON');

			return true;
		}

		return false;
	}

	public function restoreAutoIncrementRestrictions()
	{
		if($this->autoIncFld === false)
			return false;

		// for mssql, set IDENTITY_INSERT to N, if needed

		if($this->dbType == self::DB_TYPE_MSSQL)
			$this->dbConnection->query('SET IDENTITY_INSERT '.$this->tableName.' OFF');
	}

	public function incrementSequence()
	{
		if($sqName = $this->checkSequenceExists())
			$this->dbConnection->query('select '.$sqName.'.NEXTVAL from dual');
	}

	protected function checkSequenceExists()
	{
		$this->sqName = false;
		if($this->dbType != self::DB_TYPE_ORACLE)
			return false;

		$sqName = $sequenceName = 'SQ_'.ToUpper($this->tableName);
		if(!($this->dbConnection->query("select * from USER_OBJECTS where OBJECT_TYPE = 'SEQUENCE' and OBJECT_NAME = '".$sequenceName."'", true)->fetch()))
			return false;

		$this->sqName = $sqName;

		return $this->sqName;
	}

	public function resetAutoIncrementFromIndex()
	{
		$this->resetAutoIncrement($this->getIndex() + 1);
	}

	// this function is used to adjust auto_increment value of a table to a certain position
	public function resetAutoIncrement($startIndex = 1)
	{
		$dbName = $this->dbConnection->getDbName();

		$startIndex = intval($startIndex);
		if($startIndex <= 0)
			throw new Main\SystemException('Start index should be greather, than zero');

		if($this->dbType == self::DB_TYPE_ORACLE)
		{
			if($sqName = $this->checkSequenceExists())
			{
				$this->dbConnection->query('drop sequence '.$sqName);
				$this->dbConnection->query('create sequence '.$sqName.' start with '.$startIndex.' increment by 1 NOMAXVALUE NOCYCLE NOCACHE NOORDER');
			}
		}
		elseif($this->dbType == self::DB_TYPE_MSSQL)
		{
			$this->dbConnection->query("DBCC CHECKIDENT('".$dbName.".dbo.".$this->tableName."', RESEED, ".($startIndex - 1).")");
		}
		elseif($this->dbType == self::DB_TYPE_MYSQL)
		{
			$this->dbConnection->query('alter table '.$this->tableName.' AUTO_INCREMENT = '.$startIndex);
		}
	}

	public function insert($row)
	{
		if(!is_array($row) || empty($row))
			return;

		$this->index++;
		$this->bufferSize++;

		if($this->autoIncFld !== false)
		{
			$row[$this->autoIncFld] = $this->index;
			$this->incrementSequence(); // if this is oracle and we insert auto increment key directly, we must provide sequence increment manually
		}

		$sql = $this->getRepeatedPart($row);

		/*
		MySQL & MsSQL: insert into b_test (F1,F2) values ('one','two'),('one1','two1'),('one2','two2')
		Oracle: insert all into b_test (F1,F2) values ('one','two') into b_test (F1,F2) values ('one1','two1') into b_test (F1,F2) values ('one2','two2')  select * from dual
		*/

		$nextBuffer = (empty($this->buffer) ? $this->insertHead : $this->buffer.$this->getRepeatedPartSeparator()).$sql;

		// here check length
		if(defined(SITE_CHARSET) && SITE_CHARSET == 'UTF-8')
			$len = mb_strlen($nextBuffer);
		else
			$len = strlen($nextBuffer);

		if(($this->mtu - (strlen($nextBuffer) + 100)) < self::RED_LINE)
		{
			$this->flush(); // flushing the previous buffer (now $this->buffer == '')
			$this->buffer = $this->insertHead.$sql;
		}
		else
			$this->buffer = $nextBuffer;

		return $this->index;
	}

	protected function getRepeatedPartSeparator()
	{
		return $this->dbType != self::DB_TYPE_ORACLE ? ',' : ' ';
	}

	protected function getRepeatedPart($row)
	{
		$sql = $this->prepareSql($row);

		if($this->dbType != self::DB_TYPE_ORACLE)
			return $sql;

		return 'into '.$this->tableName.' ('.implode(',', $this->map).') values '.$sql;
	}

	public function flush()
	{
		if(!strlen($this->buffer))
			return;

		//_dump_r('FLUSH! ('.$this->tableName.')  '.$this->bufferSize.' items with '.strlen($this->buffer).'chars (max '.$this->mtu.' chars)');

		if($this->dbType == self::DB_TYPE_ORACLE)
			$this->buffer .= ' '.$this->insertTail;

		$restrDropped = $this->dropAutoIncrementRestrictions();

		Main\HttpApplication::getConnection()->query($this->buffer);

		if($restrDropped)
			$this->restoreAutoIncrementRestrictions();

		$this->buffer = '';
		$this->bufferSize = 0;
	}

	protected function prepareSql($row)
	{
		if(!is_array($row) || empty($row))
			return '';

		$sql = array();
		foreach($this->fldVector as $fld => $none)
		{
			$val = $row[$fld];

			// only numeric and literal fields supported at the moment
			if($this->tableMap[$fld]['data_type'] == 'integer')
				$sql[] = intval($val);
			else
				$sql[] = "'".Main\HttpApplication::getConnection()->getSqlHelper()->forSql($val)."'";
		}

		return '('.implode(',', $sql).')';
	}

	public function getMaxTransferUnit()
	{
		$fail = false;

		if($this->dbType == self::DB_TYPE_MYSQL)
		{
			$res = $this->dbConnection->query('SHOW VARIABLES LIKE \'max_allowed_packet\'')->fetch();
			if(!($res['Variable_name'] == 'max_allowed_packet' && $mtu = intval($res['Value'])))
				$fail = self::DB_TYPE_MYSQL;

			return $mtu;
		}

		if($this->dbType == self::DB_TYPE_MSSQL)
		{
			$sao = $this->dbConnection->query('EXEC sp_configure \'show advanced option\'')->fetch();
			if($sao = ($sao['config_value'] == '0'))
				$this->dbConnection->query('EXEC sp_configure \'show advanced option\', \'1\'');

			$mtu = $this->dbConnection->query('EXEC sp_configure \'network packet size\'')->fetch();
			if(!($mtu = intval($mtu['config_value'])))
				$fail = self::DB_TYPE_MSSQL;

			if($sao)
				$this->dbConnection->query('EXEC sp_configure \'show advanced option\', \'0\'');

			return $mtu;
		}

		if($this->dbType == self::DB_TYPE_ORACLE)
			return PHP_INT_MAX; // actually unlimited?

		if($fail)
			throw new Main\SystemException('Cannot determine database mtu ('.$fail.')', 0, __FILE__, __LINE__);

		return 0;
	}
}