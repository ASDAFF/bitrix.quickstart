<?php

	namespace Webprofy\Bitrix\Attribute;

	use Webprofy\General\Container;
	
	class GeneralAttributes extends Container{

		protected $iblock;

		function __construct($iblock = null){
			parent::__construct();
			$this->iblock = $iblock;
		}
		
		function setIBlock($iblock){
			$this->iblock = $iblock;
			return $this;
		}	

		function getSelectFields(){}
	}