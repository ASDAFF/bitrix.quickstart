<?php
/**
* ###################################
* # Copyright (c) 2012 SmartRealt   #
* # http://www.smartrealt.com       #
* # mailto:info@smartrealt.com      #
* ###################################
*/

class SmartRealt_CatalogElementPhoto extends SmartRealt_WebServiceDataObject
{
    const TYPE_NAME = __CLASS__;

    public function __construct()
    {
        $this->sTableName = 'smartrealt_catalog_element_photo';
        $this->sWebServiceMethodName  = 'GetPhotosByParams';
        $this->sPrimaryKeyName = 'Id';
        /*$this->sCreateDateField = 'CreateDate';
        $this->sUpdateDateField = 'UpdateDate';*/
        $this->arFields = array( 
            'Id',
            'CatalogElementId',   
            'FileId',
            'Url',
            'Sort',
            'Deleted',
            'CreateDate',
            'UpdateDate',
        );
    }
    
    public function DeleteByObjectId($CatalogElementId)
    {  
        if ($CatalogElementId)
        {
            $rsPhoto = $this->GetList(array("CatalogElementId" => $CatalogElementId));
            
            while($arPhoto = $rsPhoto->Fetch())
            {
                if (parent::Delete($arPhoto['Id']))
                {
                    CFile::Delete($arPhoto['FileId']);
                }
            }
        } 
    }
    
    /**
    * @desc Удаление
    * @param integer идентификтаор записи
    * @param boolean проверить на возможность удаления
    */
    public function Delete($PhotoId, $bCheck = false)
    {                               
        $rsPhoto = $this->GetList(array("Id" => $PhotoId));
        
        if ($arPhoto = $rsPhoto->Fetch())
        {
            if (parent::Delete($PhotoId))
            {
                CFile::Delete($arPhoto['FileId']);
            }
        }
    }
    
    protected function OnAfterWebServiceDataLoad($arData)
    {
        $arResult = array();
        
        foreach ($arData as $arObjectPhoto)
        {
            $arObjectPhoto['CatalogElementId'] = $arObjectPhoto['ObjectId'];
            $arObjectPhoto['Deleted'] = $arObjectPhoto['Deleted'] == 'True' ? "Y" : "N";
            
            if (strlen($arObjectPhoto['Photo']) > 0)
            {
                //Определим путь к папке TMP
                $sTempDir =  ini_get('upload_tmp_dir')?ini_get('upload_tmp_dir'):sys_get_temp_dir();
                
                if (strlen($sTempDir) == 0)
                    $sTempDir = session_save_path();

                $sTempFileName = tempnam($sTempDir, "sm_img_");
                
                $hTempFile = fopen($sTempFileName, "wb");                        
                fwrite($hTempFile, base64_decode($arObjectPhoto['Photo']));
                fclose($hTempFile);
                
                $arObjectPhoto['FileId'] = CFile::SaveFile(array(
                        'name' => $sTempFileName.'.jpg',
                        'tmp_name' => $sTempFileName,
                        'size' => filesize($sTempFileName),
                        'type' => 'image/jpeg',
                    ), "smartrealt/original_images");
                
                unlink($sTempFileName);    
            }
            unset($arObjectPhoto['Photo']);
            
            $arResult[] = $arObjectPhoto;    
        }
        
        return $arResult;
    }
}
?>
