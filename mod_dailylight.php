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

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Include the helper functions only once
JLoader::register('ModDailyLightHelper', __DIR__ . '/helper.php');

// get the daily light
$today = new ModDailyLightHelper($params);

// set the time of day
$target = $params->get('time_of_day', 3);
$times = ($target == 3) ? ['morning', 'evening']:(($target == 1) ? ['morning']:['evening']);

// get the module class sfx (local)
$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx', ''), ENT_COMPAT, 'UTF-8');

// load the default Tmpl
require JModuleHelper::getLayoutPath('mod_dailylight', $params->get('layout', 'default'));
