<?php
/*----------------------------------------------------------------------------------|  io.vdm.dev  |----/
			Vast Development Method
/-------------------------------------------------------------------------------------------------------/

    @package    getBible.net

    @created    3rd December, 2015
    @author     Llewellyn van der Merwe <https://getbible.net>
    @git        Get Bible <https://git.vdm.dev/getBible>
    @github     Get Bible <https://github.com/getBible>
    @support    Get Bible <https://git.vdm.dev/getBible/support>
    @copyright  Copyright (C) 2015. All Rights Reserved
    @license    GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html

/------------------------------------------------------------------------------------------------------*/

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

// No direct access to this file
defined('_JEXEC') or die();
?>
<?php if ($today->date_name): ?>
<div>
	<?php if ($params->get('show_header', 1) == 1): ?>
		<<?php echo $params->get('header', 'h2'); ?>><?php echo $today->date_name; ?></<?php echo $params->get('header', 'h2'); ?>>
	<?php endif; ?>
	<?php foreach ($times as $time): ?>
		<?php if ($params->get('show_name_header', 1) == 1): ?>
			<<?php echo $params->get('name_header', 'h3'); ?>><?php echo $today->{$time}->name; ?></<?php echo $params->get('name_header', 'h3'); ?>>
		<?php endif; ?>
		<?php if ($params->get('show_section_header', 1) == 1): ?>
			<<?php echo $params->get('section_header', 'h5'); ?>><?php echo $today->{$time}->header; ?></<?php echo $params->get('section_header', 'h5'); ?>>
		<?php endif; ?>
		<?php if ($params->get('show_body', 1) == 1): ?>
			<ul style="list-style-type: none;">
				<?php foreach ($today->{$time}->body as $row): ?>
					<li><?php echo $row; ?></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
		<?php if ($params->get('show_references', 1) == 1): ?>
			<p><?php echo implode('; ', $today->{$time}->references); ?></p>
		<?php endif; ?>
		<?php if ($today->{'comments.' . $time}): ?>
			<?php echo $today->{'comments.' . $time}; ?>
		<?php endif; ?>
	<?php endforeach; ?>
	<?php if ($params->get('link', 1) == 1): ?>
		<a href="https://t.me/s/<?php echo $today->telegram; ?>" target="_blank">
			<?php if ($params->get('show_date_footer', 0) == 1): ?>
				<?php echo $today->date_name; ?>
			<?php else: ?>
				<?php echo Text::_('MOD_DAILYLIGHT_DAILY_LIGHT'); ?>
			<?php endif; ?>
		</a>
	<?php else: ?>
		<?php if ($params->get('show_date_footer', 0) == 1): ?>
			<p><?php echo $today->date_name; ?></p>
		<?php endif; ?>
	<?php endif; ?>
</div>
<?php elseif ($params->get('type', 1) == 2 && ($today->{'telegram.morning'} || $today->{'telegram.evening'})): ?>
<div>
	<?php $counter = 1; ?>
	<?php foreach ($times as $time): ?>
		<?php if ($today->{'telegram.' . $time}): ?>
			<?php if ($counter == 2): ?>
				<br /><br />
			<?php endif; ?>
			<?php echo $today->{'telegram.' . $time}; ?>
			<?php if ($today->{'comments.' . $time}): ?>
				<?php echo $today->{'comments.' . $time}; ?>
			<?php endif; ?>
			<?php $counter++; ?>
		<?php endif; ?>
	<?php endforeach; ?>
</div>
<?php else: ?>
	<?php echo Text::_('MOD_DAILYLIGHT_THERE_WAS_AN_ERROR_LOADING_THE_DAILY_LIGHT_PLEASE_TRY_AGAIN_LATTER'); ?>
<?php endif; ?>
