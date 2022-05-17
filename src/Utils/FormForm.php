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

namespace Vex6\CdProductalert\Utils;

use HelperForm;
use Context;
use Module;

class FormForm
{

    /**
     * @param HelperForm $h
     */
    private $h;

    /**
     * @param Module $module
     */
    private $module;

    /**
     * @param array $fields_value
     */
    private $fields_value;

    /**
     * @param array $inputs
     */
    private $inputs = [];


    /**
     * @param array $legend
     */
    private $legend = [];

    /**
     * @param array $submit
     */
    private $submit = [];

    /**
     * @param string $currentIndex
     */
    private $currentIndex;

    /**
     * @param string $table
     */
    private $table = 'configuration';

    /**
     * @param string $identifier
     */
    private $identifier;

    /**
     * @param string $token
     */
    private $token;

    /**
     * @param string $toolbar_btn
     */
    private $toolbar_btn;

    /**
     * @param string $ps_help_context
     */
    private $ps_help_context;

    /**
     * @param string $title
     */
    private $title;

    /**
     * @param bool $show_toolbar
     */
    private $show_toolbar = true;

    /**
     * @param Context $context
     */
    private $context;

    /**
     * @param bool $toolbar_scroll
     */
    private $toolbar_scroll = false;

    /**
     * @param int $default_form_language
     */
    private $default_form_language;

    /**
     * @param int $allow_employee_form_lang
     */
    private $allow_employee_form_lang;

    /**
     * @param string $submit_action
     */
    private $submit_action;

    /**
     * @param array $tpl_vars
     */
    private $tpl_vars;

    /**
     * @param bool $bootstrap
     */
    private $bootstrap = false;

    public function __construct($module)
    {
        $this->module = $module;
        $this->context = Context::getContext();
        $this->h = new HelperForm();
    }

    public function setModule($module)
    {
        $this->module = $module;
        return $this;
    }

    public function setSubmit($submit)
    {
        $this->submit = $submit;
        return $this;
    }

    public function setLegend($legend)
    {
        $this->legend = $legend;
        return $this;
    }

    public function setInputs($inputs)
    {
        $this->inputs = $inputs;
        return $this;
    }

    public function addField($field)
    {
        $this->inputs[] = $field;
        return $this;
    }

    public function setBoostrap($bootstrap)
    {
        $this->bootstrap = $bootstrap;
        return $this;
    }

    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    public function setShowToolbar($show_toolbar)
    {
        $this->show_toolbar = $show_toolbar;
        return $this;
    }

    public function setToolbarBtn($toolbar_btn)
    {
        $this->toolbar_btn = $toolbar_btn;
        return $this;
    }

    public function setContext($context)
    {
        $this->context = $context;
        return $this;
    }

    public function setCurrentIndex($currentIndex)
    {
        $this->currentIndex = $currentIndex;
        return $this;
    }

    public function setTable($table)
    {
        $this->table = $table;
        return $this;
    }

    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
        return $this;
    }

    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    public function setHelperContext($ps_help_context)
    {
        $this->ps_help_context = $ps_help_context;
        return $this;
    }

    public function setDefaultFromLanguage($default_form_language)
    {
        $this->default_form_language = $default_form_language;
        return $this;
    }

    public function setAllowEmployeFromLang($allow_employee_form_lang)
    {
        $this->allow_employee_form_lang = $allow_employee_form_lang;
        return $this;
    }

    public function setSubmitAction($submit_action)
    {
        $this->submit_action = $submit_action;
        return $this;
    }

    public function setTplVar($tpl_vars)
    {
        $this->tpl_vars = $tpl_vars;
        return $this;
    }

    /**
     * @param string $key
     * @param string|array|int|bool $value
     * @return self
     */
    public function addValue($key, $value)
    {
        $this->fields_value[$key]=$value;
        return $this;
    }

    public function make()
    {
        $this->h->currentIndex = $this->currentIndex;
        $this->h->table = $this->table;
        $this->h->module = $this->module;
        $this->h->identifier = $this->identifier;
        $this->h->token = $this->token;
        $this->h->toolbar_btn = $this->toolbar_btn;
        $this->h->ps_help_context = $this->ps_help_context;
        $this->h->title = $this->title;
        $this->h->show_toolbar = $this->show_toolbar;
        $this->h->context = $this->context;
        $this->h->toolbar_scroll = $this->toolbar_scroll;
        $this->h->bootstrap = $this->bootstrap;
        $this->h->tpl_vars = $this->tpl_vars;
        $this->h->default_form_language = $this->default_form_language;
        $this->h->allow_employee_form_lang = $this->allow_employee_form_lang;
        $this->h->submit_action = $this->submit_action;
        $this->h->fields_value = $this->fields_value;
        
        return $this->h->generateForm([
            [
                'form' => [
                    'legend' => $this->legend,
                    'input' => $this->inputs,
                    'submit' => $this->submit
                ]
            ]
        ]);
    }
}
