<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
echo(($this->NavPageNomer-1)*$this->NavPageSize+1);
echo(' - ');
if($this->NavPageNomer != $this->NavPageCount)
  echo($this->NavPageNomer * $this->NavPageSize);
else
  echo($this->NavRecordCount); 
echo(' '.GetMessage("nav_of").' ');
echo($this->NavRecordCount);

?>
<br />
<?php 
echo('<font class="'.$StyleText.'">');

if($this->NavPageNomer > 1)
  echo('<a data-inumpage="1" >'.
  $sBegin.'</a> | <a data-inumpage="'.($this->NavPageNomer-1).'" >'.$sPrev.'</a>');
else
  echo($sBegin.' | '.$sPrev);

echo(' | '); 

$NavRecordGroup = $nStartPage;
while($NavRecordGroup <= $nEndPage)
{
  if($NavRecordGroup == $this->NavPageNomer) 
    echo('<b>'.$NavRecordGroup.'</b> | '); 
  else
    echo('<a data-inumpage="'.($NavRecordGroup.$strNavQueryString).'" >'.
	$NavRecordGroup.'</a> | ');

  $NavRecordGroup++;
}

//echo('| ');
if($this->NavPageNomer < $this->NavPageCount)
  echo ('<a data-inumpage="'.($this->NavPageNomer+1).'" >'.
  $sNext.'</a> | <a data-inumpage="'.($this->NavPageCount).'" >'.$sEnd.'</a> ');
else
  echo ($sNext.' | '.$sEnd.' ');
/*
if($this->bShowAll)
  echo ($this->NavShowAll? '| <a class="tablebodylink" 
  href="'.$sUrlPath.'?SHOWALL_'.$this->NavNum.'=0'.$strNavQueryString.
  '#nav_start'.$add_anchor.'">'.$sPaged.
  '</a> ' : '| <a class="tablebodylink" href="'.$sUrlPath.'?SHOWALL_'.
  $this->NavNum.'=1'.$strNavQueryString.
  '#nav_start'.$add_anchor.'">'.$sAll.'</a> ');
*/
echo('</font>');
?>