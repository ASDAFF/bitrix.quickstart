<?
abstract class DSocialIterator implements Iterator
{	
	private $iCurrentPos = 0;
	protected $array = array();
	  
	public function rewind()
	{
		$this->iCurrentPos = 0;
	}
	public function current()
	{
		return $this->array[$this->iCurrentPos];
	}
	public function key()
	{
		return $this->iCurrentPos;
	}
	public function next()
	{
		++$this->iCurrentPos;
	}
	public function valid()
	{
		return isset($this->array[$this->iCurrentPos]);
	}
	public function add($ob)
	{
		$this->array[] = $ob;
	}
}

if (!function_exists("strrpos_ex")) {
	function strrpos_ex($haystack, $needle, $offset=0) {      
		// bug in PHP 5.1 with strrpos offset
		$pos_rule = ($offset<0)?strlen($haystack)+($offset-1):$offset;
		$last_pos = false; $first_run = true;
		do {
			$pos=strpos($haystack, $needle, (intval($last_pos)+(($first_run)?0:strlen($needle))));
			if ($pos!==false && (($offset<0 && $pos <= $pos_rule)||$offset >= 0)) {
				$last_pos = $pos;
			} else { break; }
			$first_run = false;
		} while ($pos !== false);
		if ($offset>0 && $last_pos<$pos_rule) { $last_pos = false; }
		return $last_pos;
	}
}

?>