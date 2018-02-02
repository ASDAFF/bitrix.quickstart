<?

namespace Cosmos\Urlrewrite;

/**
 * ����� ��� ������ � urlrewrite.php ��������.
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
     * ����� ���������� ������ �� urlrewrite.php.
     *
     * ��������� ������� � ����������� �� �������� ���� SORT, ��������� �� ��� �� ����� ������� � ������� ��� ���� SORT.
     */
    private function _sort() {

        $this->_aLogMessage[] = "2. �������� ����������. ���������: " . count( $this->_aRewriteRules ) . "\n" . print_r( $this->_aRewriteRules, true );

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
        
        $this->_aLogMessage[] = "3. ��������� ����������. ���������: " . count( $this->_aRewriteRules ) . "\n" . print_r( $this->_aRewriteRules, true );
    }



    /**
     * ����� ��������� ����� ���������� urlrewrite.php.
     *
     * @return String
     */
    private function _prepareText() {

        $this->_aLogMessage[] = "4. ���������� ������ � ������ � ����. ���������: " . count( $this->_aRewriteRules );
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
            $this->_aLogMessage[] = "4.1. Error: �� ������� ������������ ����������� ���� � �����";
            $this->_aLogMessage[] = "detail_info: " . $sAllLogMessage;
            $this->_aLogMessage[] = "count_elements: " . count( $this->_aRewriteRules );
        }

        return $aOutput ? join( "\n", $aOutput ) : false;
    }



    /**
     * ����� ���������� ������ � ����.
     */
    private function _writeToFile( $sFileContent ) {

        if ( !$sFileContent ) {
            $this->_aLogMessage[] = "5. ����� ������ �� �������� (�����������)";
            return false;
        }
        if ( file_put_contents( $this->_sFilePath, $sFileContent ) ) {
            $this->_aLogMessage[] = "5. ����� ������ �������� � ����";
            return true;
        } else {
            $this->_aLogMessage[] = "5. ����� ������ �� �������� (�� ������� ������� ���� - " . $this->_sFilePath . ")";
            return false;
        }
    }



    /**
     * ����� ��������� ������������� ���������� �����.
     *
     * ���� ��� ����� �� ����� ���� ����������� � ���� ���������� true ����� false.
     *
     * @return Boolean
     */
    public function isModified() {
        return !( $this->_getHashFile() == $this->_generateHash() );
    }



    /**
     * ���������� ��� �����.
     *
     * @return String
     */
    public function _generateHash() {
        return hash_file( 'md5', $this->_sFilePath );
    }



    /**
     * ����� �������� �� ���� ��� �����.
     *
     * @return String
     */
    private function _getHashFile() {
        return file_get_contents( $this->_sConfPath . "/urlrewrite_hash" );
    }



    /**
     * ����� ���������� � ���� ��� �����.
     */
    private function _setHashFile() {
        file_put_contents( $this->_sConfPath . "/urlrewrite_hash", $this->_generateHash() );
    }



    /**
     * ����� ���������� � urlrewrite.php ��������������� ������ ������.
     */
    public function reWrite() {
        $this->_sort(); // ��������� ������
        if ( $this->_writeToFile( $this->_prepareText() ) ){ // ���������� ���� �������������� ��� ������ �����
            $this->_setHashFile(); // ���������� ��� �����            
        }
    }



    /**
     * ����� �������������.
     *
     * ���������� ���� �� �����, ��������� ��� �������������, ���������� ������ ������.
     */
    public function init() {
        
        global $USER;
        
        $this->_sDocumentRoot = $_SERVER["DOCUMENT_ROOT"];

        /**
         * @todo ������������ �������������� ���������� ��� ����� � �������
         */
        $this->_sLogsPath = $this->_sDocumentRoot . "/upload/logs";
        if ( !file_exists( $this->_sLogsPath ) ) {
            mkdir( $this->_sLogsPath );
        }

        /**
         * @todo ������������ ���������� ��������� �����������
         */
        $this->_sConfPath = $this->_sDocumentRoot . "/upload/tmp";
        if ( !file_exists( $this->_sConfPath ) ) {
            mkdir( $this->_sConfPath );
        }

        /**
         * @todo ������������ �������������� ����������� � ���������� �� � �������
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
            $this->_aLogMessage[] = "1. ���� ������ - " . $this->_sFilePath . ". �������� ���������: " . count( $this->_aRewriteRules );
        } else {
            $this->_aLogMessage[] = "1. �� ������� ������� ���� - " . $this->_sFilePath;
        }
        
    }



    public function __construct() {
        $this->init();
    }
    
    public function __destruct(){
        
        $this->_aLogMessage[] = "==========/LOG from Cosmos\UrlRewrite. Date " . date( "d:m:Y H:i:s" ) . "(time: " .  time(). ")==========";

        /**
         * @todo �����yenm � ������������, ��� ��������� ����������� ����� � ������� ��������� 
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
