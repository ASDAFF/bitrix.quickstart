<?
IncludeModuleLangFile(__FILE__);

class CIMYIELittleAdminUtils
{
	function ShowIntSelect($startFrom, $maxValues, $selectName, $selectedValue)
	{
		if($startFrom>-1 & $maxValues>0 && $selectName!="")
		{
			echo "<select name=\"".$selectName."\">";
			for($i=$startFrom;$i<($startFrom+$maxValues);$i++)
			{
				echo "<option value=\"".$i."\"";
				if($i==$selectedValue) { echo " selected "; }
				echo ">".$i."</option>";
			}
			echo "</select>";
		} else {
			return FALSE;
		}
	}
}
?>
