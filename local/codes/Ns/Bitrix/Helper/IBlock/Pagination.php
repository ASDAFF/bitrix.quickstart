<?
namespace Ns\Bitrix\Helper\IBlock;

/**
*
*/
class Pagination extends \Ns\Bitrix\Helper\HelperCore
{
	const BIG_PAGINATION_SIDE_PAGES_VALUE = 3;
	const MAX_SHORT_PAGES = 10;

	private $perPage;
	private $quantity;
	private $page;
	private $pages;
	private $prevPage;
	private $nextPage;

	function __construct() {}

	public function perPage($perPage = 20)
	{
		$this->perPage = $perPage;
		return $this;
	}

	public function itemsQuantity($quantity = false)
	{
		if ($quantity)
		{
			$this->quantity = $quantity;
			$this->pages = ceil($this->quantity/$this->perPage);
		}
		return $this;
	}

	public function withCurrentPage($page = false)
	{
		if (!$page)
		{
			if (isset($_GET["page"]))
			{
				$this->page = intval($_GET["page"]);
			}
			else
			{
				$this->page = 1;
			}
		}
		elseif (intval($page))
		{
			$this->page = intval($page);
		}
		else
		{
			$this->page = 1;
		}
		if ($this->pages > 1)
		{
			$this->prevPage = ($this->page-1 >= 1) ? $this->page-1 : false;
			$this->nextPage = ($this->page+1 <= $this->pages) ? 1+$this->page : false;
		}
		return $this;
	}

	public function render()
	{
		if ($this->pages == 1)
		{
			return false;
		}
		$html = '';
		/**
		 * Begin of pagination
		 */
		$html = <<<HTML
		<ul class="pagination">
HTML;
		if ($this->prevPage)
		{
			$url = $this->getUrl($this->prevPage);
			$html .= <<<HTML
				<li><a href="$url" class="btn_stl alter prev">Назад</a></li>
HTML;
		}
		if ($this->pages <= self::MAX_SHORT_PAGES)
		{
			$html .= $this->short();
		}
		else
		{
			$html .= $this->long();
		}
		/**
		 * End of pagination
		 */
		if ($this->nextPage)
		{
			$url = $this->getUrl($this->nextPage);
			$html .= <<<HTML
				<li><a href="$url" class="btn_stl alter next">Вперед</a></li>
HTML;
		}
		$html .= <<<HTML
            </ul>
HTML;
		return $html;
	}

	/**
	 * Render default pagination if < 10 pages
	 */
	private function short()
	{
		return $this->renderSomePages($this->pages);

	}

	/**
	 * Render pagination with input if > 10 pages
	 */
	private function long()
	{
		$html = '';
		$value = ($this->page > 3 && $this->page < $this->pages - self::BIG_PAGINATION_SIDE_PAGES_VALUE) ? $this->page : '...';
		$url = $this->getUrl("", true);
		$html .= $this->renderSomePages(self::BIG_PAGINATION_SIDE_PAGES_VALUE);
		$html .= <<<HTML
				<li>
					<input maxlength="2" class="text_input pagination-action" type="text" data-placeholder="$value" value="$value" style="width: 35px;">
					<button class="search_btn pagination-action-button" data-url="$url" type="submit" style="display: none;"><i class="icon ico_loupe vert_algn_top" style="background: url(/images/pagination_input_arrow.png) no-repeat right; "></i></button>

				</li>
HTML;

		$html .= $this->renderSomePages($this->pages, $this->pages - self::BIG_PAGINATION_SIDE_PAGES_VALUE + 1);
		return $html;
	}

	/**
	 * render some pages
	 */
	private function renderSomePages($till, $from = 1)
	{
		for ($page = $from; $page <= $till; $page++)
		{
			$url = $this->getUrl($page);
			if ($page != $this->page)
			{
				$html .= <<<HTML
				<li><a href="$url" class="invers_link it_name"><span class="solid">$page</span></a></li>
HTML;
			}
			else
			{
				$html .= <<<HTML
			    <li><span class="invers_link it_name current">$page<img class="pagination_back" src="/images/pagination_back.png"></span></li>
HTML;
			}
		}
		return $html;
	}

	private function getUrl($page, $forJS = false)
	{
		return $this->getListUrl() . $this->getCode() . "/page_" . $page . "/";
		// global $APPLICATION;
		// prent($APPLICATION->GetCurPage());
		// if (strpos($_SERVER["REQUEST_URI"], "page_") !== false)
		// {
		// 	$tmp = explode("/", $_SERVER["REQUEST_URI"]);

		// 	$uri = implode("/", $tmp);
		// 	$url = str_replace("page_" . $this->page, "page_" . $page, $uri);
		// }
		// else
		// {
		// 	if (strpos($_SERVER["REQUEST_URI"], $this->getCode()) !== false)
		// 	{
		// 		$url = $APPLICATION->GetCurPageParam("", array("clear_cache", "filter_type")) . "page_" . $page . "/";
		// 	}
		// 	else
		// 	{
		// 		$url = $this->getCode() . "/page_" . $page . "/";
		// 	}
		// }
		// return $url;
	}
}



?>