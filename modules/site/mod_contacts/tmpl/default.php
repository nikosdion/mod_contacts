<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_contacts
 *
 * @copyright   (C) 2023 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\String\PunycodeHelper;

\defined('_JEXEC') or die;

$currentUser = Factory::getUser();

/**
 * @var array    $list
 * @var stdClass $item
 */
foreach ($list as $item):
    $canDo   = ContentHelper::getActions('com_contact', 'category', $item->catid);
    $canEdit = $canDo->get('core.edit') || ($canDo->get('core.edit.own') && $item->created_by === $currentUser->id);
    $tparams = $item->params;
?>

<div class="card card-body my-2" itemscope itemtype="https://schema.org/Person">
	<?php // ===== Name and image ===== ?>
    <div class="d-flex flex-column-reverse flex-md-row">
        <div class="flex-grow-1">
			<h3>
				<a href="<?= $item->link ?>">
					<span class="contact-name" itemprop="name"><?= htmlentities($item->name) ?></span>
				</a>
			</h3>

			<?php // ===== Position ===== ?>
			<?php if ($item->con_position) : ?>
			<dl class="d-flex flex-column flex-md-row gap-3">
				<dt><?= Text::_('COM_CONTACT_POSITION') ?>:</dt>
				<dd itemprop="jobTitle">
					<?= $item->con_position ?>
				</dd>
			</dl>
			<?php endif; ?>

			<?php // ===== Address ===== ?>
			<?php if (($item->address || $item->suburb  || $item->state || $item->country || $item->postcode)) : ?>
				<div class="d-flex flex-row gap-2">
					<div style="width: 2em">
						<span class="icon-address" aria-hidden="true"></span>
						<span class="visually-hidden"><?= Text::_('COM_CONTACT_ADDRESS') ?></span>
					</div>
					<div>
						<?php if ($item->address) : ?>
							<div itemprop="streetAddress">
								<?= nl2br(htmlentities($item->address), false) ?>
							</div>
						<?php endif; ?>

						<?php if ($item->suburb) : ?>
							<div itemprop="addressLocality">
								<?= htmlentities($item->suburb) ?>
							</div>
						<?php endif; ?>

						<?php if ($item->state) : ?>
							<div itemprop="addressRegion">
								<?= htmlentities($item->state) ?>
							</div>
						<?php endif; ?>

						<?php if ($item->postcode) : ?>
							<div itemprop="postalCode">
								<?= htmlentities($item->postcode) ?>
							</div>
						<?php endif; ?>

						<?php if ($item->country) : ?>
							<div itemprop="addressCountry">
								<?= htmlentities($item->country) ?>
							</div>
						<?php endif; ?>
					</div>
				</div>
			<?php endif ?>

			<?php // ===== Email ===== ?>
			<?php if ($item->email_to) : ?>
			<div class="d-flex flex-row gap-2">
				<div style="width: 2em">
					<span class="icon-envelope" aria-hidden="true"></span>
					<span class="visually-hidden"><?= Text::_('COM_CONTACT_EMAIL_LABEL') ?></span>
				</div>
				<div>
					<a href="mailto:<?= htmlentities($item->email_to) ?>">
						<?= htmlentities($item->email_to) ?>
					</a>
				</div>
			</div>
			<?php endif; ?>

			<?php // ===== Telephone ===== ?>
			<?php if ($item->telephone) : ?>
			<div class="d-flex flex-row gap-2">
				<div style="width: 2em">
					<span class="icon-phone" aria-hidden="true"></span>
					<span class="visually-hidden"><?= Text::_('COM_CONTACT_TELEPHONE') ?></span>
				</div>
				<div itemprop="telephone">
					<a href="tel:<?= htmlentities($item->telephone) ?>">
						<?= htmlentities($item->telephone) ?>
					</a>
				</div>
			</div>
			<?php endif; ?>

			<?php // ===== Fax ===== ?>
			<?php if ($item->fax) : ?>
			<div class="d-flex flex-row gap-2">
				<div style="width: 2em">
					<span class="icon-fax" aria-hidden="true"></span>
					<span class="visually-hidden"><?= Text::_('COM_CONTACT_FAX') ?></span>
				</div>
				<div itemprop="faxNumber">
					<a href="fax:<?= htmlentities($item->fax) ?>">
						<?= htmlentities($item->fax) ?>
					</a>
				</div>
			</div>
			<?php endif; ?>

			<?php // ===== Cellphone ===== ?>
			<?php if ($item->mobile) : ?>
			<div class="d-flex flex-row gap-2">
				<div style="width: 2em">
					<span class="icon-mobile" aria-hidden="true"></span>
					<span class="visually-hidden"><?= Text::_('COM_CONTACT_MOBILE') ?></span>
				</div>
				<div itemprop="telephone">
					<a href="tel:<?= htmlentities($item->mobile) ?>">
						<?= htmlentities($item->mobile) ?>
					</a>
				</div>
			</div>
			<?php endif; ?>

            <?php // ===== Website ===== ?>
            <?php if ($item->webpage) : ?>
			<div class="d-flex flex-row gap-2">
				<div style="width: 2em">
					<span class="icon-home" aria-hidden="true"></span>
					<span class="visually-hidden"><?= Text::_('COM_CONTACT_WEBPAGE') ?></span>
				</div>
				<div>
					<a href="<?= htmlentities($item->webpage) ?>" target="_blank" rel="noopener noreferrer" itemprop="url">
                        <?= htmlentities(PunycodeHelper::urlToUTF8($item->webpage)) ?>
					</a>
				</div>
			</div>
            <?php endif; ?>

            <?php // ===== Miscellaneous information ===== ?>
			<?php if ($item->misc): ?>
			<div class="d-flex flex-row gap-2 mt-2">
				<div style="width: 2em">
					<span class="icon-info-circle" aria-hidden="true"></span>
					<span class="visually-hidden"><?= Text::_('COM_CONTACT_OTHER_INFORMATION') ?></span>
				</div>
				<div>
                    <?php echo $item->misc; ?>
				</div>
			</div>
			<?php endif ?>

			<?php // ===== Download as vCard ===== ?>
			<?php if ($tparams->get('allow_vcard')) : ?>
			<div class="d-flex flex-row gap-2 mt-2">
				<div style="width: 2em">
					<span class="fa fa-address-card" aria-hidden="true"></span>
					<span class="visually-hidden"><?= Text::_('COM_CONTACT_VCARD') ?></span>
				</div>
				<div>
                    <?= Text::_('COM_CONTACT_DOWNLOAD_INFORMATION_AS') ?>
					<a href="<?= Route::_('index.php?option=com_contact&amp;view=contact&amp;id=' . $item->id . '&amp;format=vcf') ?>" download>
					<?= Text::_('COM_CONTACT_VCARD') ?></a>
				</div>
			</div>
			<?php endif; ?>

		</div>

        <?php if (!empty($item->image)): ?>
            <?= LayoutHelper::render(
                'joomla.html.image',
                [
                    'src'      => $item->image,
                    'alt'      => htmlentities($item->name),
                    'itemprop' => 'image',
                    'class'    => 'img-fluid img-thumbnail',
                    'style'    => 'max-width: min(20vw, 10em); max-height: min(20vh, 10em);',
                ]
            ); ?>
        <?php endif ?>
	</div>

</div>

<?php endforeach;