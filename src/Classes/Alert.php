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

if(!class_exists('CustomerAlert'));
    require_once _PS_MODULE_DIR_.'cd_productalert/classes/CustomerAlert.php';


use Vex6\V6Kreabel\Repository;
use StockAvailable;
use AttributeGroup;
use CustomerAlert;
use FeatureValue;
use Manufacturer;
use Attribute;
use Validate;
use Supplier;
use Context;
use Product;
use Feature;
use DbQuery;
use Tools;
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
        $q->select('a.*')
            ->from('cd_alert', 'a')
            ->where('a.id_customer='.$id_customer);

        $alerts = array_map(function($a){
            $a['link'] = Context::getContext()->link->getModuleLink('cd_productalert', 'list', ['id_alert'=>$a['id_cd_alert']]);
            $a['edit_link'] = Context::getContext()->link->getModuleLink('cd_productalert', 'alert', ['id_alert'=>$a['id_cd_alert']]);
            $a['delete_link'] = Context::getContext()->link->getModuleLink('cd_productalert', 'alert', ['id_alert'=>$a['id_cd_alert'], 'delete'=>1]);
            $a['price'] = \Tools::displayPrice($a['alert_price'], ($a['id_currency'] ? (int)$a['id_currency'] : null));
            return $a;
        }, Db::getInstance()->executeS($q));
        if($full) {
            return array_map(function($a){
                $a['attributes'] = Alert::getAttributes($a['id_cd_alert']);
                $a['features'] = Alert::getFeatures($a['id_cd_alert']);
                $a['brands'] = Alert::getBrands($a['id_cd_alert']);
                $a['suppliers'] = Alert::getSuppliers($a['id_cd_alert']);
                
                return $a;
            }, $alerts);
        }

        return $alerts;
    }

    /**
     * @param int $id_alert
     * @param [] $features
     * @return []
     */
    public static function addFeatures($id_alert, $features){
        Db::getInstance()->delete("cd_alert_feature", "id_cd_alert=".(int)$id_alert);
        if(empty($features)) {
            return true;
        }

        $data = array();
        foreach($features as $id_feature) {
            if((int)$id_feature) {
                $data[] = [
                    'id_cd_alert' => $id_alert,
                    'id_feature' => $id_feature,
                ];
            }
        }
        if(empty($data)) {
            return true;
        }

        return Db::getInstance()->insert(
            'cd_alert_feature', $data, false, true, Db::INSERT_IGNORE
        );
    }

    /**
     * @param int $id_alert
     * @param [] $attributes
     * @return []
     */
    public static function addAttributes($id_alert, $attributes){
        Db::getInstance()->delete("cd_alert_attribut", "id_cd_alert=".(int)$id_alert);
        if(empty($attributes)) {
            return true;
        }
        $data = array();
        foreach($attributes as $id_attribut) {
            if((int)$id_attribut) {
                $data[] = [
                    'id_cd_alert' => $id_alert,
                    'id_attribut' => $id_attribut,
                ];
            }
        }
        if(empty($data)) {
            return true;
        }
        return Db::getInstance()->insert(
            'cd_alert_attribut', $data, false, true, Db::INSERT_IGNORE
        );
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
     * @return array|null
     */
    public static function findAlerts($id_product){
        $attributes = self::getProductAttributes($id_product);
        $features = self::getProductFeautures($id_product);
        $id_supplier = self::getProductSupplier($id_product);
        $id_manufacturer = self::getProductBrand($id_product);
        $q = new DbQuery();
        $q->select('a.id_customer, a.id_cd_alert, a.alert_name')
        ->where('a.active=0')
        ->from('cd_alert', 'a')
        ->groupBy('a.id_cd_alert');

        if(!empty($attributes) && $attributes) {
            $q->innerJoin('cd_alert_attribut', 'aa', 'aa.id_cd_alert=a.id_cd_alert')
            ->where('aa.id_attribut in ('.implode(',', $attributes).')');
        } else {
            $q->where('NOT EXISTS(SELECT 1 FROM `'._DB_PREFIX_.'cd_alert_attribut` aa WHERE aa.id_cd_alert=a.id_cd_alert )');
        }

        if(!empty($features) && $features) {
            $q->innerJoin('cd_alert_feature', 'af', 'af.id_cd_alert=a.id_cd_alert')
            ->where('af.id_feature in ('.implode(',', $features).')');
        }else {
            $q->where('NOT EXISTS(SELECT 1 FROM `'._DB_PREFIX_.'cd_alert_feature` af WHERE af.id_cd_alert=a.id_cd_alert )');
        }

        if(!empty($id_supplier) && $id_supplier) {
            $q->where('a.id_supplier='.$id_supplier);
        } else {
            $q->where('a.id_supplier = 0 OR a.id_supplier IS NULL');
        }

        if(!empty($id_manufacturer) && $id_manufacturer) {
            $q->where('a.id_manufacturer='.$id_manufacturer);
        } else {
            $q->where('a.id_manufacturer = 0 OR a.id_manufacturer IS NULL');
        }

        return array_map(function($a){
            $a['attributes'] = array_map(function($b){
                return $b['id_attribut'];
            }, self::getAttributes($a['id_cd_alert']));
            $a['features'] = array_map(function($b){
                return $b['id_feature'];
            }, self::getFeatures($a['id_cd_alert']));
            return $a;
        }, Db::getInstance()->executeS($q));
    }

    /**
     * Retourne tous les attribut d'un produit donné
     * @return null|[]
     */
    public static function getProductAttributes($id_product) {
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
    public static function getProductFeautures($id_product) {
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

    public static function addNotifications($data) {
        return Db::getInstance()->insert(
            'cd_alert_alerted',
            $data,
            false,
            true, Db::REPLACE
        );
    }

    /**
     * @param int $id_alert
     * @param int $id_product
     * @param int $id_product_attribute
     * @return bool
     */
    public static function setIsAlerted($id_alert, $id_product, $id_product_attribute) {
        return Db::getInstance()->update(
            'cd_alert_alerted',
            [
                'is_alerted' => 1
            ], 
            "id_cd_alert=".$id_alert." AND id_product=".$id_product." AND id_product_attribute=".$id_product_attribute 
        );
    }

    /**
     * Retourne les notifications courante 
     * @return []
     */
    public function getAlertsForNotifications() {
        $data = [];

        $q = new DbQuery();
        $q->select('a.*, ca.*, cu.firstname, cu.lastname, cu.email')
            ->from('cd_alert_alerted', 'a')
            ->innerJoin('cd_alert', 'ca', 'ca.id_cd_alert=a.id_cd_alert')
            ->innerJoin('customer', 'cu', 'cu.id_customer=ca.id_customer')
            ->innerJoin('product', 'p', 'p.id_product=a.id_product')
            ->where('a.is_alerted=0')
            ->where('p.active=1')
        ;

        $data = array_map(function($a){
            $a['product_quantity'] = StockAvailable::getQuantityAvailableByProduct(
                $a['id_product'], $a['id_product_attribute']
            );
            $a['product_name'] = Product::getProductName(
                $a['id_product'], $a['id_product_attribute']
            );
            $a['product_link'] = Context::getContext()->link->getProductLink(
                $a['id_product'], null, null, null, null, null, $a['id_product_attribute']
            );
            return $a;
        }, Db::getInstance()->executeS($q));

        return $data;
    }

    /**
     * @param int $id_alert
     * @return CustomerAlert|bool
     */
    public static function getAlertForView($id_alert) {
        $context = Context::getContext();
        $id_lang = $context->language->id;
        $alert = new CustomerAlert((int)$id_alert);
        if(Context::getContext()->controller->controller_type == "modulefront") {
            if(!Validate::isLoadedObject($alert) || $alert->id_customer != $context->customer->id) {
                return false;
            }
        }elseif(!Validate::isLoadedObject($alert)) {
            return false;
        }
        

        $alert->attributes = array_map(function($a)use($id_lang){
            $attribute = new Attribute($a['id_attribut'], $id_lang);
            $group = new AttributeGroup($attribute->id_attribute_group, $id_lang);
            
            return [
                'attribute' => $attribute,
                'group' => $group,
            ];
        }, Alert::getAttributes($alert->id));
        
        $alert->features = array_map(function($a)use($id_lang){
            $value = new FeatureValue($a['id_feature'], $id_lang);
            $feature = new Feature($value->id_feature, $id_lang);
            return [
                'value' => $value,
                'feature' => $feature,
            ];
        },  Alert::getFeatures($alert->id));

        if($alert->id_supplier) {
            $alert->supplier = new Supplier((int)$alert->id_supplier);
        }
        if($alert->id_manufacturer) {
            $alert->manufacturer = new Manufacturer((int)$alert->id_manufacturer);
        }
        if($alert->alert_price) {
            $alert->price = Tools::displayPrice((float)$alert->alert_price, (int)$alert->id_currency);
        }

        $alert->products = self::getSimilarProducts($alert->id);

        return $alert;
    }

    /**
     * @param int $id_alert
     * @return array
     */
    public static function getSimilarProducts($id_alert, $id_lang = null){
        $id_lang = $id_alert ? $id_alert : Context::getContext()->language->id;
        $q = new DbQuery();
        $q->from('cd_alert_alerted', 'a')
        ->select('a.*')
        ->innerJoin('product', 'p', 'p.id_product=a.id_product')
        ->where('p.active=1')
        ->where('a.id_cd_alert='.$id_alert)
        ;

        $products = Db::getInstance()->executeS($q);
        if($products && !empty($products)) {
            $data = array();
            $context = Context::getContext();
            foreach($products as $product){
                $qty = (int)StockAvailable::getQuantityAvailableByProduct($product['id_product'], $product['id_product_attribute']);
                
                if($qty > 0) {
                    $data[] = [
                        'qty'=> $qty,
                        'price'=> Product::getPriceStatic($product['id_product'], true, $product['id_product_attribute']),
                        'name'=> Product::getProductName($product['id_product'], $product['id_product_attribute']),
                        'link'=> $context->link->getProductLink($product['id_product'], null, null, null, $id_lang, null, $product['id_product_attribute']),
                    ];
                }
                
            }
        }
        return false;

        
        
    }

}