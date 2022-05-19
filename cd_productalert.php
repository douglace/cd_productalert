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

if (!defined('_PS_VERSION_')) {
    exit;
}

if (file_exists(_PS_MODULE_DIR_. 'cd_productalert/vendor/autoload.php')) {
    require_once _PS_MODULE_DIR_.  'cd_productalert/vendor/autoload.php';
}

class Cd_productalert extends Module
{
    protected $config_form = false;

    /**
     * @param array $tabs
     */
    public $tabs;

    /**
     * @param Vex6\CdProductalert\Repository $repository
     */
    protected $repository;

    /**
     * @param array $languages
     */
    protected $languages;

    public function __construct()
    {
        $this->name = 'cd_productalert';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'CleanDev';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        $this->tabs = array(
            array(
                'name'=> $this->l('Alert clients'),
                'class_name'=>'AdminCdCustomerAlert',
                'parent'=>'AdminParentModulesSf',
            ),
            array(
                'name'=> $this->l('Alert'),
                'class_name'=>'AdminCdAlerts',
                'parent'=>'AdminCdCustomerAlert',
            )
        );

        $this->repository = new Vex6\CdProductalert\Repository($this); 
        parent::__construct();
        $this->languages = Language::getLanguages();
        $this->displayName = $this->l('Alertes produits');
        $this->description = $this->l('Alertes produits mis en vente');
        
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        return parent::install() && $this->repository->install();
    }

    public function uninstall()
    {
        return parent::uninstall() && $this->repository->uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        $process = $this->postProcess();

        $this->context->smarty->assign(
            array(
                'module_dir' => $this->_path,
                'config_form' => $this->config_form,
                'form' => $this->renderConfigForm(),
            )
        );
        return $process.$this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');
    }
    
    /**
     * Retourne les caractéristiques de la boutiques
     * @param int $id_lang
     * @return []
     */
    public function getFeatures($id_lang = null) {
        $id_lang = $id_lang ? $id_lang : Context::getContext()->language->id;
        $q = new DbQuery();
        $q->select('id_feature id, name')
        ->from('feature_lang')
        ->where('id_lang='.$id_lang);

        return Db::getInstance()->executeS($q);
    }

    /**
     * Retourne les caractéristiques de la boutiques
     * @param int $id_lang
     * @return []
     */
    public function getAttributes($id_lang = null) {
        $id_lang = $id_lang ? $id_lang : Context::getContext()->language->id;
        $q = new DbQuery();
        $q->select('id_attribute_group id, name')
        ->from('attribute_group_lang')
        ->where('id_lang='.$id_lang);

        return Db::getInstance()->executeS($q);
    }

    public function renderConfigForm() {
        $form = new Vex6\CdProductalert\Utils\FormForm($this);
        $form->setShowToolbar(false)
            ->setTable($this->table)
            ->setModule($this)
            ->setDefaultFromLanguage($this->context->language->id)
            ->setAllowEmployeFromLang(Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0))
            ->setIdentifier($this->identifier)
            ->setSubmitAction('submitCdProductAlertConfig')
            ->setCurrentIndex($this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name)
            ->setToken(Tools::getAdminTokenLite('AdminModules'))
            ->setTplVar([
                'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
                'languages' => $this->context->controller->getLanguages(),
                'id_language' => $this->context->language->id,
            ])->addField(
                array(
                    'type' => 'text',
                    'label' => $this->l('Définissez une email de copie'),
                    'name' => 'CD_PRODUCT_ALERT_EMAIL_COPIE',
                )
            )
            ->addField(
                array(
                    'type' => 'text',
                    'label' => $this->l('Titre de la page'),
                    'name' => 'CD_PRODUCT_ALERT_TITLE_PAGE',
                    'lang' => true,
                )
            )
            ->addField(
                array(
                    'type' => 'select',
                    'label' => $this->l('liste des caractéristiques du formulaires'),
                    //'desc' => $this->l('laissez vide pour autoriser tous les caractéristiques'),
                    'name' => 'CD_PRODUCT_ALERT_FEATURES[]',
                    'class' => 'chosen',
                    'multiple' => true,
                    'options' => [
                        'query'=>$this->getFeatures(),
                        'id'=>'id',
                        'name'=>'name',
                    ],
                )
            )->addField(
                array(
                    'type' => 'select',
                    'label' => $this->l('liste des attributs du formulaires'),
                    //'desc' => $this->l('laissez vide pour autoriser tous les attributs'),
                    'name' => 'CD_PRODUCT_ALERT_ATTRIBUTES[]',
                    'class' => 'chosen',
                    'multiple' => true,
                    'options' => [
                        'query'=>$this->getAttributes(),
                        'id'=>'id',
                        'name'=>'name',
                    ],
                )
            )->addField(
                array(
                    'type' => 'switch',
                    'label' => $this->l('Afficher les fournisseurs dans le formulaire'),
                    'name' => 'CD_PRODUCT_ALERT_DISPLAY_SUPPLIER',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => true,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => false,
                            'label' => $this->l('Disabled')
                        )
                    ),
                )
            )
            ->addField(
                array(
                    'type' => 'switch',
                    'label' => $this->l('Afficher les marques dans le formulaire'),
                    'name' => 'CD_PRODUCT_ALERT_DISPLAY_MANUFACTURER',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => true,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => false,
                            'label' => $this->l('Disabled')
                        )
                    ),
                )
            )
            ->setLegend([
                'title' => $this->l('Forumulaires'),
                'icon' => 'icon-cogs',
            ])->setSubmit([
                'title' => $this->l('Save'),
            ])
        ;
        return $form->make();
    }

    
    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        $data =  array(
            'CD_PRODUCT_ALERT_EMAIL_COPIE'=> Configuration::get('CD_PRODUCT_ALERT_EMAIL_COPIE'),
            'CD_PRODUCT_ALERT_DISPLAY_SUPPLIER' => Configuration::get('CD_PRODUCT_ALERT_DISPLAY_SUPPLIER'),
            'CD_PRODUCT_ALERT_DISPLAY_MANUFACTURER' =>  Configuration::get('CD_PRODUCT_ALERT_DISPLAY_MANUFACTURER'),
            'CD_PRODUCT_ALERT_FEATURES[]'=> explode(',', Configuration::get('CD_PRODUCT_ALERT_FEATURES')),
            'CD_PRODUCT_ALERT_ATTRIBUTES[]'=> explode(',', Configuration::get('CD_PRODUCT_ALERT_ATTRIBUTES')),
        );

        foreach ($this->languages as $language) {
            
            $data['CD_PRODUCT_ALERT_TITLE_PAGE'][$language['id_lang']] =
             Configuration::get('CD_PRODUCT_ALERT_TITLE_PAGE', $language['id_lang']);
        }

        
        return $data;
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = [];
        $implodes = array(
            'CD_PRODUCT_ALERT_FEATURES[]',
            'CD_PRODUCT_ALERT_ATTRIBUTES[]'
        );
        $multilang = array(
            'CD_PRODUCT_ALERT_TITLE_PAGE',
        );
        if(Tools::isSubmit('submitCdProductAlertConfig')) {
            $form_values = $this->getConfigFormValues();
        }
        if(!empty($form_values)) {
            try{
                $values = [];
                if(!empty($multilang)) {
                    foreach($multilang as $k) {
                        foreach($this->languages as $l) {
                            $values[$k][$l['id_lang']] = Tools::getValue($k."_".$l['id_lang']);
                        }
                    }
                }
                foreach (array_keys($form_values) as $key) {
                    if(in_array($key, $multilang)) {
                        if(isset($values[$key])) {
                            Configuration::updateValue($key, $values[$key]);
                        }
                    } elseif(in_array($key, $implodes)) {
                        $true_key = rtrim($key, '[]');
                        $v = implode(',', Tools::getValue($true_key));
                        if($v) {
                            Configuration::updateValue($true_key,  $v);
                        }
                    } else {
                        Configuration::updateValue($key, Tools::getValue($key));
                    }
                }
                
                if(!empty($this->_errors)) {
                    return $this->displayError(current($this->_errors));
                }
                return $this->displayConfirmation($this->l('Configuration save with success'));
            
            }catch(Exception $e){
                return $this->displayError($this->l('Something when wrong'));
            }
        }
    }

    /**
    * Add the CSS & JavaScript files you want to be loaded in the BO.
    */
    public function hookBackOfficeHeader()
    {
        
        if (Tools::getValue('configure') == $this->name) {
            $this->context->controller->addJS($this->_path.'views/js/back.js');
            $this->context->controller->addCSS($this->_path.'views/css/back.css');
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path.'/views/js/front.js');
        $this->context->controller->addCSS($this->_path.'/views/css/front.css');
    }

    public function hookDisplayCustomerAccount() {
        $this->context->smarty->assign(array(
            'alert_link' => $this->context->link->getModuleLink($this->name, 'list')
        ));
        return $this->fetch('module:'.$this->name.'/views/templates/hook/accountaction.tpl');
    }

    public function hookDisplayProductActions(){
        $this->context->smarty->assign(array(
            'alert_link'=>$this->context->link->getModuleLink($this->name, 'alert'),
        ));
        return $this->fetch('module:'.$this->name.'/views/templates/hook/action.tpl');
    }

    public function hookDisplayFooterCategory($params){
        if(Tools::getValue('controller') == "manufacturer") {
            $alert_link = $this->context->link->getModuleLink($this->name, 'alert', ['ref_fabricant'=>Tools::getValue('id_manufacturer')]);
        } else {
            $alert_link = $this->context->link->getModuleLink($this->name, 'alert');
        }
        $this->context->smarty->assign(array(
            'alert_link'=>$alert_link,
        ));
        return $this->fetch('module:'.$this->name.'/views/templates/hook/action.tpl');
    
    }

    public function hookDisplayHeaderCategory($params){
        return $this->hookDisplayFooterCategory($params);
    }

    public function hookDisplayOrderConfirmation1($params) {
        return $this->hookDisplayFooterCategory($params);
    }

    public function hookDisplayOrderConfirmation2($params) {
        return $this->hookDisplayFooterCategory($params);
    }

    public function hookActionProductSave($params) {
        $alerts = Alert::findAlerts($params['id_product']);
        
        $combinations = $params['product']->getAttributeCombinations();
        $attribute_combinations = [];

        if($combinations && !empty($combinations)) {
            foreach($combinations as $combination) {
                if(!isset($attribute_combinations[$combination['id_product_attribute']])) {
                    $attribute_combinations[$combination['id_product_attribute']] = [];
                }
                $attribute_combinations[$combination['id_product_attribute']][]=$combination['id_attribute'];
            }
        }
        $data = [];
        if(!empty($alerts) && $alerts) {
            foreach($alerts as $alert) {
                $id_product_attribute = 0;
                if(!empty($alert['attributes'])) {
                    foreach($attribute_combinations as $k=>$v) {
                        if($id_product_attribute == 0 && array_count_values($alert['attributes']) == array_count_values($v)) {
                            $id_product_attribute = (int)$k;
                        }
                    }
                }
                $data[] = [
                    'id_cd_alert' => $alert['id_cd_alert'],
                    'id_product' => $params['id_product'],
                    'id_product_attribute' => $id_product_attribute,
                    'is_alerted' => 0,
                ];
            }
        }
        if($data && !empty($data)) 
            Alert::addNotifications($data);
    }
}
