<?php
class Pagination
{
	public $hits = 2;
	public $itemCount;
	public $maxPages;

	public function __construct($perPage, $itemCount)
	{
		$this->perPage = $perPage;
		$this->itemCount = $itemCount;
		$this->maxPages = ceil($itemCount / $perPage);
	}

	/**
	 * Use the current querystring as base, modify it according to $options and return the modified query string.
	 *
	 * @param array $options to set/change.
	 * @param string $prepend this to the resulting query string
	 * @return string with an updated query string.
	 */
	public function getQueryString($options, $prepend='?') {
		// parse query string into array
		$query = array();
		parse_str($_SERVER['QUERY_STRING'], $query);

		// Modify the existing query string with new options
		$query = array_merge($query, $options);

		// Return the modified querystring
		return $prepend . http_build_query($query);
	}

	/**
	* Create navigation among pages.
	*
	* @param integer $page current page.
	* @param integer $max number of pages. 
	* @param integer $min is the first page number, usually 0 or 1. 
	* @return string as a link to this page.
	*/
	public function getPageNavigation($page, $min=1) 
	{
		$pClass = 'class="pagination-link"';

		$nav  = "<a {$pClass} href='" . $this->getQueryString(array('page' => $min)) . "'>&lt;&lt;</a> ";
		$nav .= "<a {$pClass} href='" . $this->getQueryString(array('page' => ($page > $min ? $page - 1 : $min) )) . "'>&lt;</a> ";

		for($i = $min; $i <= $this->maxPages; $i++) 
		{
			$class = 'class="pagination-link"';

			if ($page == $i)
				$class = 'class="pagination-link pagination-active"';

			$nav .= "<a {$class} href='" . $this->getQueryString(array('page' => $i)) . "'>$i</a> ";
		}

		$nav .= "<a {$pClass} href='" . $this->getQueryString(array('page' => ($page < $this->maxPages ? $page + 1 : $this->maxPages) )) . "'>&gt;</a> ";
		$nav .= "<a {$pClass} href='" . $this->getQueryString(array('page' => $this->maxPages)) . "'>&gt;&gt;</a> ";

		return $nav;
	}

	/**
	* Create links for hits per page.
	*
	* @param array $hits a list of hits-options to display.
	* @return string as a link to this page.
	*/
	public function getHitsPerPage($hits) 
	{
		$nav = "Visar ";

		$nav .= '<select onchange="this.options[this.selectedIndex].value && (window.location = this.options[this.selectedIndex].value);">';

		foreach($hits as $val) 
		{
			$selected = '';
			if ($this->perPage == $val) $selected = 'selected';

			$nav .= '<option '.$selected.' value="'. $this->getQueryString(array('hits' => $val)) . '">' . $val . '</option>';
		} 

		$nav .= '</select>';

		$nav .= " träffar av {$this->itemCount}";

		return $nav;
	}

	/**
	 * Check if page has items otherwise redirect to the first
	 * 
	 * @return void
	 */
	public function checkForItems($page)
	{
		if ($page > $this->maxPages)
		{
			header("location: " . $this->getQueryString(['page' => 1]));
		}
	}

	/**
	 * Alternative method for pagination, used when the results have already been fetched as array
	 *
	 * @param array $items
	 * @param int $currentPage
	 * @param int $perPage
	 * @return array
	 */
    public static function paginateResults($items, $currentPage, $perPage)
    {
        $offset = ($currentPage - 1) * $perPage;

        return array_slice($items, $offset, $perPage);
    }	

}