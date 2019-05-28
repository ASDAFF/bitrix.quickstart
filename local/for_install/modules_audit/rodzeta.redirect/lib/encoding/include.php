<?php

namespace Encoding\Csv {
	if (!function_exists("\Encoding\Csv\Write")) {
		function Write($fname, $data, $sep = "\t") {
			$f = fopen($fname, "w");
			if ($f === false) {
				return;
			}
			foreach ($data as $row) {
				foreach ($row as $i => $v) {
					$row[$i] = trim($row[$i]);
				}
				ksort($row);
				if (count(array_filter($row)) == 0) {
					continue;
				}
				fputcsv($f, $row, $sep);
			}
			fclose($f);
		}

		function Read($fname, $sep = "\t") {
			$result = [];
			$f = fopen($fname, "r");
			if ($f === false) {
				return $result;
			}
			while (($row = fgetcsv($f, 4000, $sep)) !== false) {
				$result[] = array_map("trim", $row);
			}
			fclose($f);
			return $result;
		}
	}
}

namespace Encoding\PhpArray {
	if (!function_exists("\Encoding\PhpArray\Write")) {
		function Write($fname, $data) {
			file_put_contents(
				$fname,
				"<?php\nreturn " . var_export($data, true) . ";"
			);
		}
	}
}

namespace Encoding\PhpVariable {
	if (!function_exists("\Encoding\PhpVariable\Write")) {
		function Write($fname, $data, $varName, $before = "", $after = "") {
			file_put_contents(
				$fname,
				"<?php\n" 
		      . $before 
		      . $varName . " = " . var_export($data, true) . ";" 
		      . $after
			);
		}
	}
}

/*
namespace Encoding\PhpConst {
	function Write($fname, $data, $namespace = "") {
		// $result  = " const x1 = ... const x2 = ... or define("x1", ...); define("x2", ...) ... ";
		// file_put_contents($fname, $result
	}

	function Read($fname) {
		// require ...
	}
}

*/

/*
namespace Encoding\Ssi {
	if (!function_exists("\Encoding\Ssi\Write")) {
		function Write($fname, $data, $additional = "") {
			$vars = [];
			foreach ($data as $k => $v) {
				$vars[] = '<!--#set var="' . $k . '" value="' . htmlentities($v) . '"-->';
			}
			file_put_contents(
				$fname,
				implode("\n", $vars) . "\n"
					. ($additional != ""? $additional : "")
			);
		}
		//function Read($fname) {
			// file_get ...
		//}

		// use in ssi page
	}
}
*/

/*
namespace Encoding\JsConst {
	function Write($fname, $data) {
		// $result  = " const x1 = ... const x2 = ... ";
		// file_put_contents($fname, $result
	}

	//function Read($fname) {
		//file_get...
	//}

	// using in js: <script src="/js/some-file-consts.js"></script>
}
*/
