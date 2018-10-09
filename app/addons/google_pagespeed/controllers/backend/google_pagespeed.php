<?php
/**
 * HELOstore
 *
 * This source file is part of a commercial software. Only users who have purchased a valid license through
 * https://helostore.com/ and accepted to the terms of the License Agreement can install this product.
 *
 * @category   Add-ons
 * @package    HELOstore
 * @copyright  Copyright (c) 2018 HELOstore. (https://helostore.com/)
 * @license    https://helostore.com/legal/license-agreement/   License Agreement
 * @version    $Id$
 */

use Tygh\Registry;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($mode == 'clear_cache') {
    if (empty($_REQUEST['redirect_url'])) {
        $_REQUEST['redirect_url'] = 'index.index';
    }
    fn_clear_cache();

    fn_set_notification('N', __('notice'), __('cache_cleared'));

    if (empty($_REQUEST['redirect_url'])) {
        $_REQUEST['redirect_url'] = 'index.index';
    }

    return array(CONTROLLER_STATUS_REDIRECT);
}
