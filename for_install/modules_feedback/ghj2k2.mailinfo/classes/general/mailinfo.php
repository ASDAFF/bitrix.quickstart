<?php

class CMailinfo {
  
  /*
   * array of events sent letters
   */
  private $eventsArr=array();
  
  /*
   * array of all mail templates
   */   
  private $mailTemplates=array();
  
  /*
   * array of all mail html text
   */ 
  private $mailTemplateHTML=array();
  
  public function __construct() {
    
    $this->setMailTemplates();
  }
  
  /*
   * Set $eventArr private variable
   */
  private function setEvents($eventStatus=false, $sort, $by){
    
    global $DB;
    $status='';
    if($eventStatus!==false && is_array($eventStatus)) {
      foreach($eventStatus as $k=>$v) {
        $status.=$separ;
        $exName=explode(":", $k);
        $status.=strtoupper($exName[0]);
        switch($exName[1]) {
          case '>':
            $status.='>';
            break;
          case '>=':
            $status.='>=';
            break;
          case '<':
            $status.='<';
            break;
          case '<=':
            $status.='<=';
            break;
          case '!=':
            $status.='!=';
            break;
          default:
            $status.='=';
            break;
        }
        $status.='"'.mysql_real_escape_string($v).'" ';
        $separ='AND ';
      }
        $status="WHERE ".$status;
    }    
    $res=$DB->Query('SELECT * FROM b_event '.$status.' ORDER BY '.mysql_real_escape_string($sort).' '.mysql_real_escape_string($by));
    $this->eventsArr=$res;
  }
  
  /*
   * Returns all the events email
   */
  public function getEvents($eventStatus=false, $sort='ID', $by='DESC'){
        
    $this->setEvents($eventStatus, $sort, $by);
    return $this->eventsArr;
  }
  
  /*
   * Return single event email
   */ 
  public function getEvent($eventID=false) {
      
    if(intval($eventID)<=0)
      return;
    
    global $DB;
    $res=$DB->Query('SELECT * FROM b_event WHERE ID="'.mysql_real_escape_string($eventID).'"');
    if($result=$res->GetNext())
      return $result;
  }
  
  /*
   * Receive all e-mail templates
   */
  private function setMailTemplates($siteLang=LANGUAGE_ID) {    
    
    $rsET = CEventType::GetList(array("LID"=>$siteLang));
    while ($arET = $rsET->Fetch()) {
        $this->mailTemplates[$arET['EVENT_NAME']]=$arET;
    }
  }
  
  /*
   * Return e-mail template
   */
  public function getMailTemplates($templateCode=false) {
    
    if($templateCode!==false)       
      return $this->mailTemplates[$templateCode];
    
    return $this->mailTemplates;
  }
  
  /*
   * return pattern of letters
   */ 
  public function getMailTemplateHTML($templateID=false) {
      
    if(!is_array($this->mailTemplateHTML) || count($this->mailTemplateHTML)<=0)
      $this->setMailTemplateHTML();
    
    if($templateID!==false)
      return $this->mailTemplateHTML[$templateID];
      
    return $this->mailTemplateHTML;
  }
  
  /*
   * get all the email templates and store them in an array
   */
  private function setMailTemplateHTML() {
    
    if(!is_array($this->mailTemplates) || count($this->mailTemplates)<=0) {
      $this->mailTemplateHTML=array();
      return;
    }
    
    $arFilter = Array(
        "ACTIVE" => "Y",
    );
    $rsMess = CEventMessage::GetList($by="site_id", $order="desc", $arFilter);
    while($res=$rsMess->GetNext()) {
      $this->mailTemplateHTML[$res['EVENT_NAME']][]=$res;
    }
  }
}
?>