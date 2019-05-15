<?php
/**
 * Bitrix Framework
 * @package    Bitrix
 * @subpackage mlife.smsservices
 * @copyright  2015 Zahalski Andrew
 */

namespace Mlife\Smsservices;

use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

class Func {
	
	public static function getSelect($id,$options,$curValue,$refresh=false,$url=false,$optcode=false){
		
		$html = '<select id="'.$id.'" name="'.$id.'" ';
		if($refresh){
			$html .= 'onchange="location.href=\''.$url.'\'+this.value';
		}
		$html .= '">';
		if($optcode){
		$html .= '<option value="">---</option>';
			foreach($options as $key=>$val){
				$html .= '<option value="'.$key.'"';
				if($curValue && $curValue==$key){
					$html .= ' selected="selected"';
				}
				$html .= '>'.$val;
				$html .= '</option>';
			}
		}else{
		$html .= '<option value="">---</option>';
			foreach($options as $val){
				$html .= '<option value="'.$val['value'].'"';
				if($curValue && $curValue==$val['value']){
					$html .= ' selected="selected"';
				}
				$html .= '>'.$val['text'];
				$html .= '</option>';
			}
		}
		$html .= '</select>';
		
		return $html;
	}
	
}