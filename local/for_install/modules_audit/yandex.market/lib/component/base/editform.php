<?php

namespace Yandex\Market\Component\Base;

use Bitrix\Main;

abstract class EditForm extends AbstractProvider
{
	/**
	 * @param $request
	 *
	 * @return array
	 */
	abstract public function modifyRequest($request);

	/**
	 * @param array $select
	 * @param array|null  $item
	 *
	 * @return array
	 */
	abstract public function getFields(array $select = [], $item = null);

	/**
	 * @param       $primary
	 * @param array $select
	 *
	 * @return array
	 */
	abstract public function load($primary, array $select = [], $isCopy = false);

	/**
	 * @param $data
	 * @param $select
	 *
	 * @return array
	 */
	abstract public function extend($data, array $select = []);

	/**
	 * @param $fields
	 *
	 * @return \Bitrix\Main\Entity\Result
	 */
	abstract public function validate($fields);

	/**
	 * @param $primary
	 * @param $fields
	 *
	 * @return \Bitrix\Main\Entity\AddResult
	 */
	abstract public function add($fields);

	/**
	 * @param $primary
	 * @param $fields
	 *
	 * @return \Bitrix\Main\Entity\UpdateResult
	 */
	abstract public function update($primary, $fields);
}