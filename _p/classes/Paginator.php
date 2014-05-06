<?
/**
 * This class, given the total number of pages, the current page
 * and the number of pages to show, calculates which page will be the first
 * in the list, and which one will be the last.
 * Examples:
 * <pre>
 *   total: 20
 *   current: 4
 *   to show: 11
 *   ->first: 1
 *   ->last: 11
 *
 *   total: 20
 *   current: 7
 *   to show: 11
 *   ->first: 2
 *   ->last: 12
 *
 *   total: 20
 *   current: 19
 *   to show: 11
 *   ->first: 10
 *   ->last: 20
 * </pre>
 *
 * @package Common
 */
class Paginator {

	/**#@+
	 * @access private
	 */
	var $first;
	var $last;
	/**#@-*/

	/**
	 * Constructs a Paginator.
	 * @param integer $total_pages the total number of pages available
	 * @param integer $current_page the current page shown
	 * @param integer $pages_to_show the pages to show in the list
	 */
	function Paginator($total_pages, $current_page, $pages_to_show) {
		if ($total_pages <= $pages_to_show) {
			$this->first = 1;
			$this->last = $total_pages;
		} else {
			$first_page = $current_page - ceil(($pages_to_show - 1) / 2);
			$last_page = $current_page + floor(($pages_to_show - 1) / 2);
			if ($first_page < 1) {
				$last_page += -$first_page + 1;
				$first_page = 1;
			}
			if ($last_page > $total_pages) {
				$first_page += ($total_pages - $last_page);
				$last_page = $total_pages;
			}
			$this->first = $first_page;
			$this->last = $last_page;
		}
	}

	/**
	 * Returns the last page in the list.
	 * @return integer the last page in the list
	 */
	function getLastPage() {
		return $this->last;
	}

	/**
	 * Returns the first page in the list.
	 * @return integer the first page in the list
	 */
	function getFirstPage() {
		return $this->first;
	}

}
?>