<?
	namespace BitrixQuickStart;

	use \Bitrix\Main\Loader;

	/**
	 * Autoload class
	 *
	 * @author Eugene Zadorin 
	 **/
	class Autoloader 
	{
		/**
		 * @var array $autoloadPath Array of paths to classes folders from site root
		 **/
		protected $autoloadPath = array(
			'/local/classes/'
		);


		/**
		 * @var array $classMap Class map defines dependencies between class names and modules
		 **/
		protected $classMap = array(
			'/^CIBlock/' => 'iblock',
			'/^_CIBElement/' => 'iblock',
			'/^CCatalog/' => 'catalog',
			'/^CExtra/' => 'catalog',
			'/^CPrice/' => 'catalog',
			'/^CSale/' => 'sale',
			'/^CCurrency/' => 'currency',
			'/^CSearch/' => 'search',
			'/^CSiteMap/' => 'search',
			'/^CPosting/' => 'subscribe',
			'/^CRubric/' => 'subscribe',
			'/^CSubscription/' => 'subscribe',
			'/^CBlog/' => 'blog',
			'/^blogTextParser/' => 'blog',
			'/^CForm/' => 'form',
			'/^CAdv/' => 'advertising',
			'/^CSocNet/' => 'socialnetwork',
			'/^CFilterDictionary/' => 'forum',
			'/^CFilterLetter/' => 'forum',
			'/^CFilterUnquotableWords/' => 'forum',
			'/^forumTextParser/' => 'forum',
			'/^textParser/' => 'forum',
			'/^CForum/' => 'forum',
			'/^CWiki/' => 'wiki',
			'/^HighloadBlock/' => 'highloadblock'
		);


		public function __construct() {
			spl_autoload_register(array($this, 'load'));
		}


		/**
		 * Implementation of autoload function
		 * 
		 * @param string $className Name of class to be loaded
		 * @return boolean Returns true if class found and successfully loaded, false otherwise
		 **/
		protected function load($className) {
			// try to find module
			if ($moduleName = $this->getModule($className)) {
				if (\Bitrix\Main\Loader::includeModule($moduleName)){
					\Bitrix\Main\Loader::autoLoad($className);
					return true;
				}
			}

			// if no module exists - check same class at autoload directories
			$directories = $this->getAutoloadPath();
			foreach ($directories as $directory) {
				$directory = $_SERVER['DOCUMENT_ROOT'] . $directory;
				if ($classPath = $this->searchClassRecursive($className, $directory)) {
					require_once($classPath);
					return true;
				}
			}
			
			return false;
		}


		/**
		 * Gets class map
		 * 
		 * @see Autoloader::$classMap
		 * @return array
		 **/
		public function getClassMap() {
			return $this->classMap;
		}


		/**
		 * Adds item into class map
		 * 
		 * @param string $classReg Regular expression defining the name of the class
		 * @param string $moduleName Name of module which contains necessary class
		 * @see Autoloader::$classMap
		 * @return Autoloader
		 * 
		 * If $classReg item already exists in class map, it will be overwriten.
		 **/
		public function addClassMapItem($classReg, $moduleName)	{
			if (strlen($classReg) > 0 && strlen($moduleName) > 0) {
				$this->classMap[ $classReg ] = $moduleName;
			}

			return $this;
		}


		/**
		 * Gets paths to classes folders
		 *
		 * @see Autoloader::$autoloadPath
		 * @return array
		 **/
		public function getAutoloadPath() {
			return $this->autoloadPath;
		}


		/**
		 * Adds path to custom classes folder
		 *
		 * @param string $path Path to classes folder from site root
		 * @return Autoloader
		 **/
		public function addAutoloadPath($path) {
			if (strlen($path) > 0) {
				$this->autoloadPath[] = $path;
			}

			return $this;
		}


		/**
		 * Try to get module name by class name
		 *
		 * @param string $className Name of the class to check
		 * @return mixed string|boolean Returns module name if found in class map, false otherwise
		 **/
		protected function getModule($className) {
			$bitrixClasses = $this->getClassMap();
			
			foreach ($bitrixClasses as $regClass => $moduleName) {
				if (preg_match($regClass, $className)) {
					return $moduleName;
				}
			}

			return false;
		}


		/**
		 * Recursively searches inc-file for a class in a directory
		 *
		 * @param string $className Name of the class to search
		 * @param string $directoryPath Absolute path to classes folder 
		 * @return mixed string|boolean Returns inc-file path if found, false otherwise
		 **/
		protected function searchClassRecursive($className, $directoryPath) {
			$classPath = $directoryPath . $className . '.php';

			if (file_exists($classPath)) {
				return $classPath;
			} elseif ($directoryItems = scandir($directoryPath)) {
				foreach ($directoryItems as $item) {
					if ($item == '.' || $item == '..') {
						continue;
					}

					if (is_dir($directoryPath . $item)) {
						return $this->searchClassRecursive($className, $directoryPath . $item . '/');
					}
				}
			}

			return false;
		}
	}
?>