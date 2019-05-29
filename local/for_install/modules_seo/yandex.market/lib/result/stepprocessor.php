<?php

namespace Yandex\Market\Result;

class StepProcessor extends Step
{
	protected $step;
	protected $stepOffset;
	protected $stepReadyCount;
	protected $stepTotalCount;

	public function setStep($step)
	{
		$this->step = $step;
	}

	public function getStep()
	{
		return $this->step;
	}

	public function setStepOffset($offset)
	{
		$this->stepOffset = $offset;
	}

	public function getStepOffset()
	{
		return $this->stepOffset;
	}

	public function getStepReadyCount()
	{
		return $this->stepReadyCount;
	}

	public function setStepReadyCount($count)
	{
		$this->stepReadyCount = (int)$count;
	}

	public function getStepTotalCount()
	{
		return $this->stepTotalCount;
	}

	public function setStepTotalCount($count)
	{
		$this->stepTotalCount = (int)$count;
	}
}