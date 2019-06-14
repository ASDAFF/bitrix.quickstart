<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 11.01.2016
 * Time: 18:06
 *
 * @author Pavel Shulaev (http://rover-it.me)
 */

namespace Rover\Fadmin\Inputs;

use Bitrix\Main\Application;
use Rover\Fadmin\Tab;
use Bitrix\Main\Event;
use Bitrix\Main\EventResult;

/**
 * Class File
 *
 * @package Rover\Fadmin\Inputs
 * @author  Pavel Shulaev (http://rover-it.me)
 */
class File extends Input
{
	/**
	 * @var string
	 */
	public static $type = self::TYPE__FILE;

	/**
	 * @var bool
	 */
	protected $isImage = true;

	/**
	 * @var string
	 */
	protected $mimeType;

	/**
	 * @var int
	 */
	protected $maxSize = 0;

    /**
     * @var
     */
    protected $size;
	/**
	 * @param array $params
	 * @param Tab   $tab
	 * @throws \Bitrix\Main\ArgumentNullException
	 */
	public function __construct(array $params, Tab $tab)
	{
		parent::__construct($params, $tab);

		if (isset($params['isImage']))
			$this->isImage  = (bool)$params['isImage'];

		if (isset($params['maxSize']))
			$this->maxSize  = htmlspecialcharsbx($params['maxSize']);

		if (isset($params['mimeType']))
			$this->mimeType = htmlspecialcharsbx($params['mimeType']);

		if (isset($params['size']) && intval($params['size']))
			$this->size = intval(htmlspecialcharsbx($params['size']));
		else
		    $this->size = 20;

		// add events
		$this->addEventHandler(self::EVENT__BEFORE_SAVE_VALUE, array($this,  'beforeSaveValue'));
		$this->addEventHandler(self::EVENT__BEFORE_SAVE_REQUEST, array($this, 'beforeSaveRequest'));
	}

	public function isImage()
    {
        return $this->isImage;
    }

    /**
     * @return string
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * @param string $mimeType
     */
    public function setMimeType($mimeType)
    {
        $this->mimeType = $mimeType;
    }

    /**
     * @return int
     */
    public function getMaxSize()
    {
        return $this->maxSize;
    }

    /**
     * @param int $maxSize
     */
    public function setMaxSize($maxSize)
    {
        $this->maxSize = $maxSize;
    }

	/**
	 * @param Event $event
	 * @return EventResult|bool|int|null|string
	 * @throws \Bitrix\Main\ArgumentException
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public function beforeSaveRequest(Event $event)
	{
		if ($event->getSender() !== $this)
			return $this->getEvent()->getErrorResult($this);

		$request = Application::getInstance()
			->getContext()
			->getRequest();

		$value = null;

		$valueId = $this->getValueId();

		if (!empty($_FILES[$valueId]) && $_FILES[$valueId]['error'] == 0){

			// mime type of file checking
			if (!empty($this->mimeType) && $_FILES[$valueId]['type'] != $this->mimeType)
				throw new \Bitrix\Main\ArgumentException('incorrect file mime type');

			$value = \CFile::SaveFile($_FILES[$valueId], $this->tab->getModuleId());

		} elseif ($request->get($valueId . '_del') == 'Y') {
			// del old value
			\CFile::Delete($this->getValue(true));
			$value = false;
		}

		return $this->getEvent()->getSuccessResult($this, compact('value'));
	}

	/**
	 * not save
	 * @param Event $event
	 * @return EventResult
	 * @author Pavel Shulaev (http://rover-it.me)
	 */
	public function beforeSaveValue(Event $event)
	{
		if ($event->getSender() !== $this)
			return $this->getEvent()->getErrorResult($this);

		$value = $event->getParameter('value');

		// file is the same, do not save
		if ($value === null)
			return $this->getEvent()->getErrorResult($this);

		$oldValue = $this->getValue(true);

		if ($value != $oldValue)
			\CFile::Delete($oldValue);

		return $this->getEvent()->getSuccessResult($this, array('value' => $value));
	}

    /**
     * @return int
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param $size
     * @return $this
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function setSize($size)
    {
        $this->size = intval($size);

        return $this;
    }
}