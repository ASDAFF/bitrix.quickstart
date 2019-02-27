<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 17.11.2016
 * Time: 18:01
 *
 * @author Pavel Shulaev (http://rover-it.me)
 */

namespace Rover\Fadmin\Options;

/**
 * Class Message
 *
 * @package Rover\Fadmin\Engine
 * @author  Pavel Shulaev (http://rover-it.me)
 */
class Message
{
    const TYPE__OK      = 'OK';
    const TYPE__ERROR   = 'ERROR';

	/**
	 * message storage
	 * @var array
	 */
	protected $messages = array();

    /**
     * @param        $message
     * @param string $type
     * @param bool   $html
     * @author Pavel Shulaev (http://rover-it.me)
     */
	public function add($message, $type = self::TYPE__OK, $html = true)
	{
	    if (is_array($message))
	        $message = implode("\n", $message);

	    if (!$html)
	        $message = htmlspecialcharsbx($message);

		$this->messages[] = array(
			'MESSAGE'   => trim($message),
            'HTML'      => (bool)$html,
			'TYPE'      => htmlspecialcharsbx($type),
        );
	}

    /**
     * @param      $message
     * @param bool $html
     * @author Pavel Shulaev (http://rover-it.me)
     */
	public function addOk($message, $html = false)
	{
		$this->add($message, self::TYPE__OK, $html);
	}

    /**
     * @param      $message
     * @param bool $html
     * @author Pavel Shulaev (http://rover-it.me)
     */
	public function addError($message, $html = false)
	{
		$this->add($message, self::TYPE__ERROR, $html);
	}

    /**
     * @return array
     * @author Pavel Shulaev (https://rover-it.me)
     */
	public function get()
    {
        return $this->messages;
    }
}