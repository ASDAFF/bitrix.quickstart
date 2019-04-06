<?
//старый метод для отправки смс
class CMlifeSmsServices {
	
	function __construct() {
		$this->transport = new \Mlife\Smsservices\Sender();
	}
	
	public function checkPhoneNumber ($phone,$all=true) {
		return $this->transport->checkPhoneNumber($phone,$all);
	}
	
	public function sendSms($phones, $mess, $time=0, $sender=false, $prim='', $addHistory=true, $update=false, $error=false) {
		return $this->transport->sendSms($phones, $mess, $time, $sender, $prim, $addHistory, $update, $error);
	}
	
}

class CMlifeSmsServicesHtml {
	
	function getSelect($id,$options,$curValue,$refresh=false,$url=false,$optcode=false){
		
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
?>