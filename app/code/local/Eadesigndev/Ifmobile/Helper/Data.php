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
class Eadesigndev_Ifmobile_Helper_Data extends Mage_Core_Helper_Abstract
{

    CONST  MOBILLE_CSS = 'mobile_css';
    CONST  MOBILLE_JS = 'mobile_js';

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

    /**
     * If module is active.
     * @return bool
     */
    public function isActive()
    {
        return Mage::getStoreConfig('ifmob_settings/ifmob_opt/active');
    }

    /**
     * @return bool
     */
    public function cssDesk()
    {
        return Mage::getStoreConfig('ifmob_settings/ifmob_opt/desk_css');
    }

    /**
     * @return string
     */
    public function getDeskTheme()
    {
        return Mage::getDesign()->getTheme('frontend') . '/' . $this->cssDesk();
    }

    /**
     * @return bool
     */
    public function cssMob()
    {
        return Mage::getStoreConfig('ifmob_settings/ifmob_opt/mob_css');
    }

    /**
     * @return string
     */
    public function getMobTheme()
    {
        return Mage::getDesign()->getTheme('frontend') . '/' . $this->cssMob();
    }

    /**
     * @return bool
     */
    public function jsDesk()
    {
        return Mage::getStoreConfig('ifmob_settings/ifmob_opt/desk_js');
    }

    /**
     * @return string
     */
    public function getJsDeskTheme()
    {
        return Mage::getDesign()->getTheme('frontend') . '/' . $this->jsDesk();
    }

    /**
     * @return bool
     */
    public function jsMob()
    {
        return Mage::getStoreConfig('ifmob_settings/ifmob_opt/mob_js');
    }

    /**
     * @return string
     */
    public function getJsMobTheme()
    {
        return Mage::getDesign()->getTheme('frontend') . '/' . $this->jsMob();
    }

    /**
     * If all the needs are meet we enable the system to load different js and css.
     * @return bool
     */
    public function applyMobileHtml()
    {
        $existsCssDesk = Mage::getSingleton('core/design_package')->getSkinBaseDir() . DS . $this->cssDesk();
        $existsJsDesk = Mage::getSingleton('core/design_package')->getSkinBaseDir() . DS . $this->jsDesk();
        $existsCss = Mage::getSingleton('core/design_package')->getSkinBaseDir() . DS . $this->cssMob();
        $existsJs = Mage::getSingleton('core/design_package')->getSkinBaseDir() . DS . $this->jsMob();


        if ($this->isActive()
            && Mage::helper('ifmobile/detect')->isMobile()
            && file_exists($existsCss)
            && file_exists($existsJs)
            && file_exists($existsCssDesk)
            && file_exists($existsJsDesk)
        ) {
            return true;
        }

        return false;

    }
}