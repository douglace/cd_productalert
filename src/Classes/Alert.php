<?php
/**
* 2007-2022 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2022 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

namespace Vex6\CdProductalert\Classes;

use Vex6\V6Kreabel\Repository;
use ObjectModel;
use Context;
use Product;
use DbQuery;
use Db;

class Alert
{
    /**
     * Retourne la liste des alertes d'un client
     * @param int $id_customer
     * @param boolean $full
     * @return null|[]
     */
    public static function getCustomerAlerts($id_customer, $full = false) {
        $q = new DbQuery();
        $q->select('id_cd_alert, alert_name')
            ->from('cd_alert')
            ->where('id_customer='.$id_customer);

        if($full) {
            return array_map(function($a){
                $a['attributes'] = Alert::getAttributes($a['id_cd_alert']);
                $a['features'] = Alert::getFeatures($a['id_cd_alert']);
                $a['brands'] = Alert::getBrands($a['id_cd_alert']);
                $a['suppliers'] = Alert::getSuppliers($a['id_cd_alert']);
                return $a;
            }, Db::getInstance()->executeS($q));
        }

        return Db::getInstance()->executeS($q);
    }

    /**
     * Retourne la liste des attributs d'une alerte
     * @param int $id_alert
     * @return null|[]
     */
    public static function getAttributes($id_alert) {
        $q = new DbQuery();
        $q->select('id_attribut')
        ->from('cd_alert_attribut')
        ->where('id_cd_alert='.$id_alert);
        return Db::getInstance()->executeS($q);
    }

    /**
     * Retourne la liste des caractéristiques d'une alerte
     * @param int $id_alert
     * @return null|[]
     */
    public static function getFeatures($id_alert) {
        $q = new DbQuery();
        $q->select('id_feature')
        ->from('cd_alert_feature')
        ->where('id_cd_alert='.$id_alert);
        return Db::getInstance()->executeS($q);
    }

    /**
     * Retourne la liste des Marque d'une alerte
     * @param int $id_alert
     * @return null|[]
     */
    public static function getBrands($id_alert) {
        $q = new DbQuery();
        $q->select('id_brand')
        ->from('cd_alert_brand')
        ->where('id_cd_alert='.$id_alert);
        return Db::getInstance()->executeS($q);
    }

    /**
     * Retourne la liste des fournisseur d'une alerte
     * @param int $id_alert
     * @return null|[]
     */
    public static function getSuppliers($id_alert) {
        $q = new DbQuery();
        $q->select('id_supplier')
        ->from('cd_alert_supplier')
        ->where('id_cd_alert='.$id_alert);
        return Db::getInstance()->executeS($q);
    }

    /**
     * Cherche tous les client à notifier
     * @param int
     * @return boolean
     */
    public static function findAlerts($id_product){
        $attributes = self::getProductAttributes($id_product);
        $features = self::getProductFeautures($id_product);
        $id_supplier = self::getProductSupplier($id_product);
        $id_manufacturer = self::getProductBrand($id_product);
        $q = new DbQuery();
        $q->select('a.id_customer, a.id_cd_alert, a.alert_name')
        ->where('a.active=0')
        ->from('cd_alert', 'a');

        if(!empty($attributes) && $attributes) {
            $q->innerJoin('cd_alert_attribut', 'aa', 'aa.id_cd_alert=a.id_cd_alert')
            ->where('aa.id_attribut in ('.implode(',', $attributes).')');
        }

        if(!empty($features) && $features) {
            $q->innerJoin('cd_alert_feature', 'af', 'af.id_cd_alert=a.id_cd_alert')
            ->where('af.id_feature in ('.implode(',', $features).')');
        }

        if(!empty($id_supplier) && $id_supplier) {
            $q->innerJoin('cd_alert_supplier', 'as', 'as.id_cd_alert=a.id_cd_alert')
            ->where('as.id_supplier='.$id_supplier);
        }

        if(!empty($id_manufacturer) && $id_manufacturer) {
            $q->innerJoin('cd_alert_brand', 'am', 'am.id_cd_alert=a.id_cd_alert')
            ->where('am.id_brand='.$id_manufacturer);
        }

        return Db::getInstance()->executeS($q);
    }

    /**
     * Retourne tous les attribut d'un produit donné
     * @return null|[]
     */
    private static function getProductAttributes($id_product) {
        $q = new DbQuery();
        $q->select('a.id_attribute')
        ->from('product_attribute_combination', 'a')
        ->innerJoin('product_attribute', 'pa', 'pa.id_product_attribute=a.id_product_attribute')
        ->where('pa.id_product='.$id_product);
        return array_map(function($a){
            return $a['id_attribute'];
        }, Db::getInstance()->executeS($q));
    }

    /**
     * Retourne tous les caractéristique d'un produit donné
     * @return null|[]
     */
    private static function getProductFeautures($id_product) {
        $q = new DbQuery();
        $q->select('id_feature')
        ->from('feature_product')
        ->where('id_product='.$id_product);
        return array_map(function($a){
            return $a['id_feature'];
        }, Db::getInstance()->executeS($q));
    }

    /**
     * Retourne la marque d'un produit donné
     * @return int
     */
    private static function getProductBrand($id_product) {
        $q = new DbQuery();
        $q->select('id_manufacturer')
        ->from('product', 'a')
        ->where('id_product='.$id_product);
        return (int)Db::getInstance()->getValue($q);
    }

    /**
     * Retourne la marque d'un produit donné
     * @return int
     */
    private static function getProductSupplier($id_product) {
        $q = new DbQuery();
        $q->select('id_supplier')
        ->from('product', 'a')
        ->where('id_product='.$id_product);
        return (int)Db::getInstance()->getValue($q);
    }

    
}