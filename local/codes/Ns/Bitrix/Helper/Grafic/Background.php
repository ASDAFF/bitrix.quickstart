<?
namespace Ns\Bitrix\Helper\Grafic;

/**
*
*/
class Background extends \Ns\Bitrix\Helper\HelperCore
{
	const MENU_IBLOCK_ID = 12;
	const BACKGROUND_IBLOCK_ID = 38;

	private $defaultSelect = array(
					"PROPERTY_PATTERN_PICTURE",
					"PROPERTY_WIDTH_PICTURE",
					"PROPERTY_LEFT_PICTURE",
					"PROPERTY_RIGHT_PICTURE",
					"PROPERTY_NONCHANGED_PICTURE",
					"PROPERTY_HEX_COLOR",
					"PROPERTY_BACKGROUND_PICTURE",
					"PROPERTY_BODY_PATTERN",
                    "PROPERTY_H1_COLOR",
					);

    private function findBackgroundItem()
    {
        if (!$this->backgroundItem)
        {
            \CModule::IncludeModule('iblock');
            $tmp = explode("/", $this->getUrl());
            $links[] = "/";
            foreach ($tmp as $chunk)
            {
                if ($chunk)
                {
                    $links[] = end($links) . $chunk . "/";
                }
            }
            unset($links[0]);
            $Section = \CIBlockSection::GetList(
                array("DEPTH_LEVEL" => "DESC"),
                array(
                    "IBLOCK_ID" => self::MENU_IBLOCK_ID,
                    "ACTIVE" => "Y",
                    "UF_MENU_LINK" => $links,
                    "!UF_BACKGROUND_ITEM" => false
                ),
                false,
                array(
                    "ID",
                    "UF_BACKGROUND_ITEM",
                    "UF_NOT_INHERRIT",
                    "UF_MENU_LINK"
                ),
                array("nPageSize" => 1)
            )->Fetch();
            $currentLink = end($links);
            if ($currentLink != $Section["UF_MENU_LINK"]) {
                if (!$Section["UF_NOT_INHERRIT"]) {
                    $this->backgroundItem = ($Section["UF_BACKGROUND_ITEM"]) ? $Section["UF_BACKGROUND_ITEM"] : False;
                } else {
                    $this->backgroundItem = false;
                }
            } else {
                $this->backgroundItem = ($Section["UF_BACKGROUND_ITEM"]) ? $Section["UF_BACKGROUND_ITEM"] : False;
            }
        }
        return $this->backgroundItem;
    }

    public function getStyle($type="site")
    {
        $this->type = $type;
        $this->findBackgroundItem();
        return $this->getCss();
    }

	private function getCss()
	{
		if ($this->backgroundItem === false)
		{
			$filter = array("IBLOCK_ID" => self::BACKGROUND_IBLOCK_ID, "!PROPERTY_IS_DEFAULT" => false);
		}
		else
		{
			$filter = array("ID" => $this->backgroundItem);
		}
		$this->Item = \CIBlockElement::GetList(
			false,
			$filter,
			false,
			array("nTopCount" => 1),
			$this->defaultSelect
			)->Fetch();
        if ($this->type == "site")
        {
            return $this->cssSite();
        }
        elseif ($this->type == "body")
        {
            return $this->cssBody();
        }
        elseif ($this->type == "h1")
        {
            return $this->cssH1();
        }
	}

	/**
	 * @param array $this->Item Fetched from CDBResult this->Item
	 */
	private function cssSite()
	{
		if ($this->Item["PROPERTY_BACKGROUND_PICTURE_VALUE"])
		{
			$css = "background: url(" . htmlentities(\CFile::GetPath($this->Item["PROPERTY_BACKGROUND_PICTURE_VALUE"])) . ") ";
			if ($this->Item["PROPERTY_HEX_COLOR_VALUE"])
			{
				$css .= $this->Item["PROPERTY_HEX_COLOR_VALUE"] . " ";
			}
			if ($this->Item["PROPERTY_PATTERN_PICTURE_VALUE"])
			{
				$css .= "repeat; ";
			}
			elseif ($this->Item["PROPERTY_WIDTH_PICTURE_VALUE"])
			{
				$css .= "no-repeat; -moz-background-size: 100%; -webkit-background-size: 100%; -o-background-size: 100%; background-size: 100%; ";
			}
			elseif ($this->Item["PROPERTY_NONCHANGED_PICTURE_VALUE"])
			{
				$css .= "top center no-repeat; ";
			}
            elseif ($this->Item["PROPERTY_RIGHT_PICTURE_VALUE"])
            {
                $css .= "top right no-repeat; ";
            }
            elseif ($this->Item["PROPERTY_LEFT_PICTURE_VALUE"])
            {
                $css .= "top left no-repeat; ";
            }
		}
		else
		{
			if ($this->Item["PROPERTY_HEX_COLOR_VALUE"])
			{
				$css .= "background: " . $this->Item["PROPERTY_HEX_COLOR_VALUE"] . "; ";
			}
		}
		return $css;
	}

    private function cssBody()
    {
        if ($this->Item["PROPERTY_BODY_PATTERN_VALUE"])
        {
            $css = "background: url(" . htmlentities(\CFile::GetPath($this->Item["PROPERTY_BODY_PATTERN_VALUE"])) . ") repeat";
        }
        else
        {
            $css = "";
        }
        return $css;
    }

    private function cssH1()
    {
        if ($this->Item["PROPERTY_H1_COLOR_VALUE"])
        {
            $css = 'style="color: ' . $this->Item["PROPERTY_H1_COLOR_VALUE"] . ';"';
        }
        else
        {
            $css = "";
        }
        return $css;
    }

}


?>