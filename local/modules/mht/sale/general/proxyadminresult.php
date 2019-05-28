<?
class CSaleProxyAdminResult extends CAdminResult
{
	private $parameters = array();
	private $entityName = '';

	public function __construct($parameters, $entityName, $tableId)
	{
		$this->parameters = $parameters;
		$this->entityName = $entityName;
		parent::__construct(array(), $tableId);
	}

	function NavStart($nPageSize = 0, $bShowAll = true, $iNumPage = false)
	{
		$nSize = self::GetNavSize($this->table_id, $nPageSize);

		if(!is_array($nPageSize))
			$nPageSize = array();

		$nPageSize["nPageSize"] = $nSize;
		if($_REQUEST["mode"] == "excel")
			$nPageSize["NavShowAll"] = true;

		$this->nInitialSize = $nPageSize["nPageSize"];

		if(is_array($nPageSize))
			$this->InitNavStartVars($nPageSize);
		else
			$this->InitNavStartVars(intval($nPageSize), $bShowAll, $iNumPage);

		// force to db resource type, although we got empty array on input
		$en = $this->entityName;

		if($this->parameters['runtime'])
			$runtime = $this->parameters['runtime'];
		else
			$runtime = array();

		$count = $en::getList(array(
			'filter' => is_array($this->parameters['filter']) ? $this->parameters['filter'] : array(),
			'select' => array('CNT'),
			'runtime' => array_merge($runtime, array(
				'CNT' => array(
					'data_type' => 'integer',
					'expression' => array(
						'count(*)'
					)
				)
			))
		))->fetch();
		$this->NavRecordCount = $count['CNT'];

		// the following code was taken from DBNavStart()

		// here we could use Bitrix\Main\DB\Paginator

		//calculate total pages depend on rows count. start with 1
		$this->NavPageCount = floor($this->NavRecordCount/$this->NavPageSize);
		if($this->NavRecordCount % $this->NavPageSize > 0)
			$this->NavPageCount++;

		//page number to display. start with 1
		$this->NavPageNomer = ($this->PAGEN < 1 || $this->PAGEN > $this->NavPageCount? ($_SESSION[$this->SESS_PAGEN] < 1 || $_SESSION[$this->SESS_PAGEN] > $this->NavPageCount? 1:$_SESSION[$this->SESS_PAGEN]):$this->PAGEN);

		$parameters = $this->parameters;
		$parameters['limit'] = $this->NavPageSize;
		$parameters['offset'] = ($this->NavPageNomer - 1) * $this->NavPageSize;

		$res = $en::getList($parameters);
		$this->arResult = array();
		while($item = $res->Fetch())
			$this->arResult[] = $item;
	}
}