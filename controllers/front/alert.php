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

    
class cd_productalertAlertModuleFrontController extends ModuleFrontController {
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
            $this->alert = new CustomerAlert((int)Tools::getValue('id_alert'));
            if(!Validate::isLoadedObject($this->alert) ||
            $this->alert->id_customer != $this->context->customer->id) {
                $this->alert = null;
                $this->errors[] = $this->trans('This alert doesn\'t exist', [], 'Modules.Cdproductalert.alert.php');
            }else{
                if(Tools::isSubmit('delete')) {
                    $this->alert->delete();
                    Tools::redirectLink(
                      $this->context->link->getModuleLink($this->module->name, 'list', ['success_delete'=>1])  
                    );
                }else {
                    $this->alert->attributes = array_map(function($a){return $a['id_attribut'];}, Alert::getAttributes($this->alert->id));
                    $this->alert->features = array_map(function($a){return $a['id_feature'];}, Alert::getFeatures($this->alert->id));
                }
            }
        }
    }

    public function initContent(){
        parent::initContent();
        $id_lang = $this->context->language->id;
        if($this->alert && $this->alert->id) {
            $action_link = $this->context->link->getModuleLink($this->module->name, 'alert', ['id_alert'=>$this->alert->id]);
        } else {
            $action_link = $this->context->link->getModuleLink($this->module->name, 'alert');
        }
        
        $this->context->smarty->assign(array(
            'title' => Configuration::get('CD_PRODUCT_ALERT_TITLE_PAGE', $id_lang),
            'attributes' => $this->getAttributesForForm(),
            'features' => $this->getFearuresForForm(),
            'suppliers' => $this->getSuppliersForForm(),
            'manufacturers' => $this->getManufacuresForForm(),
            'ref_fabricant'=>Tools::getValue('ref_fabricant'),
            'alert'=>$this->alert,
            'action_link'=>$action_link
        ));
        $this->setTemplate('module:cd_productalert/views/templates/front/alert.tpl');
    }

    public function postProcess()
    {
        parent::postProcess();
        if(Tools::isSubmit('submitNewCustomerAlert')) {
            try{
                if(Tools::isSubmit('id_alert')){
                    $this->alert = new CustomerAlert(Tools::getValue('id_alert'));
                    if(Validate::isLoadedObject($this->alert) && $this->alert->id_customer != $this->context->customer->id){
                        $this->errors[] = $this->trans('You cannot edit this alert', [], 'Modules.Cdproductalert.alert.php');
                        return false;
                    }
                    $this->alert->active = 0;
                }else {
                    $this->alert = new CustomerAlert();
                }
                
                $this->alert->alert_name = Tools::getValue('alert_name');
                $this->alert->alert_price = (float)Tools::getValue('alert_price');
                $this->alert->id_supplier = (int)Tools::getValue('supplier');
                $this->alert->id_manufacturer = (int)Tools::getValue('manufacturer');
                $this->alert->id_currency = (int)$this->context->currency->id;
                $this->alert->id_customer = (int)$this->context->customer->id;
                if($this->alert->validateFields() && $this->alert->save()) {
                    Alert::addAttributes($this->alert->id, array_values(Tools::getValue('attribute')));
                    
                    Alert::addFeatures($this->alert->id, array_values(Tools::getValue('feature')));
                    $this->success[] = $this->trans('Your alert has been successful save', [], 'Modules.Cdproductalert.alert.php');
                    if(Tools::isSubmit('id_alert')) {
                        Tools::redirectLink(
                            $this->context->link->getModuleLink($this->module->name, 'list', ['success_edit'=>1, 'id_alert'=>$this->alert->id])  
                          );
                    } else {
                        Tools::redirectLink(
                            $this->context->link->getModuleLink($this->module->name, 'list', ['success_create'=>1, 'id_alert'=>$this->alert->id])  
                          );
                    }
                } else {
                    $this->errors[] = $this->trans('Something when wrong, please check fields', [], 'Modules.Cdproductalert.alert.php');
                }
            }catch(Exception $e) {
                $this->errors[] = $this->trans('Something when wrong', [], 'Modules.Cdproductalert.alert.php');
            }
            
        }
    }

    public function getManufacuresForForm() {
        $can_display = (bool)Configuration::get('CD_PRODUCT_ALERT_DISPLAY_MANUFACTURER');
        if(!$can_display) return null;
        $id_lang = $this->context->language->id;
        return Manufacturer::getManufacturers(false, $id_lang);
    }

    public function getSuppliersForForm() {
        $can_display = (bool)Configuration::get('CD_PRODUCT_ALERT_DISPLAY_SUPPLIER');
        if(!$can_display) return null;
        $id_lang = $this->context->language->id;
        return Supplier::getSuppliers(false, $id_lang);
    }

    public function getFearuresForForm() {
        $id_lang = $this->context->language->id;
        $features = Configuration::get('CD_PRODUCT_ALERT_FEATURES');
        if($features && !empty($features)) {
            return array_map(function($id_feature)use($id_lang){
                $feature = new Feature($id_feature, $id_lang);
                return [
                    'feature_id' => $feature->id,
                    'name' => $feature->name,
                    'values' => FeatureValue::getFeatureValuesWithLang($id_lang, $feature->id),
                ];
            }, explode(',', $features));
        }

        return null;
    }

    public function getAttributesForForm() {
        $id_lang = $this->context->language->id;
        $attributeGroups = Configuration::get('CD_PRODUCT_ALERT_ATTRIBUTES');
        if($attributeGroups && !empty($attributeGroups)) {
            return array_map(function($id_attribute_group)use($id_lang){
                $group = new AttributeGroup($id_attribute_group, $id_lang);
                return [
                    'group_id' => $group->id,
                    'name' => $group->name,
                    'attributes' => AttributeGroup::getAttributes($id_lang, $group->id)
                ];
            }, explode(',', $attributeGroups));
        }

        return null;
    }
}