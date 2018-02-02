<?

namespace Cosmos\Urlrewrite;

/**
 * Класс для работы с urlrewrite.php битрикса.
 */
class Urlrewrite {

    private $_sDocumentRoot = "";
    private $_sLogsPath = "";
    private $_sConfPath = "";
    private $_sFilePath = "";
    private $_aRewriteRules = array();
    private $_aLogMessage = array();



    public static function OnAfterEpilog() {
        $oUrlrewrite = new Urlrewrite;
        if ( $oUrlrewrite->isModified() ) {
            $oUrlrewrite->reWrite();
        }   
    }



    /**
     * Метод сортировки правил из urlrewrite.php.
     *
     * Сортирует правила в зависимости от значения поля SORT, оставляет на том же месте правила у которых нет поля SORT.
     */
    private function _sort() {

        $this->_aLogMessage[] = "2. Начинаем сортировку. Элементов: " . count( $this->_aRewriteRules ) . "\n" . print_r( $this->_aRewriteRules, true );

        $aSort = array();
        $aItemValues = array();
        foreach ( $this->_aRewriteRules as $iKey => $aItem ) {
            $this->_aRewriteRules[ $iKey ][ 'SORT' ] = isset( $aItem[ 'SORT' ] ) ? (int)$aItem[ 'SORT' ] : 100;
        }
        usort($this->_aRewriteRules, function($aFirst, $aSecond){
            if ( $aFirst[ 'SORT' ] == $aSecond[ 'SORT' ] ){
                return ( strlen( $aFirst[ 'CONDITION' ] ) > strlen( $aSecond[ 'CONDITION' ] ) ) ? -1 : 1;
            }
            return ($aFirst[ 'SORT' ] < $aSecond[ 'SORT' ]) ? -1 : 1;
        });
        
        $this->_aLogMessage[] = "3. Закончили сортировку. Элементов: " . count( $this->_aRewriteRules ) . "\n" . print_r( $this->_aRewriteRules, true );
    }



    /**
     * Метод формирует новое содержимое urlrewrite.php.
     *
     * @return String
     */
    private function _prepareText() {

        $this->_aLogMessage[] = "4. Подготовка данных к записи в файл. Элементов: " . count( $this->_aRewriteRules );
        $aOutput = array();
        if ( count( $this->_aRewriteRules ) > 0 ) {
            $aOutput[] = '<?';
            $aOutput[] = '$arUrlRewrite = array(';
            foreach ( $this->_aRewriteRules as $aRule ) {
                $aOutput[] = "\tarray(";
                foreach ( $aRule as $sParam => $sParamValue ) {
                    $aOutput[] = "\t\t\"" . EscapePHPString( $sParam ) . "\" => \"" . EscapePHPString( $sParamValue ) . "\",";
                }
                $aOutput[] = "\t),";
            }
            $aOutput[] = ');';
            $aOutput[] = '?>';
        } else {
            $this->_aLogMessage[] = "4.1. Error: Не удалось сформировать заполненный файл с логом";
            $this->_aLogMessage[] = "detail_info: " . $sAllLogMessage;
            $this->_aLogMessage[] = "count_elements: " . count( $this->_aRewriteRules );
        }

        return $aOutput ? join( "\n", $aOutput ) : false;
    }



    /**
     * Метод производит запись в файл.
     */
    private function _writeToFile( $sFileContent ) {

        if ( !$sFileContent ) {
            $this->_aLogMessage[] = "5. Новые данные не записаны (отсутствуют)";
            return false;
        }
        if ( file_put_contents( $this->_sFilePath, $sFileContent ) ) {
            $this->_aLogMessage[] = "5. Новые данные записаны в файл";
            return true;
        } else {
            $this->_aLogMessage[] = "5. Новые данные не записаны (не удалось открыть файл - " . $this->_sFilePath . ")";
            return false;
        }
    }



    /**
     * Метод проверяет необходимость перезаписи файла.
     *
     * Если хэш файла не равен хэшу сохранённому в базу возвращает true иначе false.
     *
     * @return Boolean
     */
    public function isModified() {
        return !( $this->_getHashFile() == $this->_generateHash() );
    }



    /**
     * генерируем хэш файла.
     *
     * @return String
     */
    public function _generateHash() {
        return hash_file( 'md5', $this->_sFilePath );
    }



    /**
     * Метод получает из базы хэш файла.
     *
     * @return String
     */
    private function _getHashFile() {
        return file_get_contents( $this->_sConfPath . "/urlrewrite_hash" );
    }



    /**
     * Метод записывает в базу хэш файла.
     */
    private function _setHashFile() {
        file_put_contents( $this->_sConfPath . "/urlrewrite_hash", $this->_generateHash() );
    }



    /**
     * Метод записывает в urlrewrite.php отсортированный массив правил.
     */
    public function reWrite() {
        $this->_sort(); // сортируем массив
        if ( $this->_writeToFile( $this->_prepareText() ) ){ // записываем файл подготовленный для записи текст
            $this->_setHashFile(); // записываем хэш файла            
        }
    }



    /**
     * Метод инициализации.
     *
     * Запоминает путь до файла, проверяет его существование, запоминает массив правил.
     */
    public function init() {
        
        global $USER;
        
        $this->_sDocumentRoot = $_SERVER["DOCUMENT_ROOT"];

        /**
         * @todo организовать редактирование директории для логов в админке
         */
        $this->_sLogsPath = $this->_sDocumentRoot . "/upload/logs";
        if ( !file_exists( $this->_sLogsPath ) ) {
            mkdir( $this->_sLogsPath );
        }

        /**
         * @todo организовать управление временной директорией
         */
        $this->_sConfPath = $this->_sDocumentRoot . "/upload/tmp";
        if ( !file_exists( $this->_sConfPath ) ) {
            mkdir( $this->_sConfPath );
        }

        /**
         * @todo Организовать многоуровневое логирование и управление им в админке
         */
        $this->_aLogMessage[] = "";
        $this->_aLogMessage[] = "==========LOG from Cosmos\UrlRewrite. Date " . date( "d:m:Y H:i:s" ) . "(time: " .  time(). ")==========";
        $this->_aLogMessage[] = "USER: " . print_r( $USER->GetID(), true );
        $this->_aLogMessage[] = "db_hash:\t" . $this->_getHashFile();
        $this->_aLogMessage[] = "file";

        $this->_sFilePath = $this->_sDocumentRoot . '/urlrewrite.php';
        if ( file_exists( $this->_sFilePath ) ) {
            
            include($this->_sFilePath);
            $this->_aRewriteRules = $arUrlRewrite;
            $this->_aLogMessage[] = "file_hash:\t" . $this->_generateHash();
            $this->_aLogMessage[] = "1. Файл открыт - " . $this->_sFilePath . ". Содержит элементов: " . count( $this->_aRewriteRules );
        } else {
            $this->_aLogMessage[] = "1. Не удалось открыть файл - " . $this->_sFilePath;
        }
        
    }



    public function __construct() {
        $this->init();
    }
    
    public function __destruct(){
        
        $this->_aLogMessage[] = "==========/LOG from Cosmos\UrlRewrite. Date " . date( "d:m:Y H:i:s" ) . "(time: " .  time(). ")==========";

        /**
         * @todo упомяyenm в документации, что отключить логирование можно с помощью константы 
         */
        if ( $this->_aLogMessage && defined( 'URLREWRITE_SORT_DEBUG' ) && URLREWRITE_SORT_DEBUG === 'Y' ) {
            $this->logging( "/urlrewrite.log", join( "\n", $this->_aLogMessage ) );
        }
        
    }



    public function logging( $sLogFileName, $sLogMessage ) {
        $sLogsFilePath = $this->_sLogsPath . $sLogFileName;
        file_put_contents( $sLogsFilePath, $sLogMessage, FILE_APPEND );
    }

}
