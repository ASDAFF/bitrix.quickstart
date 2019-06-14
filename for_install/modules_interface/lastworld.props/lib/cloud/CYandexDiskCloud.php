<?
namespace LastWorld\Cloud;

use CCloudStorageService;
use Bitrix\Main\Localization\Loc;

Loc::loadLanguageFile(__FILE__);

class CYandexDiskCloud extends CCloudStorageService
{

    /**
     * @return CCloudStorageService
     */
    public function GetObject()
    {
        return new CYandexDiskCloud();
    }

    /**
     * @return string
     */
    public function GetID()
    {
        return 'yandex_disc_cloud';
    }

    /**
     * @return string
     */
    public function GetName()
    {
        return Loc::getMessage('LW_YANDEX_DISC_CLOUD_NAME');
    }

    /**
     * @return array[string]string
     */
    public function GetLocationList()
    {
        return array(
            "" => "N/A",
        );
    }

    function GetSettingsHTML($arBucket, $bServiceSet, $cur_SERVICE_ID, $bVarsFromForm)
    {
        if($bVarsFromForm)
            $arSettings = $_POST["SETTINGS"][$this->GetID()];
        else
            $arSettings = unserialize($arBucket["SETTINGS"]);

        if(!is_array($arSettings))
        {
            $arSettings = array(
                "HOST" => "",
                "USER" => "",
                "KEY" => "",
                "FORCE_HTTP" => "N",
            );
        }

        $result = '';
        return $result;
    }

    /**
     * @param array [string]string $arBucket
     * @param array [string]string $arSettings
     *
     * @return bool
     */
    public function CheckSettings($arBucket, &$arSettings)
    {
        // TODO: Implement CheckSettings() method.
    }

    /**
     * @param array [string]string $arBucket
     *
     * @return bool
     */
    public function CreateBucket($arBucket)
    {
        // TODO: Implement CreateBucket() method.
    }

    /**
     * @param array [string]string $arBucket
     *
     * @return bool
     */
    public function DeleteBucket($arBucket)
    {
        // TODO: Implement DeleteBucket() method.
    }

    /**
     * @param array [string]string $arBucket
     *
     * @return bool
     */
    public function IsEmptyBucket($arBucket)
    {
        // TODO: Implement IsEmptyBucket() method.
    }

    /**
     * @param array [string]string $arBucket
     * @param mixed $arFile
     *
     * @return string
     */
    public function GetFileSRC($arBucket, $arFile)
    {
        // TODO: Implement GetFileSRC() method.
    }

    /**
     * @param array [string]string $arBucket
     * @param string $filePath
     *
     * @return bool
     */
    public function FileExists($arBucket, $filePath)
    {
        // TODO: Implement FileExists() method.
    }

    /**
     * @param array [string]string $arBucket
     * @param mixed $arFile
     * @param string $filePath
     *
     * @return bool
     */
    public function FileCopy($arBucket, $arFile, $filePath)
    {
        // TODO: Implement FileCopy() method.
    }

    /**
     * @param array [string]string $arBucket
     * @param mixed $arFile
     * @param string $filePath
     *
     * @return bool
     */
    public function DownloadToFile($arBucket, $arFile, $filePath)
    {
        // TODO: Implement DownloadToFile() method.
    }

    /**
     * @param array [string]string $arBucket
     * @param string $filePath
     *
     * @return bool
     */
    public function DeleteFile($arBucket, $filePath)
    {
        // TODO: Implement DeleteFile() method.
    }

    /**
     * @param array [string]string $arBucket
     * @param string $filePath
     * @param mixed $arFile
     *
     * @return bool
     */
    public function SaveFile($arBucket, $filePath, $arFile)
    {
        // TODO: Implement SaveFile() method.
    }

    /**
     * @param array [string]string $arBucket
     * @param string $filePath
     * @param bool $bRecursive
     *
     * @return array[string][int]string
     */
    public function ListFiles($arBucket, $filePath, $bRecursive = false)
    {
        // TODO: Implement ListFiles() method.
    }

    /**
     * @param array [string]string $arBucket
     * @param mixed $NS
     * @param string $filePath
     * @param float $fileSize
     * @param string $ContentType
     *
     * @return bool
     */
    public function InitiateMultipartUpload($arBucket, &$NS, $filePath, $fileSize, $ContentType)
    {
        // TODO: Implement InitiateMultipartUpload() method.
    }

    /**
     * @return float
     */
    public function GetMinUploadPartSize()
    {
        // TODO: Implement GetMinUploadPartSize() method.
    }

    /**
     * @param array [string]string $arBucket
     * @param mixed $NS
     * @param string $data
     *
     * @return bool
     */
    public function UploadPart($arBucket, &$NS, $data)
    {
        // TODO: Implement UploadPart() method.
    }

    /**
     * @param array [string]string $arBucket
     * @param mixed $NS
     *
     * @return bool
     */
    public function CompleteMultipartUpload($arBucket, &$NS)
    {
        // TODO: Implement CompleteMultipartUpload() method.
}}