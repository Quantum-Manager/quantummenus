<?php

namespace Joomla\Plugin\System\QuantumMenus\Extension;

/**
 * @package    quantummenus
 * @author     Dmitry Tsymbal <cymbal@delo-design.ru>
 * @copyright  Copyright © 2019 Delo Design & NorrNext. All rights reserved.
 * @license    GNU General Public License version 3 or later; see license.txt
 * @link       https://www.norrnext.com
 */

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Event\Menu\PreprocessMenuItemsEvent;
use Joomla\CMS\Factory;
use Joomla\CMS\Menu\AdministratorMenuItem;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Component\QuantumManager\Administrator\Helper\QuantummanagerHelper;
use Joomla\Event\SubscriberInterface;

class QuantumMenus extends CMSPlugin implements SubscriberInterface
{

	protected $app;

	protected $db;

	protected $autoloadLanguage = true;

	protected $loadAdminMenu = false;

	protected $removeAdminMenu = false;

	protected $install_quantummanager = false;

	public function __construct(&$subject, $config = [])
	{
		parent::__construct($subject, $config);

		if (file_exists(JPATH_SITE . '/administrator/components/com_quantummanager/services/provider.php'))
		{
			$this->install_quantummanager = true;
		}
	}

	public static function getSubscribedEvents(): array
	{
		return [
			'onPreprocessMenuItems' => 'onPreprocessMenuItems',
		];
	}

	public function onPreprocessMenuItems(PreprocessMenuItemsEvent $event): void
	{
		if (!$this->install_quantummanager)
		{
			return;
		}

		$context  = $event->getContext();
		$children = $event->getItems();

		if (
			QuantummanagerHelper::getParamsComponentValue('itemmenumove', false)
			&& $this->app->isClient('administrator')
			&& $context === 'com_menus.administrator.module'
			&& Factory::getApplication()->getIdentity()->authorise('core.create', 'com_quantummanager')
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

				$parent = new AdministratorMenuItem([
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
					if (
						$child->type === 'component'
						&& $child->component_id === ComponentHelper::getComponent('com_quantummanager')->id
					)
					{
						$child->getParent()->removeChild($child);
						$this->removeAdminMenu = true;
					}
				}
			}
		}
	}
}
