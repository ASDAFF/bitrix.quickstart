<?php

namespace Yandex\Market\Export\Run\Steps;

use Bitrix\Main;
use Yandex\Market;

class Root extends Base
{
	public function getName()
	{
		return 'root';
	}

	public function clear($isStrict = false)
	{
		parent::clear($isStrict);

		if ($isStrict)
		{
			$writer = $this->getWriter();

			$writer->lock(true);
			$writer->unlock();
			$writer->remove();
		}
	}

	public function run($action, $offset = null)
	{
		$result = new Market\Result\Step();

		$this->setRunAction($action);

		if ($action === 'full') // on full export reset file
		{
			$context = $this->getContext();
			$tagValuesList = [
				$this->createTagValue($context)
			];
			$elementList = [ [] ]; // one empty array

			$this->writeData($tagValuesList, $elementList, $context);
		}
		else if ($action === 'refresh')
		{
			$publicWriter = $this->getPublicWriter();

			if ($publicWriter)
			{
				$writer = $this->getWriter();

				$writer->copy($publicWriter->getPath());
				$publicWriter->refresh();
			}
		}

		return $result;
	}

	public function updateDate()
	{
		$tagName = $this->getTag()->getName();
		$date = new Main\Type\DateTime();
		$dateType = Market\Type\Manager::getType(Market\Type\Manager::TYPE_DATE);
		$writer = $this->getPublicWriter() ?: $this->getWriter();

		$writer->updateAttribute($tagName, 0, [ 'date' => $dateType->format($date) ], '');
	}

	protected function writeDataFile($tagResultList, $storageResultList)
	{
		$tagResult = reset($tagResultList);

		if ($tagResult->isSuccess())
		{
			$header = $this->getFormat()->getHeader();
			$xmlElement = $tagResult->getXmlContents();

			$this->getWriter()->writeRoot($xmlElement, $header);
		}
	}

	protected function getDataLogEntityType()
	{
		return Market\Logger\Table::ENTITY_TYPE_EXPORT_RUN_ROOT;
	}

	public function getFormatTag(Market\Export\Xml\Format\Reference\Base $format)
	{
		return $format->getRoot();
	}

	public function getFormatTagParentName(Market\Export\Xml\Format\Reference\Base $format)
	{
		return null;
	}

	protected function createTagValue($context)
	{
		$result = new Market\Result\XmlValue();

		if (isset($context['SHOP_DATA']['NAME']))
		{
			$shopName = trim($context['SHOP_DATA']['NAME']);

			if ($shopName !== '')
			{
				$result->addTag('name', $shopName);
			}
		}

		if (isset($context['SHOP_DATA']['COMPANY']))
		{
			$shopCompany = trim($context['SHOP_DATA']['COMPANY']);

			if ($shopCompany !== '')
			{
				$result->addTag('company', $shopCompany);
			}
		}

		return $result;
	}
}