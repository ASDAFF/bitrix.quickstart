<?php

class CVCSTimer {
	private $started = false;
	private $seconds;
	private $start_time;
	private $end_time;

	public function __construct($seconds = false) {
		if (false !== $seconds) {
			$this->StartTimer($seconds);
		}
	}

	/**
	 * @param double $seconds
	 */
	public function StartTimer($seconds = 30) {
		$this->seconds = doubleval($seconds);
		$this->start_time = microtime(true);
		$this->end_time = $this->start_time + $this->seconds;
		$this->started = true;
	}

	/**
	 * @return bool
	 */
	public function TimeExists() {
		return $this->started && (microtime(true) >= $this->end_time);
	}

	/**
	 * @return double
	 */
	public function GetWorkTime() {
		return microtime(true) - $this->start_time;
	}
}