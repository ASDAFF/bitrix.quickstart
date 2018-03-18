<?php

class CVCSDriverIteratorFiles extends CVCSDriverIteratorAbstract
{
	private $cur_num = 0;

	private $dirs = array('');

	private $arIncluded;
	private $arExcluded;

	private $extensions_patt;

	protected function Init() {
		if (empty($this->settings['doc_root']) || !file_exists($this->settings['doc_root']) || !is_dir($this->settings['doc_root'])) {
			$this->settings['doc_root'] = $_SERVER['DOCUMENT_ROOT'];
		}

		$this->arIncluded = (array) $this->settings['included_dirs'];
		$this->arExcluded = (array) $this->settings['excluded_dirs'];

		//$this->extensions = array_flip((array) $this->settings['extensions']);
		$exts = (array) $this->settings['extensions'];
		$exts_q = array();
		foreach ($exts as $e) {
			$exts_q[] = preg_quote($e, '#');
		}
		$this->extensions_patt = '#('.implode('|', $exts_q).')$#'.BX_UTF_PCRE_MODIFIER;
		//foreach ()
	}

	public function SetLastItemOrigID($last_item_orig_id) {
		if (false !== ($i = array_search($last_item_orig_id, $this->items))) {
			$this->cur_num = $i;
			$this->GetNextItem();
		}
	}

	public function GetNextItem() {
		if (array_key_exists($this->cur_num, $this->items)) {
			$c = $this->cur_num++;

			return new CVCSDriverItemFiles($this->GetDriverCode(), $this->items[$c], $this->settings);
		}

		return false;
	}

	private function inIncludePath($dir_entry) {
		if (empty($this->arIncluded)) {
			return true;
		}
		foreach ($this->arIncluded as $e) {
			$e = rtrim($e, '/');
			if($e == substr($dir_entry, 0, strlen($e))) {
				return true;
			}
		}
		return false;
	}

	private function inExcludePath($dir_entry) {
		foreach ($this->arExcluded as $e) {
			if($e == substr($dir_entry, 0, strlen($e))) {
				return true;
			}
		}
		return false;
	}

	private function checkExtension($entry) {
		return preg_match($this->extensions_patt, $entry);
	}

	protected function collect() {
		while (null !== ($dir = array_pop($this->dirs))) {
			$dir_fullpath = $this->settings['doc_root'] . $dir;
			if ($handle = opendir($dir_fullpath)) {
				while (false !== ($entry = readdir($handle))) {
					if ('.' == $entry || '..' == $entry) {
						continue;
					}

					$bIsDir = is_dir($dir_fullpath . DIRECTORY_SEPARATOR . $entry);
					$dir_entry = $dir . '/' . $entry;

					if ( !$this->inIncludePath($dir_entry) || $this->inExcludePath($dir_entry) || (!$bIsDir && !$this->checkExtension($dir_entry)) ) {
						continue;
					}
					if ($bIsDir) {
						array_push($this->dirs, $dir_entry);
					} else {
						array_push($this->items, $dir_entry);
					}
				}
				closedir($handle);
			}
		}
	}

	public function GetItemsCount() {
		return count($this->items);
	}

	public function GetCurPosition() {
		return $this->cur_num;
	}
}
