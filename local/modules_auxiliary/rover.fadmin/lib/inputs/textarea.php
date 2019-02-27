<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 11.01.2016
 * Time: 17:41
 *
 * @author Pavel Shulaev (http://rover-it.me)
 */

namespace Rover\Fadmin\Inputs;

use Rover\Fadmin\Tab;
/**
 * Class Textarea
 *
 * @package Rover\Fadmin\Inputs
 * @author  Pavel Shulaev (http://rover-it.me)
 */
class Textarea extends Input
{
	/**
	 * @var string
	 */
	public static $type = self::TYPE__TEXTAREA;

	/**
	 * @var int
	 */
	protected $rows = 3;

	/**
	 * @var int
	 */
	protected $cols = 50;

	/**
	 * @param array $params
	 * @param Tab   $tab
	 * @throws \Bitrix\Main\ArgumentNullException
	 */
	public function __construct(array $params, Tab $tab)
	{
		parent::__construct($params, $tab);

		if (isset($params['rows']))
			$this->rows = htmlspecialcharsbx($params['rows']);

		if (isset($params['cols']))
			$this->cols = htmlspecialcharsbx($params['cols']);
	}

    /**
     * @return int
     */
    public function getRows()
    {
        return $this->rows;
    }

    /**
     * @param $rows
     * @return $this
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function setRows($rows)
    {
        $this->rows = $rows;

        return $this;
    }

    /**
     * @return int
     */
    public function getCols()
    {
        return $this->cols;
    }

    /**
     * @param $cols
     * @return $this
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function setCols($cols)
    {
        $this->cols = $cols;

        return $this;
    }
}