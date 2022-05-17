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
    public $id_customer  = 0;
    

    /**
     * Active
     * @param bool $active
     */
    public $active = false;

    /**
     * Alert name
     * @param int $alert_name
     */
    public $alert_name;

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
            'active' => array(
                'type' => self::TYPE_BOOL
            ),
            'alert_name' => array(
                'type' => self::TYPE_STRING,
                'lang' => true,
                'validate' => 'isString'
            )
        ),
    );
}
