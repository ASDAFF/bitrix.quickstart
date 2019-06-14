<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 23.10.2017
 * Time: 8:37
 *
 * @author Pavel Shulaev (https://rover-it.me)
 */

namespace Rover\Fadmin;

use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\SystemException;
use Rover\Fadmin\Options;
/**
 * Class Preset
 *
 * @package Rover\Fadmin
 * @author  Pavel Shulaev (https://rover-it.me)
 */
class Preset
{

    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var Options
     */
    protected $options;

    /**
     * @var array
     */
    protected static $instances = array();

    /**
     * Preset constructor.
     *
     * @param         $id
     * @param Options $options
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     */
    private function __construct($id, Options $options)
    {
        $id = intval($id);
        if (!$id)
            throw new ArgumentNullException('id');

        $preset = $options->preset->getById($id);
        if (!$preset)
            throw new ArgumentOutOfRangeException('id');

        $this->id       = $id;
        $this->name     = $preset['name'];
        $this->options  = $options;
    }

    /**
     * @param                       $id
     * @param \Rover\Fadmin\Options $options
     * @param bool                  $reload
     * @return mixed
     * @throws ArgumentNullException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public static function getInstance($id, Options $options, $reload = false)
    {
        $id = intval($id);
        if (!$id)
            throw new ArgumentNullException('id');

        if (!isset(self::$instances[$id]) || $reload)
            self::$instances[$id] = new static($id, $options);

        return self::$instances[$id];
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws SystemException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function __call($name, $arguments)
    {
        if (0 !== strpos($name, 'get'))
            throw new SystemException('unacceptable method name');

        $name   = substr($name, 3);

        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $name, $matches);
        $ret    = $matches[0];
        foreach ($ret as &$match)
            $match = strtoupper($match);

        $constName = 'Options::OPTION__PRESET_' . implode('_', $ret);

        if (!defined($constName))
            throw new SystemException('preset option "' . $constName . '" not found');

        return $this->options->getPresetValue(constant($constName), $arguments[0], $arguments[1], $arguments[2]);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return \Rover\Fadmin\Options
     */
    public function getOptions()
    {
        return $this->options;
    }
}