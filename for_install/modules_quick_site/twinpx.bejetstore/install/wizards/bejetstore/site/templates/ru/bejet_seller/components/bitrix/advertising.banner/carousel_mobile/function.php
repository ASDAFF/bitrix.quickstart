<?
function sortByWeight($array){
	$array_size = count($array);
	for ($x = 0; $x < $array_size; $x++) {
		for ($y = 0; $y < $array_size; $y++) {
			if ($array[$x]["WEIGHT"] < $array[$y]["WEIGHT"]) {
			    $hold = $array[$x];
			    $array[$x] = $array[$y];
			    $array[$y] = $hold;
			}
		}
	}
	return $array;
}
?>