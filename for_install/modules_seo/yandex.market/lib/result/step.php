<?php

namespace Yandex\Market\Result;

class Step extends Base
{
	protected $offset;
	protected $progress = 0;
	protected $total = 0;
	protected $readyCount;
	protected $totalCount;

	public function setOffset($offset)
	{
		$this->offset = $offset;
	}

	public function getOffset()
	{
		return $this->offset;
	}

	public function setProgress($progress)
	{
		$this->progress = $progress;
	}

	public function increaseProgress($progress)
	{
		$this->progress += $progress;
	}

	public function getProgress()
	{
		return $this->progress;
	}

	public function setTotal($total)
	{
		$this->total = $total;
	}

	public function getTotal()
	{
		return $this->total;
	}

	public function getProgressRatio()
	{
		if ($this->readyCount !== null && $this->totalCount !== null)
		{
			$result = ($this->totalCount > 0 ? round($this->readyCount / $this->totalCount, 2) : 1);
		}
		else
		{
			$result = ($this->total > 0 ? round($this->progress / $this->total, 2) : 1);
		}

		return $result;
	}

	public function getProgressPercent()
	{
		return 100 * $this->getProgressRatio();
	}

	public function getReadyCount()
	{
		return $this->readyCount;
	}

	public function setReadyCount($count)
	{
		$this->readyCount = (int)$count;
	}

	public function getTotalCount()
	{
		return $this->totalCount;
	}

	public function setTotalCount($count)
	{
		$this->totalCount = (int)$count;
	}

	public function isFinished()
	{
		return $this->progress >= $this->total;
	}
}