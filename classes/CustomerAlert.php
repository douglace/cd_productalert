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

class CustomerAlert extends ObjectModel
{
    /**
     * Client
     * @param int $id_customer
     */
    public $id_customer = 0;

    /**
     * Currency
     * @param int $id_currency
     */
    public $id_currency = 0;

    /**
     * Manufacture
     * @param int $id_manufacturer
     */
    public $id_manufacturer = 0;

    /**
     * Supplier
     * @param int $id_supplier
     */
    public $id_supplier = 0;
    

    /**
     * Active
     * @param bool $active
     */
    public $active = false;

    /**
     * Alert name
     * @param string $alert_name
     */
    public $alert_name;

    /**
     * Alert price
     * @param float $alert_price
     */
    public $alert_price;

    /**
     * Date d'ajout
     * @param string $date_add
     */
    public $date_add;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'cd_alert',
        'primary' => 'id_cd_alert',
        'fields' => array(
            'id_customer' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isInt'
            ),
            'id_currency' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isInt'
            ),
            'id_supplier' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isInt'
            ),
            'id_manufacturer' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isInt'
            ),
            'active' => array(
                'type' => self::TYPE_BOOL
            ),
            'date_add' => array(
                'type' => self::TYPE_DATE
            ),
            'alert_name' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString'
            ),
            'alert_price' => array(
                'type' => self::TYPE_FLOAT,
                'validate' => 'isFloat'
            )
        ),
    );

    public function delete()
    {
        $id_alert = $this->id;
        $delete = parent::delete();
        if($delete){
            Db::getInstance()->delete('cd_alert_alerted', 'id_cd_alert='.$id_alert);
            Db::getInstance()->delete('cd_alert_attribut', 'id_cd_alert='.$id_alert);
            Db::getInstance()->delete('cd_alert_feature', 'id_cd_alert='.$id_alert);
        }
        return $delete;
    }
}
