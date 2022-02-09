<?
IncludeModuleLangFile(__FILE__);

class CGridOptions
{
	protected $grid_id;
	protected $all_options;
	protected $options;
	protected $filter;

	public function __construct($grid_id)
	{
		$this->grid_id = $grid_id;
		$this->options = array();
		$this->filter = array();

		$aOptions = CUserOptions::GetOption("main.interface.grid", $this->grid_id, array());

		if(!is_array($aOptions["views"]))
			$aOptions["views"] = array();
		if(!is_array($aOptions["filters"]))
			$aOptions["filters"] = array();
		if(!array_key_exists("default", $aOptions["views"]))
			$aOptions["views"]["default"] = array("columns"=>"");
		if($aOptions["current_view"] == '' || !array_key_exists($aOptions["current_view"], $aOptions["views"]))
			$aOptions["current_view"] = "default";

		$this->all_options = $aOptions;

		if(array_key_exists($aOptions["current_view"], $aOptions["views"]))
			$this->options = $aOptions["views"][$aOptions["current_view"]];
		if($this->options["saved_filter"] <> '' && array_key_exists($this->options["saved_filter"], $aOptions["filters"]))
			if(is_array($aOptions["filters"][$this->options["saved_filter"]]["fields"]))
				$this->filter = $aOptions["filters"][$this->options["saved_filter"]]["fields"];
	}

	public function GetSorting($arParams=array())
	{
		if(!is_array($arParams["vars"]))
			$arParams["vars"] = array("by" => "by", "order" => "order");
		if(!is_array($arParams["sort"]))
			$arParams["sort"] = array();

		$arResult = array(
			"sort" => $arParams["sort"],
			"vars" => $arParams["vars"],
		);

		$key = '';
		if(isset($_REQUEST[$arParams["vars"]["by"]]))
		{
			$_SESSION["main.interface.grid"][$this->grid_id]["sort_by"] = $_REQUEST[$arParams["vars"]["by"]];
		}
		elseif(!isset($_SESSION["main.interface.grid"][$this->grid_id]["sort_by"]))
		{
			if($this->options["sort_by"] <> '')
				$key = $this->options["sort_by"];
		}
		if(isset($_SESSION["main.interface.grid"][$this->grid_id]["sort_by"]))
			$key = $_SESSION["main.interface.grid"][$this->grid_id]["sort_by"];

		if($key <> '')
		{
			if(isset($_REQUEST[$arParams["vars"]["order"]]))
			{
				$_SESSION["main.interface.grid"][$this->grid_id]["sort_order"] = $_REQUEST[$arParams["vars"]["order"]];
			}
			elseif(!isset($_SESSION["main.interface.grid"][$this->grid_id]["sort_order"]))
			{
				if($this->options["sort_order"] <> '')
					$arResult["sort"] = array($key => $this->options["sort_order"]);
			}
			if(isset($_SESSION["main.interface.grid"][$this->grid_id]["sort_order"]))
				$arResult["sort"] = array($key => $_SESSION["main.interface.grid"][$this->grid_id]["sort_order"]);
		}

		return $arResult;
	}

	public function GetNavParams($arParams=array())
	{
		$arResult = array(
			"nPageSize" => (isset($arParams["nPageSize"])? $arParams["nPageSize"] : 20),
		);

		if($this->options["page_size"] <> '')
			$arResult["nPageSize"] = $this->options["page_size"];

		return $arResult;
	}

	public function GetVisibleColumns()
	{
		if($this->options["columns"] <> '')
			return explode(",", $this->options["columns"]);
		return array();
	}

	public function GetFilter($arFilter)
	{
		$aRes = array();
		foreach($arFilter as $field)
		{
			//date
			if(isset($_REQUEST[$field["id"]."_datesel"]))
			{
				if($_REQUEST[$field["id"]."_datesel"] <> '')
				{
					$aRes[$field["id"]."_datesel"] = $_REQUEST[$field["id"]."_datesel"];
					CGridOptions::CalcDates($field["id"], $_REQUEST, $aRes);
				}
				else
				{
					unset($_SESSION["main.interface.grid"][$this->grid_id]["filter"][$field["id"]."_datesel"]);
					unset($_SESSION["main.interface.grid"][$this->grid_id]["filter"][$field["id"]."_from"]);
					unset($_SESSION["main.interface.grid"][$this->grid_id]["filter"][$field["id"]."_to"]);
					unset($_SESSION["main.interface.grid"][$this->grid_id]["filter"][$field["id"]."_days"]);
				}
				continue;
			}

			//quick
			if($_REQUEST[$field["id"]."_list"] <> '' && $_REQUEST[$field["id"]] <> '')
				$aRes[$field["id"]."_list"] = $_REQUEST[$field["id"]."_list"];

			//number interval
			if(isset($_REQUEST[$field["id"]."_from"]))
			{
				if($_REQUEST[$field["id"]."_from"] <> '')
					$aRes[$field["id"]."_from"] = $_REQUEST[$field["id"]."_from"];
				else
					unset($_SESSION["main.interface.grid"][$this->grid_id]["filter"][$field["id"]."_from"]);
			}
			if(isset($_REQUEST[$field["id"]."_to"]))
			{
				if($_REQUEST[$field["id"]."_to"] <> '')
					$aRes[$field["id"]."_to"] = $_REQUEST[$field["id"]."_to"];
				else
					unset($_SESSION["main.interface.grid"][$this->grid_id]["filter"][$field["id"]."_to"]);
			}

			//filtered outside, we don't control the filter field value
			if($field["filtered"] == true)
			{
				if(isset($field["value"]))
					$aRes[$field["id"]] = $field["value"];
				else
					$aRes[$field["id"]] = true;
				continue;
			}

			//list or string
			if(isset($_REQUEST[$field["id"]]))
			{
				if(is_array($_REQUEST[$field["id"]]) && !empty($_REQUEST[$field["id"]]) && $_REQUEST[$field["id"]][0] <> '' || !is_array($_REQUEST[$field["id"]]) && $_REQUEST[$field["id"]] <> '')
					$aRes[$field["id"]] = $_REQUEST[$field["id"]];
				else
					unset($_SESSION["main.interface.grid"][$this->grid_id]["filter"][$field["id"]]);
			}
		}

		//Check for filter ID -->
		if(isset($_REQUEST["apply_filter"]) && $_REQUEST["apply_filter"] === 'Y' && isset($_REQUEST["grid_filter_id"]))
		{
			$aRes["GRID_FILTER_APPLIED"] = true;
			$aRes["GRID_FILTER_ID"] = $_REQUEST["grid_filter_id"];
		}
		//<-- Check for filter ID

		if(!empty($aRes))
			$_SESSION["main.interface.grid"][$this->grid_id]["filter"] = $aRes;
		elseif($_REQUEST["clear_filter"] <> '')
			$_SESSION["main.interface.grid"][$this->grid_id]["filter"] = array();
		elseif(is_array($_SESSION["main.interface.grid"][$this->grid_id]["filter"]))
			return $_SESSION["main.interface.grid"][$this->grid_id]["filter"];
		elseif(!empty($this->filter))
		{
			foreach($arFilter as $field)
			{
				if($this->filter[$field["id"]."_datesel"] <> '')
				{
					$aRes[$field["id"]."_datesel"] = $this->filter[$field["id"]."_datesel"];
					CGridOptions::CalcDates($field["id"], $this->filter, $aRes);
					continue;
				}
				if($this->filter[$field["id"]."_list"] <> '' && $this->filter[$field["id"]] <> '')
					$aRes[$field["id"]."_list"] = $this->filter[$field["id"]."_list"];
				if($this->filter[$field["id"]."_from"] <> '')
					$aRes[$field["id"]."_from"] = $this->filter[$field["id"]."_from"];
				if($this->filter[$field["id"]."_to"] <> '')
					$aRes[$field["id"]."_to"] = $this->filter[$field["id"]."_to"];
				if(is_array($this->filter[$field["id"]]) && !empty($this->filter[$field["id"]]) && $this->filter[$field["id"]][0] <> '' || !is_array($this->filter[$field["id"]]) && $this->filter[$field["id"]] <> '')
					$aRes[$field["id"]] = $this->filter[$field["id"]];
			}
			if(!empty($aRes))
				$_SESSION["main.interface.grid"][$this->grid_id]["filter"] = $aRes;
		}

		return $aRes;
	}

	public function Save()
	{
		CUserOptions::SetOption("main.interface.grid", $this->grid_id, $this->all_options);
	}

	public function SetColumns($columns)
	{
		$aColsTmp = explode(",", $columns);
		$aCols = array();
		foreach($aColsTmp as $col)
			if(($col = trim($col)) <> "")
				$aCols[] = $col;
		$this->all_options["views"][$this->all_options["current_view"]]["columns"] = implode(",", $aCols);
	}

	public function SetTheme($theme)
	{
		$this->all_options["theme"] = $theme;
	}

	public function SetViewSettings($view_id, $settings)
	{
		$this->all_options["views"][$view_id] = array(
			"name"=>$settings["name"],
			"columns"=>$settings["columns"],
			"sort_by"=>$settings["sort_by"],
			"sort_order"=>$settings["sort_order"],
			"page_size"=>$settings["page_size"],
			"saved_filter"=>$settings["saved_filter"],
		);
	}

	public function DeleteView($view_id)
	{
		unset($this->all_options["views"][$view_id]);
	}
	
	public function SetView($view_id)
	{
		if(!array_key_exists($view_id, $this->all_options["views"]))
			$view_id = "default";

		$this->all_options["current_view"] = $view_id;
		
		//get sorting from view, not session
		if($this->all_options["views"][$view_id]["sort_by"] <> '')
			unset($_SESSION["main.interface.grid"][$this->grid_id]["sort_by"]);

		if($this->all_options["views"][$view_id]["sort_order"] <> '')
			unset($_SESSION["main.interface.grid"][$this->grid_id]["sort_order"]);
	}
	
	public function SetFilterRows($rows, $filter_id='')
	{
		$aColsTmp = explode(",", $rows);
		$aCols = array();
		foreach($aColsTmp as $col)
			if(($col = trim($col)) <> "")
				$aCols[] = $col;
		if($filter_id <> '')
			$this->all_options["filters"][$filter_id]["filter_rows"] = implode(",", $aCols);
		else
			$this->all_options["filter_rows"] = implode(",", $aCols);
	}

	public function SetFilterSettings($filter_id, $settings)
	{
		$option = array(
			"name"=>$settings["name"],
			"fields"=>$settings["fields"],
		);

		if(isset($settings["rows"]))
		{
			$rows = $settings["rows"];
			if(is_array($rows))
			{
				$result = array();
				foreach($rows as $id)
				{
					$id = trim($id);
					if($id !== "")
					{
						$result[] = $id;
					}
				}
				$option["filter_rows"] = implode(",", $result);
			}
			elseif(is_string($settings["rows"]))
			{
				$option["filter_rows"] = $settings["rows"];
			}
		}

		$this->all_options["filters"][$filter_id] = $option;
	}

	public function DeleteFilter($filter_id)
	{
		unset($this->all_options["filters"][$filter_id]);
	}

	public function SetFilterSwitch($show)
	{
		$this->all_options["filter_shown"] = ($show == "Y"? "Y":"N");
	}

	public static function CalcDates($field_id, $aInput, &$aRes)
	{
		switch($aInput[$field_id."_datesel"])
		{
			case "today":
				$aRes[$field_id."_from"] = $aRes[$field_id."_to"] = ConvertTimeStamp();
				break;
			case "yesterday":
				$aRes[$field_id."_from"] = $aRes[$field_id."_to"] = ConvertTimeStamp(time()-86400);
				break;
			case "week":
				$day = date("w");
				if($day == 0)
					$day = 7;
				$aRes[$field_id."_from"] = ConvertTimeStamp(time()-($day-1)*86400);
				$aRes[$field_id."_to"] = ConvertTimeStamp(time()+(7-$day)*86400);
				break;
			case "week_ago":
				$day = date("w");
				if($day == 0)
					$day = 7;
				$aRes[$field_id."_from"] = ConvertTimeStamp(time()-($day-1+7)*86400);
				$aRes[$field_id."_to"] = ConvertTimeStamp(time()-($day)*86400);
				break;
			case "month":
				$aRes[$field_id."_from"] = ConvertTimeStamp(mktime(0, 0, 0, date("n"), 1));
				$aRes[$field_id."_to"] = ConvertTimeStamp(mktime(0, 0, 0, date("n")+1, 0));
				break;
			case "month_ago":
				$aRes[$field_id."_from"] = ConvertTimeStamp(mktime(0, 0, 0, date("n")-1, 1));
				$aRes[$field_id."_to"] = ConvertTimeStamp(mktime(0, 0, 0, date("n"), 0));
				break;
			case "days":
				$aRes[$field_id."_days"] = $aInput[$field_id."_days"];
				$aRes[$field_id."_from"] = ConvertTimeStamp(time() - intval($aRes[$field_id."_days"])*86400);
				$aRes[$field_id."_to"] = "";
				break;
			case "exact":
				$aRes[$field_id."_from"] = $aRes[$field_id."_to"] = $aInput[$field_id."_from"];
				break;
			case "after":
				$aRes[$field_id."_from"] = $aInput[$field_id."_from"];
				$aRes[$field_id."_to"] = "";
				break;
			case "before":
				$aRes[$field_id."_from"] = "";
				$aRes[$field_id."_to"] = $aInput[$field_id."_to"];
				break;
			case "interval":
				$aRes[$field_id."_from"] = $aInput[$field_id."_from"];
				$aRes[$field_id."_to"] = $aInput[$field_id."_to"];
				break;
		}
	}

	public static function GetThemes($path)
	{
		//color schemes
		$aColorNames = array(
			"grey"=>GetMessage("interface_grid_theme_grey"),
			"blue"=>GetMessage("interface_grid_theme_blue"),
			"brown"=>GetMessage("interface_grid_theme_brown"),
			"green"=>GetMessage("interface_grid_theme_green"),
			"lightblue"=>GetMessage("interface_grid_theme_lightblue"),
			"red"=>GetMessage("interface_grid_theme_red"),
			"lightgrey"=>GetMessage("interface_grid_theme_lightgrey"),
		);
		$arThemes = array();
		$themesPath = $_SERVER["DOCUMENT_ROOT"].$path.'/themes';
		if(is_dir($themesPath))
		{
			if($dir = opendir($themesPath))
			{
				while(($file = readdir($dir)) !== false)
				{
					if($file != '.' && $file != '..' && is_dir($themesPath."/".$file))
						$arThemes[$file] = array("theme"=>$file, "name"=>(isset($aColorNames[$file])? $aColorNames[$file]:$file));
				}
				closedir($dir);
			}
		}
		uasort($arThemes, create_function('$a, $b', 'return strcmp($a["name"], $b["name"]);'));
		return $arThemes;
	}
}
?>