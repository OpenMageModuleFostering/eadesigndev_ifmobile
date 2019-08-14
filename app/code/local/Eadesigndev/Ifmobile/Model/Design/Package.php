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
 * @copyright   Copyright (c) 2008-2015 EaDesign by Eco Active S.R.L.
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
class Eadesigndev_Ifmobile_Model_Design_Package extends Mage_Core_Model_Design_Package
{


    /**
     * Merge specified javascript files and return URL to the merged file on success
     *
     * @param $files
     * @return string
     */
    public function getMergedJsUrl($files)
    {

        $mobile = Mage::helper('ifmobile/detect')->isMobile();


        $targetFilename = md5(implode(',', $files)) . '.js';
        $targetDir = $this->_initMergerDir('js');

        if ($mobile) {
            $files = str_replace(Mage::helper('ifmobile')->getJsDeskTheme(), Mage::helper('ifmobile')->getJsMobTheme(), $files);
            $targetFilename = md5(implode(',', $files)) . '.js';
            $targetDir = $this->_initMergerDir('js_mob');
        }

//        echo '<pre>';
//        print_r($files);
//        exit();

        if (!$targetDir) {
            return '';
        }
        if ($this->_mergeFiles($files, $targetDir . DS . $targetFilename, false, null, 'js')) {
            if ($mobile) {
                return Mage::getBaseUrl('media', Mage::app()->getRequest()->isSecure()) . 'js_mob/' . $targetFilename;
            }
            return Mage::getBaseUrl('media', Mage::app()->getRequest()->isSecure()) . 'js/' . $targetFilename;
        }
        return '';
    }


    /**
     * Merge specified css files and return URL to the merged file on success
     *
     * @param $files
     * @return string
     */
    public function getMergedCssUrl($files)
    {
        $mobile = Mage::helper('ifmobile/detect')->isMobile();

        // secure or unsecure
        $isSecure = Mage::app()->getRequest()->isSecure();

        $mergerDir = $isSecure ? 'css_secure' : 'css';

        if ($mobile) {
            $mergerDir = $isSecure ? 'css_secure_mob' : 'css_mob';
            $files = str_replace(Mage::helper('ifmobile')->getDeskTheme(), Mage::helper('ifmobile')->getMobTheme(), $files);
        }

        $targetDir = $this->_initMergerDir($mergerDir);
        if (!$targetDir) {
            return '';
        }

        // base hostname & port
        $baseMediaUrl = Mage::getBaseUrl('media', $isSecure);
        $hostname = parse_url($baseMediaUrl, PHP_URL_HOST);
        $port = parse_url($baseMediaUrl, PHP_URL_PORT);
        if (false === $port) {
            $port = $isSecure ? 443 : 80;
        }

        // merge into target file
        $targetFilename = md5(implode(',', $files) . "|{$hostname}|{$port}") . '.css';
        $mergeFilesResult = $this->_mergeFiles(
            $files, $targetDir . DS . $targetFilename,
            false,
            array($this, 'beforeMergeCss'),
            'css'
        );
        if ($mergeFilesResult) {
            return $baseMediaUrl . $mergerDir . '/' . $targetFilename;
        }
        return '';
    }

}