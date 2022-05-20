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

use Vex6\CdProductalert\Classes\Alert;

if(!class_exists('CustomerAlert'));
    require_once _PS_MODULE_DIR_.'cd_productalert/classes/CustomerAlert.php';

    
class cd_productalertCronModuleFrontController extends ModuleFrontController {
    public function init() {
        parent::init();
        $email_copie = Configuration::get('CD_PRODUCT_ALERT_EMAIL_COPIE');
        $id_lang = $this->context->language->id;
        $subject = Configuration::get('CD_PRODUCT_ALERT_SUBJECT_MAIL', $id_lang);
        $notifications = Alert::getAlertsForNotifications();
        if($notifications && !empty($notifications)) {
            foreach($notifications as $notification) {
                if((int)$notification['product_quantity'] > 0) {
                    $send = Mail::send(
                        $id_lang,
                        'alert',
                        $subject,
                        [
                            '{firstname}'=>$notification['firstname'],
                            '{lastname}'=>$notification['lastname'],
                            '{email}'=>$notification['email'],
                            '{product_quantity}'=>$notification['product_quantity'],
                            '{product_name}'=>$notification['product_name'],
                            '{product_link}'=>$notification['product_link'],
                            '{alert_name}'=>$notification['alert_name'],
                            '{alert_price}'=>$notification['alert_price'],
                        ],
                        $notification['email'],
                        (ucfirst($notification['firstname'])." ".$notification['lastname']),
                        null,
                        null,
                        null,
                        null,
                        _PS_MODULE_DIR_.$this->module->name.'/mails/',
                        false,
                        null,
                        $email_copie
                    );
                    
                    if($send) {
                        Alert::setIsAlerted(
                            $notification['id_cd_alert'],
                            $notification['id_product'],
                            $notification['id_product_attribute']
                        );
                    }
                }
            }
        }
        dump($notifications);
        dump($_GET);
        die;
    }
}