<?php
class CLWOption {
	
	private $OptionsFile=array();
	private $OPTION_PATH='';
	
	
	function __construct(){ 
		
		$this->OPTION_PATH=$_SERVER['DOCUMENT_ROOT'].'/bitrix/php_interface/lw_components_options.php';
		
		$lw_components_option=array();
		@include_once($this->OPTION_PATH); //¬озвращает массив $lw_components_option
		foreach ($lw_components_option as $k=>$v){
			$option_base64_decode[$k]=base64_decode($v);	
		}
		$this->OptionsFile=$option_base64_decode;
		
		return $this;
	}
	
	/*
		ѕерезаписываем зна€ени€ в файле с настройками
	*/
	private function SaveToFile(){
		$error=false;
		if (!$option_file=fopen($this->OPTION_PATH, "w" )){return false;};
		foreach ($this->OptionsFile as $k=>$v){
			$res=fwrite($option_file, '<? $lw_components_option["'.$k.'"]="'.base64_encode($v).'";?>'."\r\n" );
			if (!$res){
				unset($this->OptionsFile[$k]);
				$error=true;
			}
		}
		fclose($option_file);
		if ($error) return false;
		return true;
	}	
	
	/*
		¬ыполн€ет предобработку вход€щих данных
	*/
	private function CheckingOptions($value){
		$value_type=gettype($value);
		if (in_array($value_type, array("array", "object", "resource", "boolean"))){
			$value=serialize($value);
		} elseif (in_array($value_type, array("NULL", "unknown type", "double"))) {
			return '';
		}
		$value=str_replace(array('<','>'),'',$value);
		return $value;
	}
	
	/*
		¬ыполн€ет изменение (или удалени€) элемента массива параметров и перезапись файла
	*/
	private function EditOptions($name, $value=''){
		if (file_exists($this->OPTION_PATH)) {
			if (empty($name)) return false;//≈сли им€ переменной не указанно завершаем обработку
			if (empty($value)){//≈сли значение не указанно Ч удал€ем переменную
				unset($this->OptionsFile[$name]);
				return $this->SaveToFile();
			} else { //≈сли значение указанно то измен€ем переменную
				if ($this->OptionsFile[$name]!=$value){ //”казанно новое значение
					$this->OptionsFile[$name]=$value;
					return $this->SaveToFile();
				} else {
					return true;	
				}
			}
		} else {
			$this->AddOptions($name, $value);
		}
	}
	
	/*
		ƒобавл€ет новое значение в массив параметров и выполн€ет перезапись файла
	*/
	private function AddOptions($name, $value){
		if (empty($name) or empty($value)) return false;//≈сли им€ переменной или ее значение не указанно завершаем обработку
		$this->OptionsFile[$name]=$value;
		return $this->SaveToFile();
	}	
	
	/*
		”танавливает значение в массив параметров
	*/	
	public function Set($name, $value){
		$value=$this->CheckingOptions($value);
		/*если запрашиваема€ переменна€ присутствует в массиве подключеного файла, то редактируем иначе добавл€ем*/
		if (isset($this->OptionsFile[$name])){
			return $this->EditOptions($name, $value);
		} else {
			return $this->AddOptions($name, $value);
		}
	}	
	
	/*
		”дал€ет значение в массив параметров
	*/	
	private function Delete($name){
		return $this->EditOptions($name);
	}
	
	/*
		ѕолучает значение массива параметров
	*/	
	public function Get($name){
		if (empty($name)) return false;
		return $this->OptionsFile[$name];
	}
	
	/*
		ѕолучает список значений массива параметров
	*/	
	public function GetList(){
		return $this->OptionsFile;
	}
	
	
	
	
	
	public function __set($name, $value){
		$this->Set($name, $value);
	}
	
	public function __get($name){
		return $this->Get($name);
	}
	
	public function __isset($name){
		return isset($this->OptionsFile[$name]);
	}
	
	public function __unset($name){
		return $this->Delete($name);
	}
}