<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_contacts
 *
 * @copyright   (C) 2023 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Module\Contacts\Site\Helper;

use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Cache\CacheControllerFactoryInterface;
use Joomla\CMS\Cache\Controller\OutputController;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Factory\MVCFactory;
use Joomla\CMS\Router\Route;
use Joomla\Component\Contact\Site\Helper\RouteHelper;
use Joomla\Component\Contact\Site\Model\CategoryModel;
use Joomla\Registry\Registry;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Helper for mod_contacts
 *
 * @since   __DEPLOY_VERSION__
 */
final class ContactsHelper
{
    /**
     * The module instance
     *
     * @var   \stdClass
     * @since __DEPLOY_VERSION__
     */
    protected $module;

    /**
     * Constructor
     *
     * @param   array  $config  An optional associative array of configuration options
     *
     * @since   __DEPLOY_VERSION__
     */
    public function __construct(array $config = [])
    {
        $this->module = $config['module'];
    }

    /**
     * Get a list of contacts in the configured category
     *
     * @param   Registry         $moduleParams  The module parameters
     * @param   SiteApplication  $app           The Site application object
     *
     * @return  array  The list of contacts to display
     *
     * @throws  \Exception
     * @since   __DEPLOY_VERSION__
     */
    public function getContacts(Registry $moduleParams, SiteApplication $app)
    {
        $cacheKey = md5(serialize([$moduleParams->toString(), $this->module->module, $this->module->id]));

        /** @var OutputController $cache */
        $cache = Factory::getContainer()->get(CacheControllerFactoryInterface::class)
            ->createCacheController('output', ['defaultgroup' => 'mod_contacts']);

        if (!$cache->contains($cacheKey)) {
            /** @var MVCFactory $mvcFactory */
            $mvcFactory = $app->bootComponent('com_contact')->getMVCFactory();

            /** @var CategoryModel $catModel */
            $catModel = $mvcFactory->createModel('Category', 'Site', ['ignore_request' => true]);

            // Set application parameters in model
            $appParams = $app->getParams();
            $catModel->setState('params', $appParams);

            // Set list limits
            $catModel->setState('list.start', 0);
            $catModel->setState('list.limit', (int) $moduleParams->get('count', 5));

            // This module does not use tags data
            $catModel->setState('load_tags', false);

            // Set the category ID
            $catModel->setState('category.id', $moduleParams->get('catid', []));

            // Filter by language
            $catModel->setState('filter.language', $app->getLanguageFilter());

            // Ordering: featured first, then the specified ordering
            $catModel->setState('list.ordering', 'featuredordering');

            $items = array_map(
                function (object $item) {
                    $item->link = Route::_(RouteHelper::getContactRoute($item->id, $item->catid, $item->language));

                    return $item;
                },
                $catModel->getItems()
            );

            $cache->store($items, $cacheKey);

            return $items;
        }

        return $cache->get($cacheKey);
    }
}