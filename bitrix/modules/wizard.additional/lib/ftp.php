<?php
/**
 * Created by PhpStorm.
 * User: Frolov_S
 * Date: 28.02.2017
 * Time: 14:20
 */

namespace Wizard\Additional;

use Bitrix\Main\Localization\Loc;
use function is_file;

Loc::loadMessages(__FILE__);

/**
 * Class Ftp
 * @package Wizard\Tender
 */
class Ftp
{
	protected $ftp;
	protected $ftpParams = [];
	protected $ftpConnectionsForFiles = [];
	protected $countSend = 5;
	protected $maxCountSend = 5;
	protected $minCountSend = 3;
	protected $localExportFolder = '/wizard/files/export';
	protected $blockFileExport = 'blockExport.data';
	protected $blockFileUsers = 'blockExportUsers.data';
	protected $blockFileExportPath;
	protected $blockFileUsersPath;
	protected $log;
	protected $timeOldSeconds = 180;
	public $fileNames = [];
	
	/**
	 * Ftp constructor.
	 *
	 * @param $params
	 */
	public function __construct($params)
	{
		$this->blockFileExportPath = $_SERVER['DOCUMENT_ROOT'] . $this->localExportFolder . '/'
			. $this->blockFileExport;
		$this->blockFileUsersPath = $_SERVER['DOCUMENT_ROOT'] . $this->localExportFolder . '/' . $this->blockFileUsers;
		$this->log = new Logger();
		$this->log->activate(true);
		$this->log->setType('ftp');
		if(!empty($params)){
			$this->setParams($params);
		}
	}
	
	/**
	 * @param $params
	 */
	public function setParams($params)
	{
		if(!empty($params)) {
			if(!isset($params['port']))
				$params['port'] = 21;
			
			$this->ftpParams = $params;
		}
	}
	
	/**
	 * @param $ftpPath
	 *
	 * @return bool
	 */
	public function ftpFileExist($ftpPath)
	{
		$arFiles = (array)ftp_nlist($this->ftp, dirname($ftpPath));
		if (in_array($ftpPath, $arFiles)) {
			return true;
		} else {
			return false;
		}
	}
	
	
	public function connect()
	{
		$this->ftp = ftp_connect($this->ftpParams['host'], $this->ftpParams['port']);
		if ($this->ftp !== false) {
			$res = ftp_login($this->ftp, $this->ftpParams['user'], $this->ftpParams['password']);
			if (!$res) {
				$this->log->write(Loc::getMessage("AUTH_ERROR_TEXT"));
				exit;
			} else {
				// включение пассивного режима
				ftp_pasv($this->ftp, true);
				//Увеличиваем таймаут
				ftp_set_option($this->ftp, FTP_TIMEOUT_SEC, 300);
			}
		} else {
			$this->log->write(Loc::getMessage("CONNECTION_ERROR_TEXT"));
			exit;
		}
	}
	
	public function disconnect()
	{
		ftp_quit($this->ftp);
	}
	
	/**
	 * @param $localPath
	 * @param $ftpPath
	 *
	 * @return bool
	 */
	public function get($localPath, $ftpPath)
	{
		if ($this->ftpFileExist($ftpPath)) {
			$transfer_mode = FTP_BINARY;
			$res = ftp_get($this->ftp, $localPath, $ftpPath, $transfer_mode);
			if (!$res) {
				$this->log->write(Loc::getMessage("ERROR_GET_BY_FTP_TEXT") . $ftpPath . Loc::getMessage("IN_TEXT") . $localPath);
			}
		} else {
			$res = true;
		}
		
		return $res;
	}
	
	/**
	 * @param $localPath
	 * @param $ftpPath
	 *
	 * @return bool
	 */
	public function put($localPath, $ftpPath)
	{
		if (file_exists($localPath)) {
			$bLoad = true;
			$res = true;
			if ($this->ftpFileExist($ftpPath)) {
				if (ftp_size($this->ftp, $ftpPath) == filesize($localPath)) {
					$bLoad = false;
				}
			}
			if ($bLoad) {
				$transfer_mode = FTP_BINARY;
				$res = ftp_put($this->ftp, $ftpPath, $localPath, $transfer_mode);
				if (!$res) {
					$this->log->write(Loc::getMessage("ERROR_LOAD_TEXT") . $localPath . Loc::getMessage("TO_FTP_IN_TEXT") . $ftpPath);
				}
			}
		} else {
			$res = false;
		}
		
		return $res;
	}
	
	/**
	 * @param $localPath
	 * @param $ftpPath
	 * @param string $additionalPath
	 */
	public function putExec($localPath, $ftpPath, $additionalPath = 'export')
	{
		if (file_exists($localPath)) {
			$filePath = $_SERVER['DOCUMENT_ROOT'] . '/wizard/tmpFilesFtp/' . $additionalPath . '/' . basename($ftpPath);
			$fp = fopen($filePath, 'w');
			fwrite($fp, 'put');
			fclose($fp);
			exec(
				"/usr/bin/php -f " .
				$_SERVER['DOCUMENT_ROOT'] .
				'/wizard/loadFileToFtp.php' .
				" $localPath $ftpPath $additionalPath > /dev/null 2>&1 &"
			);
		}
	}
	
	/**
	 * @param string $additionalPath
	 */
	public function deleteOldLocalTmpFiles($additionalPath = 'export')
	{
		$dirPath = $_SERVER['DOCUMENT_ROOT'] . '/wizard/tmpFilesFtp/' . $additionalPath;
		$files = @scandir($dirPath);
		if (!empty($files)) {
			foreach ($files as $file) {
				if (!in_array($file, ['.', '..'])) {
					$filePath = $dirPath . '/' . $file;
					if (is_file($filePath)) {
						$timeDiff = (time() - filemtime($filePath));
						if ($timeDiff >= $this->timeOldSeconds) {
							unlink($filePath);
						}
					}
				}
			}
		}
	}
	
	/**
	 * @param $arFiles
	 * @param string $additionalPath
	 */
	public function putExecFiles(&$arFiles, $additionalPath = 'export')
	{
		//ограничиваем одновременную загрузку до мининмуму если идут 2 выгрузки сразу
		while (is_array($arFiles) && !empty($arFiles) && count($arFiles) > 0) {
			while (!$this->checkMaxCount($additionalPath)) {
				usleep(100000);//ждем 0,1 секунды
			}
			$this->setCountSend();
			$ftpPath = reset($arFiles);
			$localPath = key($arFiles);
			$this->putExec($localPath, $ftpPath, $additionalPath);
			unset($arFiles[$localPath]);
		}
	}
	
	protected function setCountSend()
	{
		if (file_exists($this->blockFileExportPath) && file_exists($this->blockFileUsersPath))
			$this->countSend = $this->minCountSend;
		else
			$this->countSend = $this->maxCountSend;
	}
	
	/**
	 * @param $ftpPath
	 *
	 * @return bool
	 */
	public function delete($ftpPath)
	{
		if ($this->ftpFileExist($ftpPath))
			$res = ftp_delete($this->ftp, $ftpPath);
		else
			$res = true;
		
		return $res;
	}
	
	/**
	 * @param string $additionalPath
	 *
	 * @return bool
	 */
	public function checkEndLoad($additionalPath = 'export')
	{
		$files = @scandir($_SERVER['DOCUMENT_ROOT'] . '/wizard/tmpFilesFtp/' . $additionalPath);
		if (($files && count($files) <= 2) || (!$files))
			return true;
		
		return false;
	}
	
	/**
	 * @param $additionalPath
	 *
	 * @return bool
	 */
	public function checkMaxCount($additionalPath)
	{
		if (($files = @scandir($_SERVER['DOCUMENT_ROOT'] . '/wizard/tmpFilesFtp/' . $additionalPath))
			&& count($files) < $this->countSend + 2
		)
			return true;
		
		return false;
	}
}