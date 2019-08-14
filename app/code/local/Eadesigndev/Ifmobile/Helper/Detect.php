<?php

/**
 * EaDesgin
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@eadesign.ro so we can send you a copy immediately.
 *
 * @category    custom_ext_code
 * @copyright   Copyright (c) 2008-2016 EaDesign by Eco Active S.R.L.
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

require_once Mage::getBaseDir() . DS . 'lib' . DS . 'Eadesigndev' . DS . 'Ifmobile' . DS . 'Mobile_Detect.php';

class Eadesigndev_Ifmobile_Helper_Detect extends Mobile_Detect
{


    /**
     * If the users wats a mobile version we have a cookie.
     * @return bool
     */
    public function isMobile()
    {

        if (Mage::getModel('core/cookie')->get('wantsdesktop')) {
            return false;
        }

        return parent::isMobile();

    }

    /**
     * The mobile link will be shown only for the mobile version.
     * @return bool
     */
    public function wantsMobile()
    {
        if (parent::isMobile()) {
            return true;
        }

        return false;
    }


}