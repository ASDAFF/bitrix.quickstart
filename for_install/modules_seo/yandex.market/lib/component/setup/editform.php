<?php

namespace Yandex\Market\Component\Setup;

use Bitrix\Main;
use Yandex\Market;

Main\Localization\Loc::loadMessages(__FILE__);

class EditForm extends Market\Component\Model\EditForm
{
	public function modifyRequest($request)
	{
		$result = $request;
		$hasIblockRequest = isset($request['IBLOCK']);
		$hasIblockLinkRequest = isset($request['IBLOCK_LINK']);

		if ($hasIblockRequest || $hasIblockLinkRequest)
		{
			$iblockIds = $hasIblockRequest ? (array)$request['IBLOCK'] : [];
			$iblockIdsMap = array_flip($iblockIds);
			$usedIblockIds = [];
			$result['IBLOCK_LINK'] = $hasIblockLinkRequest ? (array)$request['IBLOCK_LINK'] : [];

			foreach ($result['IBLOCK_LINK'] as $iblockLinkKey => $iblockLink)
			{
				$iblockId = (int)$iblockLink['IBLOCK_ID'];

				if ($iblockId > 0 && isset($iblockIdsMap[$iblockId]))
				{
					$usedIblockIds[$iblockId] = true;
				}
				else
				{
					unset($result['IBLOCK_LINK'][$iblockLinkKey]);
				}
			}

			foreach ($iblockIds as $iblockId)
			{
				if (!isset($usedIblockIds[$iblockId]))
				{
					$result['IBLOCK_LINK'][] = [
						'IBLOCK_ID' => $iblockId
					];
				}
			}
		}

		return $result;
	}

	public function load($primary, array $select = [], $isCopy = false)
	{
		$result = parent::load($primary, $select, $isCopy);

		if ($isCopy)
		{
			$copyNameMarker = Market\Config::getLang('COMPONENT_SETUP_EDIT_FORM_COPY_NAME_MARKER');

			if (isset($result['NAME']) && stripos($result['NAME'], $copyNameMarker) === false)
			{
				$result['NAME'] .= ' ' . $copyNameMarker;
			}

			if (isset($result['FILE_NAME']))
			{
				$result['FILE_NAME'] = null;
			}
		}

		return $result;
	}

	public function validate($data, array $fields = null)
	{
		$result = parent::validate($data, $fields);

		$this->validateIblock($result, $data, $fields);
		$this->validateDelivery($result, $data, $fields);
		$this->validateFilterCondition($result, $data, $fields);

		return $result;
	}

	protected function validateIblock(Main\Entity\Result $result, $data, array $fields = null)
	{
		if (empty($data['IBLOCK_LINK']))
		{
			$result->addError(new Market\Error\EntityError(
				Market\Config::getLang('COMPONENT_SETUP_EDIT_FORM_ERROR_IBLOCK_EMPTY'),
				0,
				[ 'FIELD' => 'IBLOCK' ]
			));
		}
	}

	protected function validateDelivery(Main\Entity\Result $result, $data, array $fields = null)
	{
	    if (isset($fields['DELIVERY'])) // has delivery in validation list
        {
            $deliveryTypeList = [
                Market\Export\Delivery\Table::DELIVERY_TYPE_DELIVERY
            ];

            foreach ($deliveryTypeList as $deliveryType)
            {
                if (empty($data['DELIVERY']) || !$this->isValidDeliveryDataList($data['DELIVERY'], $deliveryType)) // and empty primary delivery
                {
                    $hasChildDeliveryOptions = false;

                    foreach ($data['IBLOCK_LINK'] as $iblockLink)
                    {
                        if (!empty($iblockLink['DELIVERY']) && $this->isValidDeliveryDataList($iblockLink['DELIVERY'], $deliveryType))
                        {
                            $hasChildDeliveryOptions = true;
                            break;
                        }
                        else if (!empty($iblockLink['FILTER']))
                        {
                            foreach ($iblockLink['FILTER'] as $filter)
                            {
                                if (!empty($filter['DELIVERY']) && $this->isValidDeliveryDataList($filter['DELIVERY'], $deliveryType))
                                {
                                    $hasChildDeliveryOptions = true;
                                    break 2;
                                }
                            }
                        }
                    }

                    if ($hasChildDeliveryOptions)
                    {
                        $result->addError(new Market\Error\EntityError(
                            Market\Config::getLang('COMPONENT_SETUP_EDIT_FORM_ERROR_CHILD_DELIVERY_OPTIONS_WITHOUT_ROOT'),
                            0,
                            [ 'FIELD' => 'DELIVERY' ]
                        ));
                        break;
                    }
                }
            }
		}
	}

	protected function validateFilterCondition(Main\Entity\Result $result, $data, array $fields = null)
	{
		if (!empty($data['IBLOCK_LINK']))
		{
			foreach ($data['IBLOCK_LINK'] as $iblockLinkIndex => $iblockLink)
			{
				$filterFieldName = 'IBLOCK_LINK_' . $iblockLinkIndex . '_FILTER';
				$filterInputName = 'IBLOCK_LINK[' . $iblockLinkIndex . '][FILTER]';

				if (isset($fields[$filterFieldName]) && !empty($iblockLink['FILTER']))
				{
					foreach ($iblockLink['FILTER'] as $filterIndex => $filter)
					{
						$hasValidCondition = false;

						if (!empty($filter['FILTER_CONDITION']))
						{
							foreach ($filter['FILTER_CONDITION'] as $filterCondition)
							{
								if (Market\Export\FilterCondition\Table::isValidData($filterCondition))
								{
									$hasValidCondition = true;
									break;
								}
							}
						}

						if (!$hasValidCondition)
						{
							$result->addError(new Market\Error\EntityError(
								Market\Config::getLang('COMPONENT_SETUP_EDIT_FORM_ERROR_FILTER_CONDITION_EMPTY'),
								0,
								[ 'FIELD' => $filterInputName ]
							));
							break 2;
						}
					}
				}
			}
		}
	}

	public function extend($data, array $select = [])
	{
		$result = $data;

		if (!isset($result['FILE_NAME']) || trim($result['FILE_NAME']) === '')
		{
			$result['FILE_NAME'] = 'export_' . randString(3) . '.xml';
		}

		if (!empty($result['IBLOCK_LINK']))
		{
			$setup = $this->loadSetupModel($data);

			foreach ($setup->getIblockLinkCollection() as $iblockLinkIndex => $iblockLink)
			{
				if (isset($result['IBLOCK_LINK'][$iblockLinkIndex]))
				{
					$result['IBLOCK_LINK'][$iblockLinkIndex]['CONTEXT'] = $iblockLink->getContext();
				}
			}
		}

		return $result;
	}

	public function processAjaxAction($action, $data)
	{
		$result = null;

		switch ($action)
		{
			case 'filterCount':
				session_write_close(); // release sessions

				$result = $this->ajaxActionFilterCount($data);
			break;

			default:
				$result = parent::processAjaxAction($action, $data);
			break;
		}

		return $result;
	}

	public function ajaxActionFilterCount($data)
	{
		$request = Main\Context::getCurrent()->getRequest();

		$setup = $this->loadSetupModel($data);
		$offset = null;
		$baseName = $request->getPost('baseName');

		if ($baseName !== null && preg_match('/^IBLOCK_LINK\[(\d+)\]\[FILTER\](?:\[(\d+)\])?/', $baseName, $baseNameMatches))
		{
			$offset = $baseNameMatches[1] . (isset($baseNameMatches[2]) ? ':' . $baseNameMatches[2] : '');
		}

		return [ 'status' => 'ok' ] + $this->getFilterCount($setup, $offset);
	}

	public function getFilterCount(Market\Export\Setup\Model $setup, $offset = null)
	{
		/** @var $offerStep Market\Export\Run\Steps\Offer */
		$processor = new Market\Export\Run\Processor($setup);
		$offerStep = Market\Export\Run\Manager::getStepProvider(
			Market\Export\Run\Manager::ENTITY_TYPE_OFFER,
			$processor
		);

		$filterCountList = $offerStep->getCount($offset, true);
		$iblockLinkIndex = 0;
		$result = [
			'countList' => [],
			'warningList' => []
		];

		foreach ($setup->getIblockLinkCollection() as $iblockLink)
		{
			$iblockLinkId = $iblockLink->getInternalId();
			$iblockId = $iblockLink->getIblockId();
			$filterIndex = 0;

			if ($iblockId > 0 && $filterCountList->hasCount($iblockLinkId))
			{
				$inputName = 'IBLOCK_LINK[' . $iblockLinkIndex . '][FILTER]';
				$warning = $filterCountList->getCountWarning($iblockLinkId);

				$result['countList'][$inputName] = $filterCountList->getCount($iblockLinkId);
				$result['warningList'][$inputName] = $warning ? $warning->getMessage() : null;
			}

			foreach ($iblockLink->getFilterCollection() as $filterModel)
			{
				$filterInternalId = $filterModel->getInternalId();
				$filterCountKey = $iblockLinkId . ':' . $filterInternalId;

				if ($filterCountList->hasCount($filterCountKey))
				{
					$inputName = 'IBLOCK_LINK[' . $iblockLinkIndex . '][FILTER][' . $filterIndex . '][FILTER_CONDITION]';
					$warning = $filterCountList->getCountWarning($filterCountKey);

					$result['countList'][$inputName] = $filterCountList->getCount($filterCountKey);
					$result['warningList'][$inputName] = $warning ? $warning->getMessage() : null;
				}

				$filterIndex++;
			}

			$iblockLinkIndex++;
		}

		return $result;
	}

	/**
	 * @param $data
	 *
	 * @return Market\Export\Setup\Model
	 */
	protected function loadSetupModel($data)
	{
		/** @var \Yandex\Market\Export\Setup\Model $modelClassName */
		$modelClassName = $this->getModelClass();

		return $modelClassName::initialize($data);
	}

	protected function isValidDeliveryDataList($dataList, $deliveryType)
	{
		$result = false;

		if (is_array($dataList))
		{
			foreach ($dataList as $data)
			{
			    $isMatchType = (isset($data['DELIVERY_TYPE']) && $data['DELIVERY_TYPE'] === $deliveryType);

				if ($isMatchType && Market\Export\Delivery\Table::isValidData($data))
				{
					$result = true;
					break;
				}
			}
		}

		return $result;
	}
}