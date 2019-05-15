<?php

class CVCSDriverItemFiles extends CVCSDriverItemAbstract {
	private $fullpath;
	private $source_hash;
	protected function Init() {
		$this->fullpath = $this->settings['doc_root'] . $this->GetID();
	}

	public function GetSourceHash() {
		if (empty($this->source_hash)) {
			$this->source_hash = md5_file($this->fullpath);
		}
		return $this->source_hash;
	}

	public function GetSource() {
		return file_get_contents($this->fullpath);
	}

	public function SetSource($source, $cdp = false) {
		if ($cdp) {
			CheckDirPath($this->fullpath);
		}
		return file_put_contents($this->fullpath, $source);
	}

	public function IsExists() {
		return file_exists($this->fullpath);
	}

	public function Delete() {
		return @unlink($this->fullpath);
	}

}
