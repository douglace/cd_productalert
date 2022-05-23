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

if(!class_exists('CustomerAlert'));
    require_once _PS_MODULE_DIR_.'cd_productalert/classes/CustomerAlert.php';

use Vex6\CdProductalert\Classes\Alert;

class AdminCdAlertsController extends ModuleAdminController {

    public function __construct()
    {
        $this->table = 'cd_alert';
        $this->className = 'CustomerAlert';
        $this->lang = false;
        $this->bootstrap = true;

        $this->deleted = false;
        $this->allow_export = true;
        $this->list_id = 'cd_alert';
        $this->identifier = 'id_cd_alert';
        $this->_defaultOrderBy = 'id_cd_alert';
        $this->_defaultOrderWay = 'ASC';
        $this->context = Context::getContext();

        $this->_select .= "cu.email, cu.firstname, cu.lastname";
        $this->_join = " LEFT JOIN `"._DB_PREFIX_."customer` cu ON cu.id_customer = a.id_customer";
        
        $this->addRowAction('view'); 
        $this->addRowAction('delete'); 

        parent::__construct();

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected', [], 'Modules.Cdproductalert.Admincdalertscontroller.php'),
                'icon' => 'icon-trash',
                'confirm' => $this->l('Delete selected items?', [], 'Modules.Cdproductalert.Admincdalertscontroller.php')
            )
        );

        $this->fields_list = array(
            'id_cd_alert'=>array(
                'title' => $this->l('ID', [], 'Modules.Cdproductalert.Admincdalertscontroller.php'),
                'align'=>'center',
                'class'=>'fixed-width-xs'
            ),
            'email'=>array(
                'title'=>$this->l('Email', [], 'Modules.Cdproductalert.Admincdalertscontroller.php'),
                'width'=>'auto'
            ),
            'alert_name'=>array(
                'title'=>$this->l('Désignations', [], 'Modules.Cdproductalert.Admincdalertscontroller.php'),
                'width'=>'auto'
            ),
            'active'=>array(
                'title'=>$this->l('Etat', [], 'Modules.Cdproductalert.Admincdalertscontroller.php'),
                'width'=>'auto',
                'callback'=>'alertState',
            ),
            'date_add' => array(
                'title' => $this->trans('Date', array(), 'Modules.Cdproductalert.Admincdalertscontroller.php'),
                'align' => 'text-right',
                'type' => 'datetime',
                'filter_key' => 'a!date_add',
            ),
        );
    }

    public function alertState($state, $row) {
        return (int)$state === 0 ?
        '<span class="bg-warning text-warning">'.$this->l('En attente', [], 'Modules.Cdproductalert.Admincdalertscontroller.php').'</span>' :
        '<span class="bg-success text-success">'.$this->l('Alerté', [], 'Modules.Cdproductalert.Admincdalertscontroller.php').'</span>' ;
    }

    public function renderView()
    {
        $id_alert = (int)Tools::getValue('id_cd_alert');
        $alert = Alert::getAlertForView($id_alert);
        if($alert == false || !Validate::isLoadedObject($alert)) {
            $alert = null;
            $this->errors[] = $this->trans('This alert doesn\'t exist', [], 'Modules.Cdproductalert.alert.php');
        }
        $this->context->smarty->assign(array(
            'alert' => $alert));
        
        return $this->module->fetch('module:'.$this->module->name.'/views/templates/admin/view.tpl');
    }

    public function l($string, $params = [], $domaine = 'Modules.Cdproductalert.Admincdalertscontroller.php', $local = null){
        if(_PS_VERSION_ >= '1.7'){
            return $this->module->getTranslator()->trans($string, $params, $domaine, $local);
        }else{
            return parent::l($string, null, false, true);
        }
    }
}
