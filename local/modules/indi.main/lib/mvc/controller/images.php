<?php
/**
 * Individ module
 *
 * @category       Individ
 * @package        MVC
 * @link           http://individ.ru
 * @revision    $Revision$
 * @date        $Date$
 */

namespace Indi\Main\Mvc\Controller;

use Indi\Main as Main;
use Indi\Main\Mvc as Mvc;

/**
 * Контроллер для работы с изображениями
 *
 * @category       Individ
 * @package        MVC
 */
class Images extends Prototype
{
	/**
	 * Сохранение загруженного изображения
	 *
	 * @return array
	 */
	public function saveUploadImageAction()
	{
		$result = false;
		$arIMAGE = $_FILES["file"];//сам файл
		$folder = htmlspecialchars($_REQUEST["folder"]);//директория, в которую будет сохранен файл, отностельно upload
		if (\CFile::IsImage($arIMAGE["name"])) {
			$fid = \CFile::SaveFile($arIMAGE, $folder);
			$result = array(
				"image" => \CFile::GetPath($fid),
				"id" => $fid,
			);
		}
		return json_encode($result);
	}

	/**
	 * Удаление изображения
	 *
	 * @return array
	 */
	public function deleteImageAction()
	{
		//Удаляем файл
		$res = \CFile::Delete(htmlspecialchars($_REQUEST["imageid"]));

		return $res;

	}

	/**
	 * Обрезка изображений с помощью cropper
	 *
	 * @return array
	 */
	public function cropImageAction()
	{
		$result = false;
		$oldImageId = htmlspecialchars($_REQUEST["imgid"]); //ID сторого файла
		$img = $_SERVER["DOCUMENT_ROOT"] . $_REQUEST["img"]; //Путь к картинке
		$width = round(htmlspecialchars($_REQUEST["width"])); //Длина для обрезки
		$height = round(htmlspecialchars($_REQUEST["height"])); //Ширина для обрезки
		$x = htmlspecialchars($_REQUEST["x"]); //Отступ слева
		$y = htmlspecialchars($_REQUEST["y"]); //Отступ сверху
		//Обрезаем файл
		$image = new \Imagick($img);
		$image->cropImage($width, $height, $x, $y);

		//Сохраняем новый файл
		$image->writeImage($img);
		if ($image) {
			$result = $_REQUEST["img"];
		}
		$newFileId = \CFile::CopyFile($oldImageId);
		$result = \CFile::GetPath($newFileId);
		//Удаляем старый файл
		\CFile::Delete($oldImageId);

		//Возвращаем новый файл (путь и ID)
		$result = array(
			"image" => \CFile::GetPath($newFileId),
			"id" => $newFileId,
		);
		return json_encode($result);
	}

	/**
	 * Сохраняет фото пользователя
	 *
	 * @return string
	 */
	public function saveUserPhotoAction()
	{
		//Получаем новые значения
		$photoId = $this->getParam('photoId');
		$userId = $this->getParam('userId');

		//Получаем старое фото, чтобы удалить
		$filter = Array
		(
			"ID" => $userId,
		);
		$rsUsers = \CUser::GetList(($by = "id"), ($order = "desc"), $filter, array("FIELDS" => array("PERSONAL_PHOTO")));

		if ($arUser = $rsUsers->GetNext()) {
			$oldFile = $arUser["PERSONAL_PHOTO"];
		}

		//Обновляем данные пользователя
		$arFile = \CFile::MakeFileArray($photoId);

		if ($oldFile) {
			$arFile['del'] = "Y";
			$arFile['old_file'] = $oldFile;
		}

		$user = new \CUser;
		$fields = Array(
			"PERSONAL_PHOTO" => $arFile,
		);
		$user->Update($userId, $fields);
		return;
	}

}