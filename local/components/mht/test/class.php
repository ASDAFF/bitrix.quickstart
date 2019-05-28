<?
	namespace Components\MHT;
	
	if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true){
		die();
	}

	foreach(debug_backtrace() as $a){
		echo $a['file']."\nxxx ";
	}
	\WP::log();
	
	class TestComponent extends \CBitrixComponent{

		function executeComponent(){
			echo $this->getName(); // mht:test
		}
	}