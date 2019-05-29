<?php

namespace Yandex\Market\Export\Setup;

use Bitrix\Main;
use Bitrix\Main\Localization\Loc;
use Yandex\Market;

Loc::loadMessages(__FILE__);

class Model extends Market\Reference\Storage\Model
{
	/** @var \Yandex\Market\Export\Xml\Format\Reference\Base */
	protected $format = null;

	public static function getDataClass()
	{
		return Table::getClassName();
	}

	public static function normalizeFileName($fileName, $primary = null)
	{
		$fileName = basename(trim($fileName), '.xml');

		if ($fileName === '' && !empty($primary))
		{
			$fileName = 'setup_' . $primary;
		}

		return ($fileName !== '' ? $fileName . '.xml' : null);
	}

	public function onBeforeSave()
	{
		if ($this->isAutoUpdate())
		{
			$this->handleChanges(false);
		}

		if ($this->hasFullRefresh())
		{
			$this->handleRefresh(false);
		}
	}

	public function onAfterSave()
	{
		if ($this->isAutoUpdate())
		{
			$this->handleChanges(true);
		}

		if ($this->hasFullRefresh())
		{
			$this->handleRefresh(true);
		}
	}

	public function handleChanges($direction)
	{
		if (!$direction || $this->isFileReady())
		{
			$iblockLinkCollection = $this->getIblockLinkCollection();

			foreach ($iblockLinkCollection as $iblockLink)
			{
				$iblockLink->handleChanges($direction);
			}
		}
	}

	public function handleRefresh($direction)
	{
		$interval = $this->getRefreshPeriod();

		$agentParams = [
			'method' => 'refreshStart',
			'arguments' => [ (int)$this->getId() ],
			'interval' => $interval,
			'next_exec' => ConvertTimeStamp(time() + $interval, 'FULL')
		];

		if ($direction)
		{
			if ($this->isFileReady())
			{
				Market\Export\Run\Agent::register($agentParams);
			}
		}
		else
		{
			Market\Export\Run\Agent::unregister($agentParams);
			Market\Export\Run\Agent::unregister([
				'method' => 'refresh',
				'arguments' => [ (int)$this->getId() ]
			]);

			Market\Export\Run\Agent::releaseState('refresh', (int)$this->getId());
		}
	}

	/**
	 * @return array
	 */
	public function getContext()
	{
		$format = $this->getFormat();
		$result = [
			'SETUP_ID' => $this->getId(),
			'EXPORT_SERVICE' => $this->getField('EXPORT_SERVICE'),
			'EXPORT_FORMAT' => $this->getField('EXPORT_FORMAT'),
			'EXPORT_FORMAT_TYPE' => $format->getType(),
			'ENABLE_AUTO_DISCOUNTS' => $this->isAutoDiscountsEnabled(),
			'DOMAIN_URL' => $this->getDomainUrl(),
			'USER_GROUPS' => [2], // support only public
			'HAS_CATALOG' => Main\ModuleManager::isModuleInstalled('catalog'),
			'HAS_SALE' => Main\ModuleManager::isModuleInstalled('sale'),
			'SHOP_DATA' => $this->getShopData()
		];

		// sales notes

		$salesNotes = $this->getSalesNotes();

		if (strlen($salesNotes) > 0)
		{
			$result['SALES_NOTES'] = $salesNotes;
		}

		// delivery options

		$deliveryOptions = $this->getDeliveryOptions();

		if (!empty($deliveryOptions))
		{
			$result['DELIVERY_OPTIONS'] = $deliveryOptions;
		}

		return $result;
	}

	public function getDeliveryOptions()
	{
		$deliveryCollection = $this->getDeliveryCollection();

		return $deliveryCollection->getDeliveryOptions();
	}

	public function getSalesNotes()
	{
		return trim($this->getField('SALES_NOTES'));
	}

	public function getShopData()
	{
		$fieldValue = $this->getField('SHOP_DATA');

		return is_array($fieldValue) ? $fieldValue : null;
	}

	public function getFormat()
	{
		if (!isset($this->format))
		{
			$this->format = $this->loadFormat();
		}

		return $this->format;
	}

	protected function loadFormat()
	{
		$service = $this->getField('EXPORT_SERVICE');
		$format = $this->getField('EXPORT_FORMAT');

		return Market\Export\Xml\Format\Manager::getEntity($service, $format);
	}

	public function getFileName()
	{
		return static::normalizeFileName($this->getField('FILE_NAME'), $this->getId());
	}

	public function getFileRelativePath()
	{
		return BX_ROOT . '/catalog_export/' . $this->getFileName();
	}

	public function getFileAbsolutePath()
	{
		$relativePath = $this->getFileRelativePath();

		return Main\IO\Path::convertRelativeToAbsolute($relativePath);
	}

	public function isFileReady()
	{
		$path = $this->getFileAbsolutePath();

		return Main\IO\File::isFileExists($path);
	}

	public function getFileUrl()
	{
		return $this->getDomainUrl() . $this->getFileRelativePath();
	}

	public function getDomainUrl()
	{
		return 'http' . ($this->isHttps() ? 's' : '') . '://' . $this->getDomain();
	}

	public function getDomain()
	{
		return $this->getField('DOMAIN');
	}

	public function isHttps()
	{
		return ($this->getField('HTTPS') === '1');
	}

	public function isAutoDiscountsEnabled()
	{
		return ($this->getField('ENABLE_AUTO_DISCOUNTS') === '1');
	}

	public function isAutoUpdate()
	{
		return ($this->getField('AUTOUPDATE') === '1');
	}

	public function hasFullRefresh()
	{
		return $this->getRefreshPeriod() !== null;
	}

	public function getRefreshPeriod()
	{
		$period = (int)$this->getField('REFRESH_PERIOD');
		$result = null;

		if ($period > 0)
		{
			$result = $period;
		}

		return $result;
	}

	/**
	 * @return \Yandex\Market\Export\IblockLink\Collection
	 */
	public function getIblockLinkCollection()
	{
		return $this->getChildCollection('IBLOCK_LINK');
	}

	/**
	 * @return \Yandex\Market\Export\Delivery\Collection
	 */
	public function getDeliveryCollection()
	{
		return $this->getChildCollection('DELIVERY');
	}

	protected function getChildCollectionReference($fieldKey)
	{
		$result = null;

		switch ($fieldKey)
		{
			case 'IBLOCK_LINK':
				$result = Market\Export\IblockLink\Collection::getClassName();
			break;

			case 'DELIVERY':
				$result = Market\Export\Delivery\Collection::getClassName();
			break;
		}

		return $result;
	}
}
