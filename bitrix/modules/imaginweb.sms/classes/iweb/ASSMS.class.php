<?
##########################################################
// Класс предназначен для отправки СМС через систему smpp.amegasystem.ru
// связь: foxilitas@gmail.com

class ASSMS {
  var $user='userX.Y';    // ваш логин в системе smpp.amegasystem.ru
  var $pass='';        // ваш пароль в системе smpp.amegasystem.ru
  var $post_data=array();      // данные передаваемые на сервер
  var $multipost=false;      // множественный запрос по умолчанию false
  
  function ASSMS($user=false,$pass=false) {
    if($user) $this->user=$user;
    if($pass) $this->pass=$pass;
  }
  
  // команда на начало мульти запроса
  function start_multipost() {
    $this->multipost=true;
  }
  // сбор данных запроса
  function to_multipost($inv) {
    $this->post_data['data'][]=$inv;
  }
  // результирующий запрос на сервер и получение результата
  function process() {
    return $this->get_post_request($this->post_data);
  }
  ################# post_message
  // рассылка смс [mes] по телефонам [target] с возвратом результата XML
  function post_message($mes,$target,$sender=null) {
    if(is_array($target))  $target=implode(',',$target);
    return $this->post_mes($mes,$target,false,$sender);
  }
  // рассылка смс [mes] по кодовому имени контакт листа [phl_codename]
  function post_message_phl($mes,$phl_codename,$sender=null) {
    return $this->post_mes($mes,false,$phl_codename,$sender);
  }
  
  ################# post_voice
  // рассылка voice [mes] по телефонам [target] с возвратом результата XML
  function post_voice($mes,$target,$sender=null) {
    if(is_array($target))  $target=implode(',',$target);
    return $this->post_mes($mes,$target,false,$sender,'SENDVOICE');
  }
  // рассылка смс [mes] по кодовому имени контакт листа [phl_codename]
  function post_voice_phl($mes,$phl_codename,$sender=null) {
    return $this->post_mes($mes,false,$phl_codename,$sender,'SENDVOICE');
  }
  
  function post_mes($mes,$target,$phl_codename,$sender,$smstype='SENDSMS') {
    $in=array(
      'action' => 'post_sms',
      'message' => $mes,
      'sender' => $sender,
      'smstype' => $smstype
    );
    if($target) $in['target']=$target;
    if($phl_codename) $in['phl_codename']=$phl_codename;
    if($this->multipost) $this->to_multipost($in);
    else return $this->get_post_request($in);
  }
  
  ################# status_sms
  /*  получение стстуса смс 
    допустимые параметры:
    1.   date_from
      date_to
      smstype [SENDSMS SENDVOICE]
    2.  sms_group_id
    3.  sms_id  */
  function status_sms_id($sms_id) {
    return $this->status_sms(false,false,false,false,$sms_id);
  }
  function status_sms_group_id($sms_group_id) {
    return $this->status_sms(false,false,false,$sms_group_id,false);
  }
  function status_sms_date($date_from,$date_to,$smstype='SENDSMS') {
    return $this->status_sms($date_from,$date_to,$smstype,false,false);
  }
  function status_sms($date_from,$date_to,$smstype,$sms_group_id,$sms_id) {
    $in=array('action' => 'status');
    if($date_from)    $in['date_from']=$date_from;
    if($date_to)     $in['date_to']=$date_to;
    if($smstype)    $in['smstype']=$smstype;
    if($sms_group_id)  $in['sms_group_id']=$sms_group_id;
    if($sms_id)      $in['sms_id']=$sms_id;
    if($this->multipost) $this->to_multipost($in);
    else return $this->get_post_request($in);
  }
  ################ inbox_sms
  function inbox_sms($new_only=false,$sib_num=false,$phone=false,$prefix=false) {
    $in=array('action' => 'inbox');
    if($new_only)    $in['new_only']=$new_only;
    if($sib_num)     $in['sib_num']=$sib_num;
    if($phone)      $in['phone']=$phone;
    if($prefix)      $in['prefix']=$prefix;
    if($this->multipost) $this->to_multipost($in);
    else return $this->get_post_request($in);
  }
  ################ phone_report_sms
  function phone_report_sms($date_from=false,$date_to=false,$sms_target=false,$smstype='SENDSMS') {
    $in=array('action' => 'phone_report');
    if($date_from)    $in['date_from']=$date_from;
    if($date_to)     $in['date_to']=$date_to;
    if($sms_target)      $in['sms_target']=$sms_target;
    $in['smstype']=$smstype;
    if($this->multipost) $this->to_multipost($in);
    else return $this->get_post_request($in);
  }
  
  ################################################
  // запрос на сервер и получение результата
  function get_post_request($invars) {
    $invars['user'] = ($this->user);
    $invars['pass'] = ($this->pass);
//    $CLIENTADR=isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:false;
    $CLIENTADR=isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:false;
    $invars['CLIENTADR'] = isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:false;
    $invars['CLIENTADR'] ="52.52.52.52";
    $invars['HTTP_ACCEPT_LANGUAGE'] = isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])?$_SERVER['HTTP_ACCEPT_LANGUAGE']:false;

    $hostname="smpp.amegasystem.ru";
    
    $PostData=http_build_query($invars);
    $len=strlen($PostData);
    $nn="\r\n";
//    $send="POST /sendsms/ HTTP/1.0".$nn."Host: ".$hostname."".$nn."Content-Type: application/x-www-form-urlencoded".$nn."Content-Length: $len".$nn."User-Agent: Mozilla/4.0 (compatible; MSIE 5.01; Windows NT)".$nn.$nn.$PostData;
      $send="POST /smw/aisms HTTP/1.0".$nn."Host: ".$hostname."".$nn."Content-Type: application/x-www-form-urlencoded".$nn."Content-Length: $len".$nn."User-Agent: Mozilla/4.0 (compatible; MSIE 5.01; Windows NT)".$nn.$nn.$PostData;
    flush();
    if(($fp = @fsockopen("ssl://".$hostname, 443, $errno, $errstr, 30))!==false) {
      fputs($fp,$send);
      
      $header='';
      do { $header.= fgets($fp, 4096);
      } while (strpos($header,"\r\n\r\n")===false);
      if(get_magic_quotes_runtime())  $header=$this->decode_header(stripslashes($header));
      else              $header=$this->decode_header($header);
      
      $body='';
      while (!feof($fp))  $body.=fread($fp,8192);
      if(get_magic_quotes_runtime())  $body=$this->decode_body($header, stripslashes($body));
      else              $body=$this->decode_body($header, $body);
      
      fclose($fp);
      return $body;
      
    //} elseif(($body=file_get_contents('http://213.171.60.125/sendsms/index.php?'.$PostData))!==false) {
    //  return get_magic_quotes_runtime()?stripslashes($body):$body;
    } else
      return GetMessage("IMAGINWEB_SMS_NEVOZMOJNO_SOEDINITQ");
  }
  
  function decode_header ($str) {
    $part = preg_split ( "/\r?\n/", $str, -1, PREG_SPLIT_NO_EMPTY);
    $out = array ();
    for ($h=0;$h<sizeof($part);$h++) {
    if ($h!=0) {
      $pos = strpos($part[$h],':');
      $k = strtolower ( str_replace (' ', '', substr ($part[$h], 0, $pos )));
      $v = trim(substr($part[$h], ($pos + 1)));
    } else {
      $k = 'status';
      $v = explode (' ',$part[$h]);
      $v = $v[1];
    }
    if ($k=='set-cookie') {
      $out['cookies'][] = $v;
    } else
      if ($k=='content-type') {
        if (($cs = strpos($v,';')) !== false) {
          $out[$k] = substr($v, 0, $cs);
        } else {
          $out[$k] = $v;
        }
      } else {
        $out[$k] = $v;
      }
    }
    return $out;
  }
  
  function decode_body($info,$str,$eol="\r\n" ) {
    $tmp=$str;
    $add=strlen($eol);
    if (isset($info['transfer-encoding']) && $info['transfer-encoding']=='chunked') {
      $str='';
      do {
        $tmp=ltrim($tmp);
        $pos=strpos($tmp, $eol);
        $len=hexdec(substr($tmp,0,$pos));
        if (isset($info['content-encoding'])) {
          $str.=gzinflate(substr($tmp,($pos+$add+10),$len));
        } else {
          $str.=substr($tmp,($pos+$add),$len);
        }
        $tmp = substr($tmp,($len+$pos+$add));
        $check = trim($tmp);
      } while(!empty($check));
    } elseif (isset($info['content-encoding'])) {
      $str=gzinflate(substr($tmp,10));
    }
    return $str;
  }
}

if(!function_exists('http_build_query')) {
  function http_build_query($data,$prefix=null,$sep='',$key='') {
    $ret=array();
    foreach((array)$data as $k => $v) {
      $k=urlencode($k);
      if(is_int($k) && $prefix != null) $k=$prefix.$k;
      if(!empty($key)) $k=$key."[".$k."]";
      
      if(is_array($v) || is_object($v))  array_push($ret,http_build_query($v,"",$sep,$k));
      else   array_push($ret,$k."=".urlencode($v));
    };
    if(empty($sep)) $sep = ini_get("arg_separator.output");    
    return    implode($sep, $ret);
  };
};
?>
