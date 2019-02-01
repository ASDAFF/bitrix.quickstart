<?
class Environment {
	
	private static $arrEnvironments = array();
	
	private static $strLocalCode = null;
	
	private static $strLocalEnv = null;
	
	
	/**
	 * Environment::add()
	 * Добавляет окружения.
	 * 
	 * @param mixed $arrEnv
	 * @return void
	 */
	public static function add($arrEnv) {
		// Сохраним переданые окружения
		if (count($arrEnv)) {
			foreach ($arrEnv as $strEnvCode => $mixList) {
				if (is_array($mixList)) {
					foreach ($mixList as $strList) {
						self::$arrEnvironments[$strEnvCode][] = $strList;
					}//\\ foreach
				} else self::$arrEnvironments[$strEnvCode][] = $strList;
			}//\\ foreach
		}//\\ if
		
		// Получим текущее окружение
		self::detect();
	}//\\ add
	
	/**
	 * Environment::detect()
	 * Определяет по шаблонам какое окружение сейчаи используется.
	 * 
	 * @return void
	 */
	private static function detect() {

		// Получим текущую
		self::$strLocalEnv = self::getEnv();
		
		$strCode = null;
		
		if (count(self::$arrEnvironments) && self::$strLocalEnv != null) {
			foreach (self::$arrEnvironments as $strEnvCode => $arrTemplate) {
				if ($strCode != null) break;
				foreach ($arrTemplate as $strTemplate) {
					if ($strTemplate == self::$strLocalEnv) {
						$strCode = $strEnvCode;
						break;
					}//\\ if
				}//\\ foreach
			}//\\ foreach
		}//\\ if
		
		// Запомним текущее окружение
		self::set($strCode);
	}//\\ detect
	
	/**
	 * Environment::has()
	 * Проверяет соотведствует ли $strCodeEnvironments текущему окружению.
	 * 
	 * @param mixed $strCodeEnvironments
	 * @return
	 */
	public static function has($strCodeEnvironments) {
		
		return ($strCodeEnvironments == self::get());
		
	}//\\ has
	
	/**
	 * Environment::get()
	 * Возвращает код текущего окружения.
	 * 
	 * @return void
	 */
	public static function get() {
		
		return self::$strLocalCode;
		
	}//\\ get
	
	/**
	 * Environment::set()
	 * Устанавливает код текущего окружения.
	 * 
	 * @param mixed $strLocalCode
	 * @return
	 */
	public static function set($strLocalCode) {
		
		self::$strLocalCode = $strLocalCode;
		return self::$strLocalCode;
		
	}//\\ set
	
	/**
	 * Environment::getEnv()
	 * Получает текущее окружение из серверных переменных.
	 * 
	 * @return
	 */
	private static function getEnv() {
		
		if (isset($_SERVER['BITRIX_ENV']) && strlen($_SERVER['BITRIX_ENV'])) return $_SERVER['BITRIX_ENV'];
		else return null;
		
	}//\\ getEnv
	
	
}//\\ Environment