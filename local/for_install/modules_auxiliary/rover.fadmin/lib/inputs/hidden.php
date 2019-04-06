<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 11.01.2016
 * Time: 17:37
 *
 * @author Pavel Shulaev (http://rover-it.me)
 */

namespace Rover\Fadmin\Inputs;

use Rover\Fadmin\Tab;
/**
 * Class Hidden
 *
 * @package Rover\Fadmin\Inputs
 * @author  Pavel Shulaev (http://rover-it.me)
 */
class Hidden extends Text
{
	/**
	 * @var string
	 */
	public static $type = self::TYPE__HIDDEN;

    /**
     * @param array $params
     * @param Tab   $tab
     * @throws \Bitrix\Main\ArgumentNullException
     */
    public function __construct(array $params, Tab $tab)
    {
        if (!isset($params['label']))
            $params['label'] = self::$type;

        parent::__construct($params, $tab);
    }
}