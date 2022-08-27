<?php
/**
 * @package    quantummenus
 * @author     Dmitry Tsymbal <cymbal@delo-design.ru>
 * @copyright  Copyright © 2019 Delo Design & NorrNext. All rights reserved.
 * @license    GNU General Public License version 3 or later; see license.txt
 * @link       https://www.norrnext.com
 */

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Menu\AdministratorMenuItem;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Database\DatabaseDriver;

/**
 * Quantummenus plugin.
 *
 * @package  quantummanagermedia
 * @since    1.0
 */
class plgSystemQuantummenus extends CMSPlugin
{

	protected $app;


	protected $db;


	protected $autoloadLanguage = true;


	protected $loadAdminMenu = false;


	protected $removeAdminMenu = false;


	public function onPreprocessMenuItems($context, $children)
	{
		JLoader::register('QuantummanagerHelper', JPATH_ADMINISTRATOR . '/components/com_quantummanager/helpers/quantummanager.php');

		if (
			QuantummanagerHelper::isJoomla4() &&
			QuantummanagerHelper::getParamsComponentValue('itemmenumove', false) &&
			$this->app->isClient('administrator') &&
			$context === 'com_menus.administrator.module' &&
			Factory::getUser()->authorise('core.create', 'com_quantummanager')
		)
		{
			if ($this->loadAdminMenu === false)
			{
				QuantummanagerHelper::loadLang();

				$const = 'COM_QUANTUMMANAGER';

				if (QuantummanagerHelper::getParamsComponentValue('itemmenumovefiles', false))
				{
					$const = 'COM_QUANTUMMANAGER_MENUS_FILES';
				}

				$parent = new AdministratorMenuItem ([
					'title'     => $const,
					'type'      => 'component',
					'link'      => 'index.php?option=com_quantummanager',
					'element'   => 'com_quantummanager',
					'class'     => 'class:folder-open',
					'ajaxbadge' => null,
					'dashboard' => false
				]);

				/* @var $root AdministratorMenuItem */
				$root = $children[0]->getParent();
				$root->addChild($parent);
				$this->loadAdminMenu = true;

			}
			elseif ($this->removeAdminMenu === false)
			{
				foreach ($children as $child)
				{
					if ($child->type === 'component'
						&& (int) $child->component_id === ComponentHelper::getComponent('com_quantummanager')->id)
					{
						$child->getParent()->removeChild($child);
						$this->removeAdminMenu = true;
					}
				}
			}
		}
	}
}
