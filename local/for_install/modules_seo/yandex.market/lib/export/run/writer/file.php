<?php

namespace Yandex\Market\Export\Run\Writer;

use Bitrix\Main;
use Yandex\Market;

Main\Localization\Loc::loadMessages(__FILE__);

class File extends Base
{
	const BUFFER_LENGTH = 8192;

	protected $filePath;
	protected $fileResource;
	protected $tempResource;

	public function __construct(array $parameters = [])
	{
		parent::__construct($parameters);

		$this->filePath = $this->getParameter('filePath');
	}

	public function destroy()
	{
		$this->releaseFileResource();
		$this->releaseTempResource();
	}

	public function getPath()
	{
		return $this->filePath;
	}

	public function refresh()
	{
		$this->releaseFileResource();
	}

	public function lock($isBlocked = false)
	{
		$file = $this->getFileResource();
		$mode = null;

		if ($isBlocked)
		{
			$mode = LOCK_EX;
		}
		else
		{
			$mode = LOCK_EX | LOCK_NB;
		}

		return flock($file, $mode);
	}

	public function unlock()
	{
		$file = $this->getFileResource();

		return flock($file, LOCK_UN);
	}

	public function move($filePath)
	{
		$this->releaseFileResource();

		if (file_exists($filePath)) { unlink($filePath); }

		rename($this->filePath, $filePath);

		$this->filePath = $filePath;
	}

	public function copy($fromPath)
	{
		$this->releaseFileResource();

		if (file_exists($this->filePath)) { unlink($this->filePath); }

		copy($fromPath, $this->filePath);
	}

	public function remove()
	{
		$this->releaseFileResource();

		if (file_exists($this->filePath)) { unlink($this->filePath); }
	}

	public function writeRoot($element, $header = '')
	{
		$resource = $this->getFileResource();
		$contents = $header;
		$contents .= ($element instanceof \SimpleXMLElement ? $element->asXML() : $element);

		ftruncate($resource, 0);
		fseek($resource, 0);
		fwrite($resource, $contents);
	}

	public function writeTagList($elementList, $parentName)
	{
		$tagClosePosition = $this->getPosition('</' . $parentName . '>');

		if (isset($tagClosePosition))
		{
			$contents = '';

			foreach ($elementList as $element)
			{
				$contents .= $element instanceof \SimpleXMLElement ? $element->asXML() : $element;
			}

			$this->writeSplice($tagClosePosition, $tagClosePosition, $contents);
		}
	}

	public function writeTag($element, $parentName)
	{
		$this->writeTagList([ $element ], $parentName);
	}

	public function updateAttributeList($tagName, $elementAttributeList, $idAttr = 'id')
	{
		$searchList = [];

		foreach ($elementAttributeList as $id => $attributeList)
		{
			if ($idAttr)
			{
				$searchList[$id] = '<' . $tagName . ' ' . $idAttr . '="' . $id . '"';
			}
			else
			{
				$searchList[$id] = '<' . $tagName;
			}
		}

		if (!empty($searchList))
		{
			$positionList = $this->getPositionList($searchList);

			asort($positionList, SORT_NUMERIC);

			foreach ($positionList as $elementId => &$tagOpenPosition)
			{
				$tagEndPosition = $this->getPosition('>', $tagOpenPosition + 1, '<'); // stop next open tag

				if ($tagEndPosition !== null)
				{
					$originalTag = $this->read($tagOpenPosition, $tagEndPosition);
					$newTag = $originalTag;
					$attributeList = $elementAttributeList[$elementId];
					$newAttributes = '';

					foreach ($attributeList as $attributeName => $attributeValue)
					{
						$attributeString = ' ' . $attributeName . '="' . $attributeValue . '"';

						if (preg_match('/ ' . $attributeName . '=".*?"/', $newTag, $matches))
						{
							$newTag = str_replace($matches[0], $attributeString, $newTag);
						}
						else
						{
							$newAttributes .= $attributeString;
						}
					}

					if ($newAttributes !== '')
					{
						if (substr($newTag, -2) === ' /') // is self closed
						{
							$newTag .= substr($newTag, 0, -2) . $newAttributes . ' /';
						}
						else
						{
							$newTag .= $newAttributes;
						}
					}

					if ($originalTag !== $newTag)
					{
						$this->writeSplice($tagOpenPosition, $tagEndPosition, $newTag);
					}
				}
			}
		}
	}

	public function updateAttribute($tagName, $id, $attributeList, $idAttr = 'id')
	{
		$this->updateAttributeList($tagName, [ $id => $attributeList ], $idAttr);
	}

	public function updateTagList($tagName, $elementList, $idAttr = 'id')
	{
		$searchList = [];
		$result = [];

		foreach ($elementList as $id => $element)
		{
			$searchList[$id] = '<' . $tagName . ' ' . $idAttr . '="' . $id . '"';
		}

		if (!empty($searchList))
		{
			$positionList = $this->getPositionList($searchList);
			$selfClose = '/>';
			$selfCloseLength = Market\Export\Run\Helper\BinaryString::getLength($selfClose);
			$tagClose = '</' . $tagName . '>';
			$tagCloseLength = Market\Export\Run\Helper\BinaryString::getLength($tagClose);

			asort($positionList, SORT_NUMERIC);

			$positionElements = array_keys($positionList);

			foreach ($positionElements as $elementId)
			{
				$position = $positionList[$elementId];
				$closePosition = null;
				$selfClosePosition = $this->getPosition($selfClose, $position + 1, '<'); // stop next open tag

				if ($selfClosePosition !== null)
				{
					$closePosition = $selfClosePosition + $selfCloseLength;
				}
				else
				{
					$tagClosePosition = $this->getPosition($tagClose, $position + 1, '<' . $tagName . ' '); // stop on next tag same type

					if ($tagClosePosition !== null)
					{
						$closePosition = $tagClosePosition + $tagCloseLength;
					}
				}

				if ($closePosition !== null)
				{
					$result[$elementId] = true;
					$element = $elementList[$elementId];
					$newContents = $element instanceof \SimpleXMLElement ? $element->asXML() : $element;

					$diffLength = $this->writeSplice($position, $closePosition, $newContents);

					if ($diffLength !== 0)
					{
						foreach ($positionList as $nextElementId => $nextPosition)
						{
							if ($nextPosition > $position)
							{
								$positionList[$nextElementId] = $nextPosition + $diffLength;
							}
						}
					}
				}
			}
		}

		return $result;
	}

	public function updateTag($tagName, $id, $element, $idAttr = 'id')
	{
		$this->updateTagList($tagName, [ $id => $element ], $idAttr);
	}

	public function searchTagList($tagName, $idList, $idAttr = 'id')
	{
		$searchList = [];
		$result = [];

		foreach ($idList as $id)
		{
			$searchList[$id] = '<' . $tagName . ' ' . $idAttr . '="' . $id . '"';
		}

		if (!empty($searchList))
		{
			$positionList = $this->getPositionList($searchList);
			$selfClose = '/>';
			$selfCloseLength = Market\Export\Run\Helper\BinaryString::getLength($selfClose);
			$tagClose = '</' . $tagName . '>';
			$tagCloseLength = Market\Export\Run\Helper\BinaryString::getLength($tagClose);

			asort($positionList, SORT_NUMERIC);

			foreach ($positionList as $id => $position)
			{
				$closePosition = null;
				$selfClosePosition = $this->getPosition($selfClose, $position + 1, '<'); // stop next open tag

				if ($selfClosePosition !== null)
				{
					$closePosition = $selfClosePosition + $selfCloseLength;
				}
				else
				{
					$tagClosePosition = $this->getPosition($tagClose, $position + 1, '<' . $tagName); // stop on next tag same type

					if ($tagClosePosition !== null)
					{
						$closePosition = $tagClosePosition + $tagCloseLength;
					}
				}

				if ($closePosition !== null)
				{
					$result[$id] = $this->read($position, $closePosition);
				}
			}
		}

		return $result;
	}

	public function searchTag($tagName, $id, $idAttr = 'id')
	{
		$listResult = $this->searchTagList($tagName, [ $id ], $idAttr);

		return isset($listResult[$id]) ? $listResult[$id] : null;
	}

	protected function writeSplice($startPosition, $finishPosition, $contents = '')
	{
		$resource = $this->getFileResource();
		$tempResource = null;
		$contentsLength = Market\Export\Run\Helper\BinaryString::getLength($contents);
		$diffLength  = $contentsLength - ($finishPosition - $startPosition);

		if ($diffLength !== 0) // copy contents after finish to temp
		{
			$tempResource = $this->getTempResource();

			stream_copy_to_stream($resource, $tempResource, -1, $finishPosition);
		}

		if ($diffLength < 0) // hanging end
		{
			ftruncate($resource, $startPosition);
		}

		fseek($resource, $startPosition);

		if ($contentsLength > 0) // write contents
		{
			fwrite($resource, $contents);
		}

		if ($diffLength !== 0) // return contents after finish to initial resource
		{
			fseek($tempResource, 0);
			stream_copy_to_stream($tempResource, $resource);
		}

		return $diffLength;
	}

	protected function read($startPosition, $finishPosition)
	{
		$resource = $this->getFileResource();

		fseek($resource, $startPosition);

		return fread($resource, $finishPosition - $startPosition);
	}

	protected function getPosition($search, $startPosition = null, $stopSearch = null)
	{
		$searchList = [ 0 => $search ];
		$positionList = $this->getPositionList($searchList, $startPosition, $stopSearch);

		return isset($positionList[0]) ? $positionList[0] : null;
	}

	protected function getPositionList($searchList, $startPosition = null, $stopSearch = null)
	{
		$resource = $this->getFileResource();
		$isSupportReturnToStart = false;

		if (!isset($startPosition))
		{
			$isSupportReturnToStart = true;
			$startPosition = ftell($resource);
		}
		else
		{
			fseek($resource, $startPosition);
		}

		$currentPosition = $startPosition;
		$bufferPosition = $currentPosition;
		$buffer = '';
		$isEndOfFileReached = false;
		$searchCount = count($searchList);
		$foundCount = 0;
		$isAllFound = false;
		$result = [];

		do
		{
			$iterationBuffer = fread($resource, static::BUFFER_LENGTH);
			$buffer .= $iterationBuffer;

			foreach ($searchList as $searchKey => $searchVariant)
			{
				if (!isset($result[$searchKey]))
				{
					$variantPosition = Market\Export\Run\Helper\BinaryString::getPosition($buffer, $searchVariant);

					if ($variantPosition !== false)
					{
						$result[$searchKey] = $bufferPosition + $variantPosition;
						$foundCount++;

						$isAllFound = ($searchCount === $foundCount);
					}
				}
			}

			if ($stopSearch !== null)
			{
				$stopPosition = Market\Export\Run\Helper\BinaryString::getPosition($buffer, $stopSearch);

				if ($stopPosition !== false)
				{
					$stopPosition += $bufferPosition;

					foreach ($result as $searchKey => $position)
					{
						if ($position > $stopPosition)
						{
							unset($result[$searchKey]);
						}
					}

					break;
				}
			}

			if ($isAllFound)
			{
				break;
			}

			$buffer = $iterationBuffer;
			$bufferPosition = $currentPosition;
			$currentPosition += static::BUFFER_LENGTH;

			if (!$isEndOfFileReached && feof($resource))
			{
				if ($isSupportReturnToStart)
				{
					$isEndOfFileReached = true;
					$bufferPosition = 0;
					$currentPosition = 0;
					$buffer = '';

					fseek($resource, 0);
				}
				else
				{
					break;
				}
			}
		}
		while (!$isEndOfFileReached || $currentPosition < $startPosition);

		return $result;
	}

	/**
	 * @return resource
	 */
	protected function getFileResource()
	{
		if (!isset($this->fileResource))
		{
			CheckDirPath($this->filePath);

			if (!file_exists($this->filePath))
			{
				touch($this->filePath);
				chmod($this->filePath, BX_FILE_PERMISSIONS);
			}
			else if (!is_writable($this->filePath))
			{
				chmod($this->filePath, BX_FILE_PERMISSIONS);
			}

			$this->fileResource = fopen($this->filePath, 'rb+');

			if ($this->fileResource === false)
			{
				throw new Main\SystemException(Market\Config::getLang('EXPORT_RUN_WRITER_FILE_CANT_OPEN_FILE'));
			}
		}

		return $this->fileResource;
	}

	protected function releaseFileResource()
	{
		if (isset($this->fileResource))
		{
			fclose($this->fileResource);
			$this->fileResource = null;
		}
	}

	protected function releaseTempResource()
	{
		if (isset($this->tempResource))
		{
			fclose($this->tempResource);
			$this->tempResource = null;
		}
	}

	protected function getTempResource()
	{
		if (isset($this->tempResource))
		{
			ftruncate($this->tempResource, 0);
			fseek($this->tempResource, 0);
		}
		else
		{
			$this->tempResource = fopen('php://temp', 'rb+');

			if ($this->tempResource === false)
			{
				throw new Main\SystemException(Market\Config::getLang('EXPORT_RUN_WRITER_FILE_CANT_OPEN_TEMP'));
			}
		}

		return $this->tempResource;
	}
}