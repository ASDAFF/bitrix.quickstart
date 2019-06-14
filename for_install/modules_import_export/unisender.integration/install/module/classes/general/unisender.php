<?php

IncludeModuleLangFile(__FILE__);

class UniAPI 
{
	const DOMAIN = 'http://api.unisender.com/';

	protected $API_KEY;
	protected $Encoding = 'CP1251';
	protected $error = array();
	
	public function __construct($API_KEY)
	{
		$this->API_KEY = $API_KEY;
		if (defined('BX_UTF')) {
			$this->Encoding = "UTF-8";
		}
	}
	
	protected function iconv(&$Value, $Key) {
		$Value = iconv($this->Encoding, 'UTF8//IGNORE', $Value);
	}

	protected function mb_convert_encoding(&$Value, $Key) {
		$Value = mb_convert_encoding($Value, 'UTF8', $this->Encoding);
	}
	
	private function query($method, $params = array())
	{
		// Создаём POST-запрос
		$POST = array (
		  'api_key' => $this->API_KEY,
		);
		
		if ($this->Encoding != 'UTF-8') {
			if (function_exists('iconv')) {
				array_walk_recursive($params, array($this, 'iconv'));
			}
			elseif (function_exists('mb_convert_encoding')) {
				array_walk_recursive($params, array($this, 'mb_convert_encoding'));
			}
		}
		
		$POST = array_merge((array)$params, $POST);

		// Устанавливаем соединение
		/*$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $POST);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_URL, self::DOMAIN . 'ru/api/'.$method.'?format=json');
		$result = curl_exec($ch);*/
		
		$ContextOptions = array(
			'http' =>
				array(
					'method'  => 'POST',
					'header'  => 'Content-type: application/x-www-form-urlencoded',
					'content' => http_build_query($POST)
				)
		);

		$Context = stream_context_create($ContextOptions);

		$result = file_get_contents(self::DOMAIN.'ru/api/' .$method.'?format=json', FALSE, $Context);
		
		return $result;
	}
	
	private function checkErrors($jsonResult)
	{
		if (!empty($jsonResult->error))
		{
			$error = $jsonResult->error;
			$code = $jsonResult->code;
			
			if ($this->Encoding!="UTF-8")
			{
				$error = iconv("UTF-8", $this->Encoding, $error);
				$code = iconv("UTF-8", $this->Encoding, $code);
			}
			$this->error = array($error, $code);
			return false;
		}
		else
			return true;
	}
	
	public function getError()
	{
		return $this->error;
	}
	
	public function showError()
	{
		$error = $this->getError();
		echo "<span class=\"errortext\">API ERROR: " . $error[0] . " (code: " . $error[1] . ")";
	}
	
	public function getLists()
	{
		if (($json = $this->query("getLists"))!==false)
		{
			$data = json_decode($json);
			// если есть ошибки
			if ($this->checkErrors($data)===true)
			{
				$lists = array();
				foreach ($data->result as $res)
				{
					$list_id = intval($res->id);
					$list_title = trim($res->title);
					if ($this->Encoding!="UTF-8")
						$list_title = iconv("UTF-8", $this->Encoding, $list_title);
					$lists[] = array('id'=>$list_id, 'title'=>$list_title);
				}
				return $lists;
			}
			else
				return false;
		}
		else
			return false;
	}
	
	public function getFields($types = array('string', 'text', 'number', 'bool'))
	{
		if (($json = $this->query("getFields"))!==false)
		{
			$data = json_decode($json);
			// если есть ошибки
			if ($this->checkErrors($data)===true)
			{
				//print_r($data);
				$fields = array();
				foreach ($data->result as $res)
				{
					$id = intval($res->id);
					$name = trim($res->name);
					if ($this->Encoding!="UTF-8")
						$name = iconv("UTF-8", $this->Encoding, $name);
					$type = $res->type;
					$is_visible = $res->is_visible;
					$view_pos = $res->view_pos;
					
					if (in_array($type, $types))
					{
						$fields[$id] = array(
							'id'=>$id,
							'name'=>$name,
							'type'=>$type,
							'is_visible'=>$is_visible,
							'view_pos'=>$view_pos
						);
					}
				}
				return $fields;
			}
			else
				return false;
		}
		else
			return false;
	}
	
	public function importContacts($params)
	{
		if (($json = $this->query("importContacts", $params))!==false)
		{
			$data = json_decode($json);
			// если есть ошибки
			if ($this->checkErrors($data)===true)
			{
				$results = array();
				$results['total'] = intval($data->result->total);
				$results['inserted'] = intval($data->result->inserted);
				$results['updated'] = intval($data->result->updated);
				$results['deleted'] = intval($data->result->deleted);
				$results['new_emails'] = intval($data->result->new_emails);
				return $results;
			}
			else
				return false;
		}
		else
			return false;
	}

}