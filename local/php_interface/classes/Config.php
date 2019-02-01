<?

class Config {

	/**
	 * All of the loaded configuration items.
	 *
	 * The configuration arrays are keyed by their owning bundle and file.
	 *
	 * @var array
	 */
	public static $arrItems = array();
	
	// Кеш конфигурации
	private static $arrCache = array();


	/**
	 * Config::has()
	 * Проверяет существование ключа.
	 * 
	 * @param mixed $strKey
	 * @return
	 */
	public static function has($strKey) {
		
		return !is_null(static::get($strKey));
		
	}//\\ has

	/**
	 * Config::get()
	 * Возвращает по ключу переменную.
	 * 
	 * <code>
	 *		$session = Config::get('session');
	 *
	 *		// Get a configuration item from a bundle's configuration file
	 *		$name = Config::get('admin::names.first');
	 *
	 *		// Get the "timezone" option from the "application" configuration file
	 *		$timezone = Config::get('application.timezone');
	 * </code>
	 * 
	 * @param mixed $strKey
	 * @param mixed $mixDefault
	 * @return
	 */
	public static function get($strKey, $mixDefault = null) {
		
		list($strFile, $item) = static::parse($strKey);
		
		// Пробуем получить конфиг из файла
		// Если не удается загрузить файл, то возвратим дефолтное значение
		if (!static::load($strFile)) return value($mixDefault);

		$arrItems = static::$arrItems[$strFile];

		// If a specific configuration item was not requested, the key will be null,
		// meaning we'll return the entire array of configuration items from the
		// requested configuration file. Otherwise we can return the item.
		if (is_null($item)) {
		
			return $arrItems;
		
		} else {
		
			return static::array_get($arrItems, $item, $mixDefault);
		
		}//\\ if
	}//\\ get

	/**
	 * Set a configuration item's value.
	 *
	 * <code>
	 *		// Set the "session" configuration array
	 *		Config::set('session', $array);
	 *
	 *		// Set a configuration option that belongs by a bundle
	 *		Config::set('admin::names.first', 'Taylor');
	 *
	 *		// Set the "timezone" option in the "application" configuration file
	 *		Config::set('application.timezone', 'UTC');
	 * </code>
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return void
	 */
	public static function set($key, $value) {
		
		list($file, $item) = static::parse($key);

		static::load($file);

		// If the item is null, it means the developer wishes to set the entire
		// configuration array to a given value, so we will pass the entire
		// array for the bundle into the array_set method.
		if (is_null($item))
		{
			array_set(static::$arrItems, $file, $value);
		}
		else
		{
			array_set(static::$arrItems[$file], $item, $value);
		}
	}

	/**
	 * Config::parse()
	 * Парсит ключ и возвращает имя файла в котором содержится этот ключ  
	 * 
	 * @param mixed $strKey
	 * @return
	 */
	protected static function parse($strKey) {
		
		// Проверим существование ключа в кеше
		if (isset(static::$arrCache[$strKey])) {
			return static::$arrCache[$strKey];
		}//\\ if
		
		// Разобьем ключ по точкам 
		$arrSegments = explode('.', $strKey);

		// Возвратим массив вида [имя файла, имя ключа]
		if (count($arrSegments) >= 2) {
			$arrParsed = array($arrSegments[0], implode('.', array_slice($arrSegments, 1)));
		} else {
			$arrParsed = array($arrSegments[0], null);
		}//\\ if

		return static::$arrCache[$strKey] = $arrParsed;
	}//\\ parse

	/**
	 * Config::load()
	 * Загружает всю конфигурацию из файла.
	 * 
	 * @param mixed $strFile
	 * @return
	 */
	public static function load($strFile) {
	
		// Если он есть в памяти - возвратим его
		if (isset(static::$arrItems[$strFile])) return true;
		
		// Загрузим конфиг из файла
		$arrConfig = static::file($strFile);

		if (count($arrConfig) > 0)
			static::$arrItems[$strFile] = $arrConfig;

		return isset(static::$arrItems[$strFile]);
	}//\\ load

	/**
	 * Config::file()
	 * Загружает конфигурацию из файл.
	 * 
	 * @param mixed $strFile
	 * @return array массив конфигурации
	 */
	public static function file($strFile) {
		
		$arrConfig = array();

		// Пробежимся по всем путям и поищем в этих папках нужный нам конфигурационный файл
		foreach (static::paths() as $strDir) {
			// Если такой файл существует, сольем его с такими же файлами по другим путям
			if (strlen($strDir) && file_exists($strPath = $strDir.$strFile.'.php'))
				$arrConfig = array_merge($arrConfig, require $strPath);
				
		}//\\ foreach

		return $arrConfig;
	}//\\ file

	/**
	 * Config::paths()
	 * Возвращает пути, по которым будет искаться файлы конфиги.
	 * 
	 * @return array пути по которым нужно искать файлы
	 */
	protected static function paths() {
		
		$arrPaths[] = $_SERVER['DOCUMENT_ROOT'].'/bitrix/php_interface/config/';

		return $arrPaths;
	}//\\ paths

	/**
	 * Get an item from an array using "dot" notation.
	 *
	 * <code>
	 *		// Get the $array['user']['name'] value from the array
	 *		$name = array_get($array, 'user.name');
	 *
	 *		// Return a default from if the specified item doesn't exist
	 *		$name = array_get($array, 'user.name', 'Taylor');
	 * </code>
	 *
	 * @param  array   $array
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return mixed
	 */
	protected static function array_get($array, $key, $default = null) {
		if (is_null($key)) return $array;
	
		foreach (explode('.', $key) as $segment) {
			
			if (!is_array($array) || !array_key_exists($segment, $array))
				return (is_callable($default) && ! is_string($default)) ? call_user_func($default) : $default;
	
			$array = $array[$segment];
		}//\\ foreach
	
		return $array;
	}//\\ array_get
}//\\ Config