<?php

	namespace Webprofy\Bitrix;
	

	class Module{
		private 
			$app,
			$file,
			$id,
			$prefix,
			$css,
			$js;

		function __construct($file, $id, $prefix, $app){
			$this->file = $file;
			$this->app = $app;
			$this->prefix = $prefix;
			$this->id = $id;
		}

		function setLang($file){
			IncludeModuleLangFile($file);
			return $this;
		}

		function getFileRelative(){
			return strtr($this->file, array(
				$_SERVER['DOCUMENT_ROOT'] => ''
			));
		}

		function lang($index, $replacements = array()){
			return GetMessage($this->prefix.$index, $replacements);
		}

		function setTitle($title = null){
			$this->app->setTitle($title ? $title : $this->lang('TITLE'));
			return $this;
		}

		function showMessage($title, $text, $isError = false){
			\CAdminMessage::ShowMessage(array(
				"MESSAGE" => $title,
				"DETAILS" => $text,
				"HTML" => true,
				"TYPE" => $isError ? "ERROR" : "OK",
			));
			return $this;
		}

		function getPath($path = ''){
			if($path[0] == '/'){
				$path = substr($path, 1);
			}
			return $this->getLocalPath('/modules/'.$this->id.'/'.$path);
		}

		private $hasLocal = null;

		function getLocalPath($path, $baseFolder = "/bitrix"){
			$root = rtrim($_SERVER["DOCUMENT_ROOT"], "\\/");

			if($this->hasLocal === null){
				$this->hasLocal = is_dir($root."/local");
			}

			if($this->hasLocal && file_exists($root."/local/".$path)){
				return "/local/".$path;
			}
			elseif(file_exists($root.$baseFolder."/".$path)){
				return $baseFolder."/".$path;
			}

			return false;
		}

		function setJS($js){
			$this->js = $js;
			return $this;
		}

		function setCSS($css){
			$this->css = $css;
			return $this;
		}

		function showJS($js = null){
			if($js == null){
				$js = $this->js;
			}
			foreach($js as $script){
				$src = $this->getPath('/js/'.$script);
				echo '<script src="'.$src.'"></script>';
			}
			return $this;
		}

		function showCSS($css = null){
			if($css == null){
				$css = $this->css;
			}
			foreach($css as $script){
				$src = $this->getPath('/css/'.$script);
				echo '<link rel="stylesheet" href="'.$src.'"/>';
			}
			return $this;
		}

		function getTabs($tabs, $postfix = ''){
			return new \CAdminTabControl(strtr($this->id, array('.' => '_')).'_tabs'.$postfix, $tabs);
		}

		function posting($save, $apply, $method = null){
			if($method == null){
				$method = $_SERVER['REQUEST_METHOD'];
			}
			return
				($method == 'POST') && 
				(strlen($save) || strlen($apply)) && 
				check_bitrix_sessid();
		}

		function getAjaxData(){
			if($_GET['ajax'] == 1){
				return json_decode(file_get_contents("php://input"), 1);
			}
			return false;
		}

		function formStart(){
			?>
				<form action="<?=$this->getFileRelative()?>" method="POST">
			<?
			echo bitrix_sessid_post();
			return $this;
		}
		function formEnd(){
			?>
				</form>
			<?
		}

		function ajaxStart(){
			$this->app->RestartBuffer();
			header('Content-Type: json/application');
		}

		function ajaxEnd($o){
			echo json_encode($o);
			die();	
		}
	}