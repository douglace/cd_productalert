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

class cd_productalertListModuleFrontController extends ModuleFrontController {
    public $display_column_left = false;
    public $auth = true;
    
    /**
    * @var CustomerAlert|null $alert
    */
   public $alert = null;

    public function setMedia()
    {
        $this->addCSS($this->module->getPathUri().'/views/css/front.css');
        parent::setMedia();
    }

    public function init(){
        parent::init();
        if(Tools::isSubmit('id_alert')){
            $id_alert = (int)Tools::getValue('id_alert');
            $this->alert = Alert::getAlertForView($id_alert);
            
            
            if(!Validate::isLoadedObject($this->alert) ||
                $this->alert->id_customer != $this->context->customer->id) {
                $this->alert = null;
                $this->errors[] = $this->trans('This alert doesn\'t exist', [], 'Modules.Cdproductalert.alert.php');
            }
        }
    }

    public function initContent(){
        parent::initContent();
        if(Tools::isSubmit('success_edit')) {
            $this->success[] = $this->trans('Your alert has been successfully edited', [], 'Modules.Cdproductalert.list');
        }elseif(Tools::isSubmit('success_delete')) {
            $this->success[] = $this->trans('Your alert has been successfully deleted', [], 'Modules.Cdproductalert.list');
        }elseif(Tools::isSubmit('success_create')) {
            $this->success[] = $this->trans('Your alert has been successfully created', [], 'Modules.Cdproductalert.list');
        }
        
        if($this->alert && $this->alert->id) {
            $this->context->smarty->assign(array(
                'alert' => $this->alert,
                'edit_link' => $this->context->link->getModuleLink($this->module->name, 'alert', ['id_alert'=>$this->alert->id]),
                'alert_link' => $this->context->link->getModuleLink($this->module->name, 'list')
            ));
            $this->setTemplate('module:cd_productalert/views/templates/front/show.tpl');
        } else {
            $this->context->smarty->assign(array(
                'add_alert' => $this->context->link->getModuleLink($this->module->name, 'alert'),
                'alerts' => Alert::getCustomerAlerts($this->context->customer->id)
            ));
            $this->setTemplate('module:cd_productalert/views/templates/front/list.tpl');    
        }
    }
    
}