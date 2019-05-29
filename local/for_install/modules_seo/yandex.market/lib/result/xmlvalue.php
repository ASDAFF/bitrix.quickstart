<?php

namespace Yandex\Market\Result;

class XmlValue extends Base
{
	protected $tagData = [];
	protected $multipleTags = [];

	/**
	 * Данные тега
	 *
	 * @internal
	 * @return array
	 */
	public function getTagData()
	{
		return $this->tagData;
	}

	/**
	 * Выгружен ли тег с идентичными значениями
	 *
	 * @param       $tagName
	 * @param       $value
	 * @param array $attributeList
	 *
	 * @return bool
	 */
	public function hasTag($tagName, $value, array $attributeList = [])
	{
		$result = false;

		if (!isset($this->tagData[$tagName]))
		{
			// nothing
		}
		else if (!isset($this->multipleTags[$tagName])) // not is multiple
		{
			$tag = $this->tagData[$tagName];

			$result = (
				$tag['VALUE'] === $value
				&& $tag['ATTRIBUTES'] == $attributeList
			);
		}
		else
		{
			foreach ($this->tagData[$tagName] as $tagData)
			{
				if ($tag['VALUE'] === $value && $tag['ATTRIBUTES'] == $attributeList)
				{
					$result = true;
					break;
				}
			}
		}

		return $result;
	}

	/**
	 * Добавить тег.
	 *
	 * @param       $tagName
	 * @param mixed $value
	 * @param array $attributeList Ассоциативный массив, где ключ массива - название атрибута, значение массива - значение атрибута.
	 * @param array|null $tagSettings дополнительные настройки для генерации тега
	 */
	public function addTag($tagName, $value, array $attributeList = [], $tagSettings = null)
	{
		$tag = [
			'VALUE' => $value,
			'ATTRIBUTES' => $attributeList,
			'SETTINGS' => $tagSettings
		];

		if (!isset($this->tagData[$tagName]))
		{
			$this->tagData[$tagName] = $tag;
		}
		else
		{
			if (!isset($this->multipleTags[$tagName]))
			{
				$this->multipleTags[$tagName] = true;
				$this->tagData[$tagName] = [ $this->tagData[$tagName] ];
			}

			$this->tagData[$tagName][] = $tag;
		}
	}

	/**
	 * Удалить тег
	 *
	 * @param       $tagName
	 * @param mixed $value Фильтр по значению тега
	 * @param array $attributeList Фильтр по атрибутам тега
	 */
	public function removeTag($tagName, $value = null, array $attributeList = [])
	{
		if (!isset($this->tagData[$tagName]))
		{
			// nothing
		}
		else
		{
			$tagList = (
				isset($this->multipleTags[$tagName])
					? $this->tagData[$tagName]
					: [ $this->tagData[$tagName] ]
			);
			$newTagList = [];

			foreach ($tagList as $tagKey => $tag)
			{
				$isMatch = true;

				if ($value !== null && $tag['VALUE'] !== $value)
				{
					$isMatch = false;
				}
				else
				{
					foreach ($attributeList as $attributeName => $attributeValue)
					{
						$tagAttributeValue = (
							isset($tag['ATTRIBUTES'][$attributeName])
								? $tag['ATTRIBUTES'][$attributeName]
								: null
						);

						if ($attributeValue !== $tagAttributeValue)
						{
							$isMatch = false;
							break;
						}
					}
				}

				if ($isMatch)
				{
					unset($tagList[$tagKey]);
				}
			}

			$tagCount = count($tagList);

			if ($tagCount === 0)
			{
				if (isset($this->multipleTags[$tagName]))
				{
					unset($this->multipleTags[$tagName]);
				}

				unset($this->tagData[$tagName]);
			}
			else if ($tagCount === 1)
			{
				if (isset($this->multipleTags[$tagName]))
				{
					unset($this->multipleTags[$tagName]);
				}

				$this->tagData[$tagName] = reset($tagList);
			}
			else
			{
				$this->multipleTags[$tagName] = true;
				$this->tagData[$tagName] = $tagList;
			}
		}
	}

	/**
	 * Получить значение тега
	 *
	 * @param string    $tagName        Имя тега
	 * @param bool      $isMultiple     Является ли значение атрибута множественным
	 *
	 * @return mixed
	 */
	public function getTagValue($tagName, $isMultiple = false)
	{
		$result = $isMultiple ? [] : null;

		if (isset($this->tagData[$tagName]))
		{
			$tagList = (
				isset($this->multipleTags[$tagName])
					? $this->tagData[$tagName]
					: [ $this->tagData[$tagName] ]
			);

			foreach ($tagList as $tag)
			{
				if ($isMultiple)
				{
					$result[] = $tag['VALUE'];
				}
				else
				{
					$result = $tag['VALUE'];
					break;
				}
			}
		}

		return $result;
	}

	/**
	 * Получить значение атрибута
	 *
	 * @param string    $tagName        Имя тега
	 * @param string    $attributeName  Имя атрибута
	 * @param bool      $isMultiple     Является ли значение атрибута множественным
	 *
	 * @return mixed
	 */
	public function getTagAttribute($tagName, $attributeName, $isMultiple = false)
	{
		$result = $isMultiple ? [] : null;

		if (isset($this->tagData[$tagName]))
		{
			$tagList = (
				isset($this->multipleTags[$tagName])
					? $this->tagData[$tagName]
					: [ $this->tagData[$tagName] ]
			);

			foreach ($tagList as $tag)
			{
				$attributeValue = (
					isset($tag['ATTRIBUTES'][$attributeName])
						? $tag['ATTRIBUTES'][$attributeName]
						: null
				);

				if ($isMultiple)
				{
					$result[] = $attributeValue;
				}
				else
				{
					$result = $attributeValue;
					break;
				}
			}
		}

		return $result;
	}

	/**
	 * Установить значение тега
	 *
	 * @param string    $tagName    Имя тега
	 * @param mixed     $value      Значение тега
	 * @param bool      $isMultiple Является ли значение тега множественным
	 */
	public function setTagValue($tagName, $value, $isMultiple = false)
	{
		if (!isset($this->tagData[$tagName]))
		{
			// nothing
		}
		else if (isset($this->multipleTags[$tagName]))
		{
			$tagIndex = 0;

			foreach ($this->tagData[$tagName] as &$tag)
			{
				$tagValue = null;

				if ($isMultiple)
				{
					$tagValue = isset($value[$tagIndex]) ? $value[$tagIndex] : null;
				}
				else
				{
					$tagValue = $value;
				}

				$tag['VALUE'] = $tagValue;

				$tagIndex++;
			}
			unset($tag);
		}
		else
		{
			$tagValue = null;

			if ($isMultiple)
			{
				$tagValue = is_array($value) ? reset($value) : null;
			}
			else
			{
				$tagValue = $value;
			}

			$this->tagData[$tagName]['VALUE'] = $tagValue;
		}
	}

	/**
	 * Установить атрибут тега
	 *
	 * @param string    $tagName        Имя тега
	 * @param string    $attributeName  Имя атрибута
	 * @param mixed     $value          Значение атрибута
	 * @param bool      $isMultiple     Является ли значение атрибута множественным
	 */
	public function setTagAttribute($tagName, $attributeName, $value, $isMultiple = false)
	{
		if (!isset($this->tagData[$tagName]))
		{
			// nothing
		}
		else if (isset($this->multipleTags[$tagName]))
		{
			$tagIndex = 0;

			foreach ($this->tagData[$tagName] as &$tag)
			{
				$attributeValue = null;

				if ($isMultiple)
				{
					$attributeValue = isset($value[$tagIndex]) ? $value[$tagIndex] : null;
				}
				else
				{
					$attributeValue = $value;
				}

				$tag['ATTRIBUTES'][$attributeName] = $attributeValue;

				$tagIndex++;
			}
			unset($tag);
		}
		else
		{
			$attributeValue = null;

			if ($isMultiple)
			{
				$attributeValue = is_array($value) ? reset($value) : null;
			}
			else
			{
				$attributeValue = $value;
			}

			$this->tagData[$tagName]['ATTRIBUTES'][$attributeName] = $attributeValue;
		}
	}
}