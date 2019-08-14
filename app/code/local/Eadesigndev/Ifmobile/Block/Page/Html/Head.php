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
class Eadesigndev_Ifmobile_Block_Page_Html_Head extends Mage_Page_Block_Html_Head
{


    /**
     * The cache control system to load different cache
     */
    protected function _construct()
    {

        parent::_construct();
        $isMobile = 'NO_MOBILE';
        if (Mage::helper('ifmobile')->applyMobileHtml()) {
            $isMobile = 'YES_MOBILE';
        }

        $this->addData(array(
            'cache_lifetime' => 3600,
            'cache_tags' => array(
                Mage_Core_Model_Store::CACHE_TAG,
                Mage_Cms_Model_Block::CACHE_TAG
            ),
            'cache_key' => $isMobile
        ));

    }

    /**
     * The cahce key info.
     * @return array
     */
    public function getCacheKeyInfo()
    {

        $isMobile = 'NO_MOBILE';
        if (Mage::helper('ifmobile')->applyMobileHtml()) {
            $isMobile = 'YES_MOBILE';
        }

        return array(
            $isMobile,
            Mage::app()->getStore()->getId(),
            (int)Mage::app()->getStore()->isCurrentlySecure(),
            Mage::getDesign()->getPackageName(),
            Mage::getDesign()->getTheme('template')
        );
    }

    /**
     * Get HEAD HTML with CSS/JS/RSS definitions
     * (actually it also renders other elements, TODO: fix it up or rename this method)
     *
     * @return string
     */
    public function getCssJsHtml()
    {
        if (!Mage::helper('ifmobile')->applyMobileHtml()) {
            return parent::getCssJsHtml();
        }

        $cssSkinUrl = $this->getSkinUrl('css');

        $mobileCssSkinUrl = str_replace(
            Mage::helper('ifmobile')->getDeskTheme(),
            Mage::helper('ifmobile')->getMobTheme(),
            $cssSkinUrl
        );

        $jsSkinUrl = $this->getSkinUrl('js');

        $mobileJsSkinUrl = str_replace(
            Mage::helper('ifmobile')->getJsDeskTheme(),
            Mage::helper('ifmobile')->getJsMobTheme(),
            $jsSkinUrl
        );


        // separate items by types
        $lines = array();
        foreach ($this->_data['items'] as $item) {
            if (!is_null($item['cond']) && !$this->getData($item['cond']) || !isset($item['name'])) {
                continue;
            }
            $if = !empty($item['if']) ? $item['if'] : '';
            $params = !empty($item['params']) ? $item['params'] : '';
            switch ($item['type']) {
                case 'js':        // js/*.js
                case 'skin_js':   // skin/*/*.js
                case 'js_css':    // js/*.css
                case 'skin_css':  // skin/*/*.css
                    $lines[$if][$item['type']][$params][$item['name']] = $item['name'];
                    break;
                default:
                    $this->_separateOtherHtmlHeadElements($lines, $if, $item['type'], $params, $item['name'], $item);
                    break;
            }
        }

        // prepare HTML
        $shouldMergeJs = Mage::getStoreConfigFlag('dev/js/merge_files');
        $shouldMergeCss = Mage::getStoreConfigFlag('dev/css/merge_css_files');
        $html = '';
        foreach ($lines as $if => $items) {
            if (empty($items)) {
                continue;
            }
            if (!empty($if)) {
                // open !IE conditional using raw value
                if (strpos($if, "><!-->") !== false) {
                    $html .= $if . "\n";
                } else {
                    $html .= '<!--[if ' . $if . ']>' . "\n";
                }
            }

            // static and skin css

            $mobile = Mage::helper('ifmobile/detect')->isMobile();
            if ($mobile) {
                $css = $this->_prepareStaticAndSkinElements('<link rel="stylesheet" type="text/css" href="%s"%s />' . "\n",
                    empty($items['js_css']) ? array() : $items['js_css'],
                    empty($items['skin_css']) ? array() : $items['skin_css'],
                    $shouldMergeCss ? array(Mage::getDesign(), 'getMergedCssUrl') : null
                );

                $js = $this->_prepareStaticAndSkinElements('<script type="text/javascript" src="%s"%s></script>' . "\n",
                    empty($items['js']) ? array() : $items['js'],
                    empty($items['skin_js']) ? array() : $items['skin_js'],
                    $shouldMergeJs ? array(Mage::getDesign(), 'getMergedJsUrl') : null
                );

                $html .= str_replace($cssSkinUrl, $mobileCssSkinUrl, $css);
                $html .= str_replace($jsSkinUrl, $mobileJsSkinUrl, $js);

            } else {

                $html .= $this->_prepareStaticAndSkinElements('<link rel="stylesheet" type="text/css" href="%s"%s />' . "\n",
                    empty($items['js_css']) ? array() : $items['js_css'],
                    empty($items['skin_css']) ? array() : $items['skin_css'],
                    $shouldMergeCss ? array(Mage::getDesign(), 'getMergedCssUrl') : null
                );

                $html .= $this->_prepareStaticAndSkinElements('<script type="text/javascript" src="%s"%s></script>' . "\n",
                    empty($items['js']) ? array() : $items['js'],
                    empty($items['skin_js']) ? array() : $items['skin_js'],
                    $shouldMergeJs ? array(Mage::getDesign(), 'getMergedJsUrl') : null
                );
            }


            // static and skin javascripts


            // other stuff
            if (!empty($items['other'])) {
                $html .= $this->_prepareOtherHtmlHeadElements($items['other']) . "\n";
            }

            if (!empty($if)) {
                // close !IE conditional comments correctly
                if (strpos($if, "><!-->") !== false) {
                    $html .= '<!--<![endif]-->' . "\n";
                } else {
                    $html .= '<![endif]-->' . "\n";
                }
            }
        }

        return $html;
    }

}