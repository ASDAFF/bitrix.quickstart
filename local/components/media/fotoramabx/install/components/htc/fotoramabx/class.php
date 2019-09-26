<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

class CFotoramaComponent extends CBitrixComponent
{
	/**
	 * Константы
	 */
	const SOURCE_TYPE_MEDIALIBRARY_COLLECTION = 'medialibrary_collection';
	const SOURCE_TYPE_IBLOCK_SECTION = 'iblock_section';
	const FULLSCREEN_MODE_DISABLED = 'false';
	const FULLSCREEN_MODE_NATIVE = 'native';
	const FULLSCREEN_MODE_ENABLED = 'true';
	const NAVIGATION_STYLE_DOTS = 'dots';
	const NAVIGATION_STYLE_THUMBS = 'thumbs';
	const NAVIGATION_STYLE_DISABLED = 'false';
	const NAVIGATION_POSITION_BOTTOM = 'bottom';
	const NAVIGATION_POSITION_TOP = 'top';
	const CACHE_TIME_DEFAULT = 3600;

	/**
	 * Получает все изображения коллекции медиабиблиотеки
	 * @param $medialibraryCollectionId
	 * @return array
	 */
	public function getImagesFromMedialibraryCollection($medialibraryCollectionId)
	{		
		$images = array();
		
		CMedialib::Init(); //Классы медиабиблиотеки недоступны до ее инициализации
		
		$items = CMedialibItem::GetList(array(
			'arCollections' => array(
				$medialibraryCollectionId,
			)
		));

		/**
		 * В CMedialibItem::GetList нет возможности фильтрации по типу элемента коллекции, 
		 * поэтому придется выбрать изображения вручную
		 */
		foreach ($items as $item)
		{
			if ($item['TYPE'] === 'image')
			{
				$image = array(
					'HEIGHT' => $item['HEIGHT'],
					'WIDTH' => $item['WIDTH'],
					'PATH' => $item['PATH'],
					'THUMB_PATH' => $item['THUMB_PATH'],
					'DESCRIPTION' => $item['DESCRIPTION'],
				);
				$images[] = $image;
			}
		}
		
		return $images;
	}

	/**
	 * Получает все изображения (анонса и детальные) элементов одного раздела инфоблока
	 * @param $sectionId
	 * @return array
	 */
	public function getImagesFromIblockSection($sectionId)
	{
		$images = array();
		
		$iblockElements = CIBlockElement::GetList(
			array(
				'SORT' => 'ASC',
				'ID' => 'ASC',
			),
			array(
				'SECTION_ID' => $sectionId,
				'ACTIVE' => 'Y',
				'>PREVIEW_PICTURE' => 0, //фильтруем по обязательному наличию изображения анонса
				'>DETAIL_PICTURE' => 0 //и по обязательному наличию детального изображения
			),
			false,
			false,
			array(
				'ID',
				'NAME',
				'PREVIEW_PICTURE',
				'DETAIL_PICTURE',
			)
		);

		while ($iblockElement = $iblockElements->GetNext())
		{
			$path = CFile::GetPath($iblockElement['DETAIL_PICTURE']); //CFile::GetByID не возвращает полного пути до изображения
			$thumbPath = CFile::GetPath($iblockElement['PREVIEW_PICTURE']);
			$detailPictureInfo = CFile::GetByID($iblockElement['DETAIL_PICTURE'])->Fetch();
			
			$image = array(
				'HEIGHT' => $detailPictureInfo['HEIGHT'],
				'WIDTH' => $detailPictureInfo['WIDTH'],
				'PATH' => $path,
				'THUMB_PATH' => $thumbPath,
				'DESCRIPTION' => $detailPictureInfo['DESCRIPTION'],
			);
			$images[] = $image;
		}
		
		return $images;
	}
}