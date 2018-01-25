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

    $settings = Registry::get('addons.google_pagespeed');
    $adminUrl = $settings['admin_url'];
    if (empty($adminUrl)) {
        fn_set_notification('E', __('error'), __('google_pagespeed.admin_url_not_set'));

        return array(CONTROLLER_STATUS_REDIRECT);
    }

    $message = array();
    $result = fn_clear_cache();
    $result2 = fn_rm(Registry::get('config.dir.cache_templates'));
    $message[] = 'CS-Cart cache: ' . ($result2 != false ? 'OK' : 'FAIL');;
    $message[] = 'CS-Cart templates: ' . ($result2 == true ? 'OK' : 'FAIL');

    if (function_exists('opcache_reset')) {
        if (opcache_reset()) {
            $message[] = 'PHP/opcache: OK';
        } else {
            $message[] = 'PHP/opcache: FAIL';
        }
    }

    $ch = curl_init($adminUrl . "/cache?purge=*");
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    $curlError = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($response === false) {
        $message[] = "PageSpeed: FAIL (${$curlError})";
    } else {
        if ($httpCode === 200) {
            $response = (! is_string($response) ? json_encode($response) : $response);
            $message[] = "PageSpeed: OK (${response})";
        } else {
            $message[] = "PageSpeed: FAIL (httpCode=${httpCode})";
        }
    }

    $message = implode("<br />", $message);
    fn_set_notification('N', __('notice'), 'Results: <br />' . $message, 'K');

    return array(CONTROLLER_STATUS_REDIRECT);
}