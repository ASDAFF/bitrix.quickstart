<?php
/**
 * KDAPHPExcel
 *
 * Copyright (c) 2006 - 2013 KDAPHPExcel
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category	KDAPHPExcel
 * @package		KDAPHPExcel_Chart
 * @copyright	Copyright (c) 2006 - 2013 KDAPHPExcel (http://www.codeplex.com/KDAPHPExcel)
 * @license		http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version		1.7.9, 2013-06-02
 */


/**
 * KDAPHPExcel_Chart_PlotArea
 *
 * @category	KDAPHPExcel
 * @package		KDAPHPExcel_Chart
 * @copyright	Copyright (c) 2006 - 2013 KDAPHPExcel (http://www.codeplex.com/KDAPHPExcel)
 */
class KDAPHPExcel_Chart_PlotArea
{
	/**
	 * PlotArea Layout
	 *
	 * @var KDAPHPExcel_Chart_Layout
	 */
	private $_layout = null;

	/**
	 * Plot Series
	 *
	 * @var array of KDAPHPExcel_Chart_DataSeries
	 */
	private $_plotSeries = array();

	/**
	 * Create a new KDAPHPExcel_Chart_PlotArea
	 */
	public function __construct(KDAPHPExcel_Chart_Layout $layout = null, $plotSeries = array())
	{
		$this->_layout = $layout;
		$this->_plotSeries = $plotSeries;
	}

	/**
	 * Get Layout
	 *
	 * @return KDAPHPExcel_Chart_Layout
	 */
	public function getLayout() {
		return $this->_layout;
	}

	/**
	 * Get Number of Plot Groups
	 *
	 * @return array of KDAPHPExcel_Chart_DataSeries
	 */
	public function getPlotGroupCount() {
		return count($this->_plotSeries);
	}

	/**
	 * Get Number of Plot Series
	 *
	 * @return integer
	 */
	public function getPlotSeriesCount() {
		$seriesCount = 0;
		foreach($this->_plotSeries as $plot) {
			$seriesCount += $plot->getPlotSeriesCount();
		}
		return $seriesCount;
	}

	/**
	 * Get Plot Series
	 *
	 * @return array of KDAPHPExcel_Chart_DataSeries
	 */
	public function getPlotGroup() {
		return $this->_plotSeries;
	}

	/**
	 * Get Plot Series by Index
	 *
	 * @return KDAPHPExcel_Chart_DataSeries
	 */
	public function getPlotGroupByIndex($index) {
		return $this->_plotSeries[$index];
	}

	/**
	 * Set Plot Series
	 *
	 * @param [KDAPHPExcel_Chart_DataSeries]
	 */
	public function setPlotSeries($plotSeries = array()) {
		$this->_plotSeries = $plotSeries;
	}

	public function refresh(KDAPHPExcel_Worksheet $worksheet) {
	    foreach($this->_plotSeries as $plotSeries) {
			$plotSeries->refresh($worksheet);
		}
	}

}
