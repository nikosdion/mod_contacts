<?php

/**
 * @package     Joomla.Site
 * @subpackage  mod_contacts
 *
 * @copyright   (C) 2023 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Module\Contacts\Site\Dispatcher;

use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Dispatcher\AbstractModuleDispatcher;
use Joomla\CMS\Helper\HelperFactoryAwareInterface;
use Joomla\CMS\Helper\HelperFactoryAwareTrait;

// phpcs:disable PSR1.Files.SideEffects
\defined('JPATH_PLATFORM') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Dispatcher class for mod_articles_popular
 *
 * @since  __DEPLOY_VERSION__
 */
class Dispatcher extends AbstractModuleDispatcher implements HelperFactoryAwareInterface
{
    use HelperFactoryAwareTrait;

    /**
     * Returns the layout data.
     *
     * @return  array
     *
     * @since   __DEPLOY_VERSION__
     */
    protected function getLayoutData()
    {
        $data = parent::getLayoutData();

        /** @var SiteApplication $app */
        $app = $data['app'];
        $app->getLanguage()->load('com_contact');

        $data['list'] = $this->getHelperFactory()
            ->getHelper('ContactsHelper', $data)
            ->getContacts($data['params'], $data['app']);

        return $data;
    }
}
