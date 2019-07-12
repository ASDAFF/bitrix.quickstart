<?
echo('<font class="'.$StyleText.'">('.$title.' ');
echo(($this->NavPageNomer-1)*$this->NavPageSize+1);
echo(' - ');
if($this->NavPageNomer != $this->NavPageCount)
  echo($this->NavPageNomer * $this->NavPageSize);
else
  echo($this->NavRecordCount); 
echo(' '.GetMessage("nav_of").' ');
echo($this->NavRecordCount);
echo(")\n \n</font>");

echo('<font class="'.$StyleText.'">');

if($this->NavPageNomer > 1)
  echo('<a class="tablebodylink" href="'.$sUrlPath.'?PAGEN_'.$this->NavNum.'=1'.
  $strNavQueryString.'#reviews'.$add_anchor.'">'.
  $sBegin.'</a> | <a class="tablebodylink" href="'.$sUrlPath.'?PAGEN_'.
  $this->NavNum.'='.($this->NavPageNomer-1).$strNavQueryString.'#reviews'.
  $add_anchor.'">'.$sPrev.'</a>');
else
  echo($sBegin.' | '.$sPrev);

echo(' | '); 

$NavRecordGroup = $nStartPage;
while($NavRecordGroup <= $nEndPage)
{
  if($NavRecordGroup == $this->NavPageNomer) 
    echo('<b>'.$NavRecordGroup.'</b> '); 
  else
    echo('<a class="tablebodylink" href="'.$sUrlPath.'?PAGEN_'.$this->NavNum.'='.
	$NavRecordGroup.$strNavQueryString.'#reviews'.$add_anchor.'">'.
	$NavRecordGroup.'</a> ');

  $NavRecordGroup++;
}

echo('| ');
if($this->NavPageNomer < $this->NavPageCount)
  echo ('<a class="tablebodylink" href="'.$sUrlPath.'?PAGEN_'.$this->NavNum.'='.
  ($this->NavPageNomer+1).$strNavQueryString.'#reviews'.$add_anchor.'">'.
  $sNext.'</a> | <a class="tablebodylink" href="'.$sUrlPath.'?PAGEN_'.
  $this->NavNum.'='.$this->NavPageCount.$strNavQueryString.
  '#reviews'.$add_anchor.'">'.$sEnd.'</a> ');
else
  echo ($sNext.' | '.$sEnd.' ');

if($this->bShowAll)
  echo ($this->NavShowAll? '| <a class="tablebodylink" 
  href="'.$sUrlPath.'?SHOWALL_'.$this->NavNum.'=0'.$strNavQueryString.
  '#reviews'.$add_anchor.'">'.$sPaged.
  '</a> ' : '| <a class="tablebodylink" href="'.$sUrlPath.'?SHOWALL_'.
  $this->NavNum.'=1'.$strNavQueryString.
  '#reviews'.$add_anchor.'">'.$sAll.'</a> ');

echo('</font>');
?>