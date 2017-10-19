<?php

class CASDiblockVersion {
	protected static $iblockVersion = null;

	public static function getIblockVersion() {
		if (self::$iblockVersion === null) {
			self::loadIblockVersion();
		}
		return self::$iblockVersion;
	}

	public static function checkMinVersion($checkVersion)
	{
		if (self::$iblockVersion === null) {
			self::loadIblockVersion();
		}
		if (self::$iblockVersion) {
			return version_compare(self::$iblockVersion, $checkVersion, '>=');
		}
		return false;
	}

	protected static function loadIblockVersion() {
		$moduleIblock = CModule::CreateModuleObject('iblock');
		if ($moduleIblock) {
			self::$iblockVersion = $moduleIblock->MODULE_VERSION;
		}
		unset($moduleIblock);
	}
}