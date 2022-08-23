<?php
namespace Wizard\Additional;


/**
 * Class CreateMultiLvlArray
 * @package Wizard\Additional
 */
Class CreateMultiLvlArray {
	protected $lastKey;
	protected $countSubArrayItems;
	protected $arSubArrayItems;
	protected $childrenSectionsName;
	protected $depthLvlName;

	/**
	 * @param $array
	 * @param string $childrenSectionsName
	 * @param string $depthLvlName
	 * @return array
	 */
	public function get($array, $childrenSectionsName = 'SECTIONS', $depthLvlName = 'DEPTH_LEVEL'){
		$this->childrenSectionsName = $childrenSectionsName;
		$this->depthLvlName = $depthLvlName;
		$this->lastKey = 0;
		$this->arSubArrayItems = $array;
		$this->countSubArrayItems = count($this->arSubArrayItems);
		return $this->returnSubArray();
	}

	/**
	 * @return array
	 */
	protected function returnSubArray ()
	{
		$k = $this->lastKey;
		$arSubMenu = [];

		while ($this->countSubArrayItems > $k) {
			if (!empty($arSubMenu) && count($arSubMenu) != 0 && $arSubMenu[0][$this->depthLvlName] < $this->arSubArrayItems[$k][$this->depthLvlName]) {
				$this->lastKey = $k;
				$arSubMenu[count($arSubMenu) - 1][$this->childrenSectionsName] = $this->returnSubArray();
				$k += $this->countMultiArray($arSubMenu[count($arSubMenu) - 1][$this->childrenSectionsName]);
				continue;
			} elseif (!empty($arSubMenu) && count($arSubMenu) != 0 && $arSubMenu[0][$this->depthLvlName] > $this->arSubArrayItems[$k][$this->depthLvlName]) {
				return $arSubMenu;
			} else {
				$arSubMenu[] = $this->arSubArrayItems[$k];
			}
			$k++;
		}
		return $arSubMenu;
	}

	/**
	 * @param $array
	 * @return int
	 */
	public function countMultiArray($array){
		$count = 0;
		foreach($array as $arItem){
			if (isset($arItem[$this->childrenSectionsName]) && !empty($arItem[$this->childrenSectionsName])){
				$count += $this->countMultiArray($arItem[$this->childrenSectionsName]);
			}
			$count++;
		}
		return $count;
	}
}