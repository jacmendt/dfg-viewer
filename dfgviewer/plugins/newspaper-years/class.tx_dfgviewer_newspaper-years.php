<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2014 Alexander Bigga <alexander.bigga@slub-dresden.de>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/


/**
 * Plugin 'DFG-Viewer: Newspaper Year Listing' for the 'dfgviewer' extension.
 *
 * @author	Alexander Bigga <alexander.bigga@slub-dresden.de>
 * @copyright	Copyright (c) 2014, Alexander Bigga, SLUB Dresden
 * @package	TYPO3
 * @subpackage	tx_dfgviewer
 * @access	public
 */
class tx_dfgviewer_newspaperyears extends tx_dlf_plugin {

	public $extKey = 'dfgviewer';

	public $scriptRelPath = 'plugins/newspaper-years/class.tx_dfgviewer_newspaper-years.php';

	/**
	 * The main method of the PlugIn
	 *
	 * @access	public
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 *
	 * @return	string		The content that is displayed on the website
	 */
	public function main($content, $conf) {

		$this->init($conf);

		// Load current document.
		$this->loadDocument();

		if ($this->doc === NULL) {

			// Quit without doing anything if required variables are not set.
			return $content;

		} else {

			// Set default values if not set.
			$this->piVars['page'] = \TYPO3\CMS\Core\Utility\MathUtility::forceIntegerInRange($this->piVars['page'], 1, $this->doc->numPages, 1);

		}

		$toc = $this->doc->tableOfContents;

		// Load template file.
		if (!empty($this->conf['templateFile'])) {

			$this->template = $this->cObj->getSubpart($this->cObj->fileResource($this->conf['templateFile']), '###TEMPLATE###');

		} else {

			$this->template = $this->cObj->getSubpart($this->cObj->fileResource('EXT:dfgviewer/plugins/newspaper-years/template.tmpl'), '###TEMPLATE###');

		}

		// Get subpart templates
		$subparts['template'] = $this->template;
		$subparts['year'] = $this->cObj->getSubpart($subparts['template'], '###LISTYEAR###');

		$years = $toc[0]['children'];

		$subYearPartContent = '';

		foreach($years as $id => $year) {

			$yearLabel = empty($year['label']) ? $year['orderlabel'] : $year['label'];

			if (empty($yearLabel)) {

				// if neither order nor orderlabel is set, use the id...
				$yearLabel = (string)$id;

			}
			if (strlen($year['points']) > 0) {

				$linkConf = array (
					'useCacheHash' => 1,
					'parameter' => $this->conf['targetPid'],
					'additionalParams' => '&' . $this->prefixId . '[id]=' . urlencode($year['points']),
					'title' => $yearLabel
				);
				$yearText = $this->cObj->typoLink($yearLabel, $linkConf);

			} else {

				$yearText = $yearLabel;

			}

			$yearArray = array(
				'###YEARNAME###' => $yearText,
			);

			$subYearPartContent .= $this->cObj->substituteMarkerArray($subparts['year'], $yearArray);

		}

		// fill the week markers
		return $this->cObj->substituteSubpart($subparts['template'], '###LISTYEAR###', $subYearPartContent);

	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dfgviewer/plugins/newspaper-years/class.tx_dfgviewer_newspaper-years.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dfgviewer/plugins/newspaper-years/class.tx_dfgviewer_newspaper-years.php']);
}
