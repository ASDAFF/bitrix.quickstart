<?

global $MESS;
IncludeModuleLangFile(str_replace("\\", "/", __FILE__));

class CSitemapStructure
{
	private $dirsScanInfo;

	function __construct($startDirectory)
	{
		CModule::IncludeModule('iblock');

		$this->dirsScanInfo = $this->readDirectory($startDirectory);
	}

	public function getSitemap($arParams, $level = 0, $dirsScanInfo = 0)
	{
		if ($dirsScanInfo == 0)
		{
			$dirsScanInfo = $this->dirsScanInfo;
		}

		$arSections = array();

		if ($arParams['HIDE_'.$dirsScanInfo['DATA']['HASH']] != 'Y')
		{
			$arSections[] = array(
				'NAME' => !empty($arParams['NAME_FOR_'.$dirsScanInfo['DATA']['HASH']]) ? $arParams['NAME_FOR_'.$dirsScanInfo['DATA']['HASH']] : $dirsScanInfo['DATA']['NAME'],
				'LINK' => str_replace($_SERVER['DOCUMENT_ROOT'], '', $dirsScanInfo['DATA']['PATH']).'/',
				'LEVEL' => $level
			);

			if (count($dirsScanInfo['CHILD']) > 0)
			{
				foreach ($dirsScanInfo['CHILD'] as $childDirInfo)
				{
					$arSections = array_merge($arSections, $this->getSitemap($arParams, $level + 1, $childDirInfo));
				}
			}

			if ($arParams['IBLOCK_FOR_'.$dirsScanInfo['DATA']['HASH']] > 0)
			{
				$arSections = array_merge(
					$arSections,
					$this->getIBlockData(
						$arParams['IBLOCK_FOR_'.$dirsScanInfo['DATA']['HASH']],
						$arParams['IBLOCK_SECTION_FOR_'.$dirsScanInfo['DATA']['HASH']],
						$arParams['IBLOCK_SECTION_DEPTH_'.$dirsScanInfo['DATA']['HASH']] > 0 ? $arParams['IBLOCK_SECTION_DEPTH_'.$dirsScanInfo['DATA']['HASH']] : 99,
						$level + 1
					)
				);
			}
		}

		return $arSections;
	}

	public function buildSectionSettings($currentComponentSettings, $arIBlocks, $dirsScanInfo = 0)
	{
		if ($dirsScanInfo == 0)
		{
			$dirsScanInfo = $this->dirsScanInfo;
		}

		$arSettings = $arGroups = array();
		$dirName = '"'.str_replace($_SERVER['DOCUMENT_ROOT'], '', $dirsScanInfo['DATA']['PATH']).'/"'.($dirsScanInfo['DATA']['NAME'] != '' ? ' ('.$dirsScanInfo['DATA']['NAME'].')' : '');

		$arSettings['HIDE_'.$dirsScanInfo['DATA']['HASH']] = array(
			'NAME' => GetMessage("NSANDREY_SITEMAP_SKRYTQ_RAZDEL").$dirName,
			'TYPE' => 'CHECKBOX',
			'DEFAULT' => 'N',
			'PARENT' => 'ACTIVE_SETTINGS',
			'REFRESH' => 'Y'
		);

		if ($currentComponentSettings['HIDE_'.$dirsScanInfo['DATA']['HASH']] != 'Y')
		{
			$arGroups['IBLOCK_SETTINGS_'.$dirsScanInfo['DATA']['HASH']] = array('NAME' => GetMessage("NSANDREY_SITEMAP_NASTROYKI_DLA_RAZDEL").$dirName);

			$arSettings['NAME_FOR_'.$dirsScanInfo['DATA']['HASH']] = array(
				'NAME' => GetMessage("NSANDREY_SITEMAP_NAZVANIE_DLA_RAZDELA").$dirName,
				'TYPE' => 'STRING',
				'DEFAULT' => $dirsScanInfo['DATA']['NAME'],
				'PARENT' => 'NAME_SETTINGS'
			);

			$arSettings['IBLOCK_FOR_'.$dirsScanInfo['DATA']['HASH']] = array(
				'NAME' => 'ID '.GetMessage("NSANDREY_SITEMAP_INFOBLOKA"),
				'TYPE' => 'LIST',
				'VALUES' => $arIBlocks,
				'PARENT' => 'IBLOCK_SETTINGS_'.$dirsScanInfo['DATA']['HASH']
			);

			$arSettings['IBLOCK_SECTION_FOR_'.$dirsScanInfo['DATA']['HASH']] = array(
				'NAME' => 'ID '.GetMessage("NSANDREY_SITEMAP_RAZDELA"),
				'TYPE' => 'STRING',
				'DEFAULT' => '0',
				'PARENT' => 'IBLOCK_SETTINGS_'.$dirsScanInfo['DATA']['HASH']
			);

			$arSettings['IBLOCK_SECTION_DEPTH_'.$dirsScanInfo['DATA']['HASH']] = array(
				'NAME' => GetMessage("NSANDREY_SITEMAP_GLUBINA"),
				'TYPE' => 'STRING',
				'DEFAULT' => '0',
				'PARENT' => 'IBLOCK_SETTINGS_'.$dirsScanInfo['DATA']['HASH']
			);

			if (count($dirsScanInfo['CHILD']) > 0)
			{
				foreach ($dirsScanInfo['CHILD'] as $childDirInfo)
				{
					$settings = $this->buildSectionSettings($currentComponentSettings, $arIBlocks, $childDirInfo);

					$arGroups = array_merge($arGroups, $settings['GROUPS']);
					$arSettings = array_merge($arSettings, $settings['PARAMETERS']);
				}
			}
		}

		return array(
			'GROUPS' => $arGroups,
			'PARAMETERS' => $arSettings
		);
	}

	private function readDirectory($dir)
	{
		$dirs = array();
		$excludeDirs = array('.', '..', 'bitrix', 'upload', 'local');
		$dirHandler = opendir($dir);

		$thisDir = array(
			'PATH' => $dir,
			'HASH' => md5($dir)
		);

		while (($filename = readdir($dirHandler)) !== false)
		{
			if ($filename == '.section.php')
			{
				$sSectionName = '';
				include $dir.'/'.$filename;

				$thisDir['NAME'] = $sSectionName;
			}
			else if (is_dir($dir.'/'.$filename) && $filename[0] !== '.' && !in_array($filename, $excludeDirs))
			{
				$dirs['CHILD'][] = $this->readDirectory($dir.'/'.$filename);
			}
		}

		$dirs['DATA'] = $thisDir;

		return $dirs;
	}

	private function getIBlockData($iblockID, $iblockSection, $depth, $baseLevel)
	{
		$arSections = array();

		if ($depth > 0)
		{
			$sections = CIBlockSection::GetList(
				array('NAME' => 'ASC'),
				array('IBLOCK_ID' => $iblockID, 'SECTION_ID' => $iblockSection, 'ACTIVE' => 'Y')
			);

			while ($section = $sections->GetNext())
			{
				$arSections[] = array(
					'NAME' => $section['NAME'],
					'LINK' => $section['SECTION_PAGE_URL'],
					'LEVEL' => $baseLevel
				);

				$arSections = array_merge(
					$arSections,
					$this->getIBlockData(
						$iblockID,
						$section['ID'],
						$depth - 1,
						$baseLevel + 1
					)
				);
			}
		}

		$elements = CIBlockElement::GetList(
			array('NAME' => 'ASC'),
			array('IBLOCK_ID' => $iblockID, 'SECTION_ID' => $iblockSection, 'ACTIVE' => 'Y'),
			false,
			false,
			array('NAME', 'DETAIL_PAGE_URL')
		);

		while ($element = $elements->GetNext())
		{
			$arSections[] = array(
				'NAME' => $element['NAME'],
				'LINK' => $element['DETAIL_PAGE_URL'],
				'LEVEL' => $baseLevel
			);
		}


		return $arSections;
	}
} 