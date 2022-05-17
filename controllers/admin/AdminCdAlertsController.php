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
        $this->addRowAction('edit');
        $this->addRowAction('delete'); 

        parent::__construct();

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected', [], 'Modules.Kreabelhome.Admin'),
                'icon' => 'icon-trash',
                'confirm' => $this->l('Delete selected items?', [], 'Modules.Kreabelhome.Admin')
            )
        );

        $this->fields_list = array(
            'id_cd_alert'=>array(
                'title' => $this->l('ID', [], 'Modules.Kreabelhome.Admin'),
                'align'=>'center',
                'class'=>'fixed-width-xs'
            ),
            'email'=>array(
                'title'=>$this->l('Email', [], 'Modules.Kreabelhome.Admin'),
                'width'=>'auto'
            ),
            'alert_name'=>array(
                'title'=>$this->l('Désignations', [], 'Modules.Kreabelhome.Admin'),
                'width'=>'auto'
            ),
            'active'=>array(
                'title'=>$this->l('Etat', [], 'Modules.Kreabelhome.Admin'),
                'width'=>'auto'
            ),
        );
    }

    public function renderForm()
    {
        if (!($submenu = $this->loadObject(true))) {
            return;
        }

        $root = Category::getRootCategory();
        $tree = new HelperTreeCategories('id_category'); 
        $tree->setUseCheckBox(false)
            ->setAttribute('id_category', $root->id)
            ->setRootCategory($root->id)
            ->setUseSearch(true)
            ->setSelectedCategories(array((int)$submenu->id_category))
            ->setInputName('id_category'); //Set the name of input. The option "name" of $fields_form doesn't seem to work with "categories_select" type
        

        $this->fields_form = array(
            'tinymce' => true,
            'legend' => array(
                'title' => $this->l('Menu Kreabel', [], 'Modules.Kreabelhome.Admin'),
                'icon' => 'icon-certificate'
            ),
            'input' => array(
                array(
                    'type'  => 'categories_select',
                    'label' => $this->l('Catégory', [], 'Modules.Kreabelhome.Admin'),
                    'name' => 'id_category',
                    'category_tree'  => $tree->render(),
                    'required' => false,
                    'hint' => $this->l('Invalid characters:', [], 'Modules.Kreabelhome.Admin').' &lt;&gt;;=#{}'
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('product', [], 'Modules.Kreabelhome.Admin'),
                    'name' => 'id_product',
                    'class' => 'chosen',
                    'options' => [
                        'query' => $this->getProducts(),
                        'id' => 'id',
                        'name' => 'name',
                    ],
                    'hint' => $this->l('Invalid characters:', [], 'Modules.Kreabelhome.Admin').' &lt;&gt;;=#{}'
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Title', [], 'Modules.Kreabelhome.Admin'),
                    'name' => 'title',
                    'options' => [
                        'query' => $this->getProducts(),
                        'id' => 'id',
                        'name' => 'name',
                    ],
                    'hint' => $this->l('Invalid characters:', [], 'Modules.Kreabelhome.Admin').' &lt;&gt;;=#{}'
                )
            )
        );

        if (!($submenu = $this->loadObject(true))) {
            return;
        }


        $this->fields_form['submit'] = array(
            'title' => $this->l('Save', [], 'Modules.Kreabelhome.Admin')
        );

        return parent::renderForm();
    }

    public function getProducts() {
        $q = new DbQuery();
        $q->select('id_product id, name')
            ->from('product_lang')
            ->where('id_lang='.$this->context->language->id)
        ;

        return Db::getInstance()->executeS($q);
    }


    public function l($string, $params = [], $domaine = 'Modules.Kreabelhome.Admin', $local = null){
        if(_PS_VERSION_ >= '1.7'){
            return $this->module->getTranslator()->trans($string, $params, $domaine, $local);
        }else{
            return parent::l($string, null, false, true);
        }
    }
}
