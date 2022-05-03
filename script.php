<?php
/**
 * @package    quantummenus
 * @author     Dmitry Tsymbal <cymbal@delo-design.ru>
 * @copyright  Copyright © 2019 Delo Design & NorrNext. All rights reserved.
 * @license    GNU General Public License version 3 or later; see license.txt
 * @link       https://www.norrnext.com
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Version;

defined('_JEXEC') or die;

/**
 * Quantummanagermedia script file.
 *
 * @package     A package name
 * @since       1.0
 */
class plgSystemQuantummenusInstallerScript
{

	/**
	 * Called after any type of action
	 *
	 * @param   string            $route    Which action is happening (install|uninstall|discover_install|update)
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function postflight($route, $adapter)
	{
		if (
			$route === 'install' &&
			version_compare((new Version())->getShortVersion(), '4.0', '>')
		)
		{
			$this->enablePlugin();
		}

	}


	protected function enablePlugin($parent)
	{
		$plugin          = new stdClass();
		$plugin->type    = 'plugin';
		$plugin->element = $parent->getElement();
		$plugin->folder  = (string) $parent->getParent()->manifest->attributes()['group'];
		$plugin->enabled = 1;

		Factory::getDbo()->updateObject('#__extensions', $plugin, ['type', 'element', 'folder']);
	}

}
