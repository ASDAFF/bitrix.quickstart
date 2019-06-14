<?php
class CLWOption {
	
	private $OptionsFile=array();
	private $OPTION_PATH='';
	
	
	function __construct(){ 
		
		$this->OPTION_PATH=$_SERVER['DOCUMENT_ROOT'].'/bitrix/php_interface/lw_components_options.php';
		
		$lw_components_option=array();
		@include_once($this->OPTION_PATH); //���������� ������ $lw_components_option
		foreach ($lw_components_option as $k=>$v){
			$option_base64_decode[$k]=base64_decode($v);	
		}
		$this->OptionsFile=$option_base64_decode;
		
		return $this;
	}
	
	/*
		�������������� �������� � ����� � �����������
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
		��������� ������������� �������� ������
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
		��������� ��������� (��� ��������) �������� ������� ���������� � ���������� �����
	*/
	private function EditOptions($name, $value=''){
		if (file_exists($this->OPTION_PATH)) {
			if (empty($name)) return false;//���� ��� ���������� �� �������� ��������� ���������
			if (empty($value)){//���� �������� �� �������� � ������� ����������
				unset($this->OptionsFile[$name]);
				return $this->SaveToFile();
			} else { //���� �������� �������� �� �������� ����������
				if ($this->OptionsFile[$name]!=$value){ //�������� ����� ��������
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
		��������� ����� �������� � ������ ���������� � ��������� ���������� �����
	*/
	private function AddOptions($name, $value){
		if (empty($name) or empty($value)) return false;//���� ��� ���������� ��� �� �������� �� �������� ��������� ���������
		$this->OptionsFile[$name]=$value;
		return $this->SaveToFile();
	}	
	
	/*
		������������ �������� � ������ ����������
	*/	
	public function Set($name, $value){
		$value=$this->CheckingOptions($value);
		/*���� ������������� ���������� ������������ � ������� ������������ �����, �� ����������� ����� ���������*/
		if (isset($this->OptionsFile[$name])){
			return $this->EditOptions($name, $value);
		} else {
			return $this->AddOptions($name, $value);
		}
	}	
	
	/*
		������� �������� � ������ ����������
	*/	
	private function Delete($name){
		return $this->EditOptions($name);
	}
	
	/*
		�������� �������� ������� ����������
	*/	
	public function Get($name){
		if (empty($name)) return false;
		return $this->OptionsFile[$name];
	}
	
	/*
		�������� ������ �������� ������� ����������
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