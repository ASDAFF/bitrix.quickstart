<?
interface DSocialPosterFactory
{
	/**
	 * 
	 * Get connection instance
	 * return DSocialPosterConnection
	 */
	public static function GetConnection($postfix="");

	/**
	 *
	 * settings iterator
	 * @param DSocialPosterSettings
	 */
	public static function GetSettings($obEntity=false);
	
	/**
	 * entity iblock settings iterator
	 * return DSocialPosterSettings
	 */
	public static function GetIBlockSettings($IBLOCK_ID=false, $ID=0, $obEntity=false);
	
	/**
	 * 
	 * return list of DSocialPosterEntity
	 */
	public static function GetEntityList();

	/**
	 * 
	 * return list of DSocialPostParams
	 */
	public static function GetPostParams(DSocialPosterParams $settings, $replaceParams=array(), $ID="", $sUrl="", $sName="", $sPreviewText="", $sDetailText="", $sImgUrl="", $sExtId="");
}

class DSocialPosterEntityFactory implements DSocialPosterFactory
{
	public static function GetConnection($postfix="")
	{
		return new DSocialPosterCUrlConnection($postfix);
	}
	public static function GetSettings($obEntity=false)
	{
		return new DSocialPosterIBlockPropertySettings(false, false, $obEntity);	
	}	
	public static function GetIBlockSettings($IBLOCK_ID=false, $ID=0, $obEntity=false)
	{
		return new DSocialPosterIBlockPropertySettings($IBLOCK_ID, $ID, $obEntity);	
	}
	public static function GetEntityList()
	{
		return DSocialPosterEntityManager::GetInstance();
	}
	public static function GetPostParams(DSocialPosterParams $settings, $replaceParams=array(), $ID="", $sUrl="", $sName="", $sPreviewText="", $sDetailText="", $sImgUrl="", $sExtId="", $entityId="")
	{
		return new DSocialPostParams($settings, $replaceParams, $ID, $sUrl, $sName, $sPreviewText, $sDetailText, $sImgUrl, $sExtId, $entityId);
	}
}
?>