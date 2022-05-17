{*
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
*}



<div class="row">
	<div class="col-md-9">
		<div class="v6-tab-item " id="v6-documentation">
			<div class="panel">
				<h3><i class="icon icon-tags"></i> {l s='Documentation' mod='kreabelhome'}</h3>
				<p>
					&raquo; {l s='You can get a PDF documentation to configure this module' mod='kreabelhome'} :
					<ul>
						<li><a href="{$module_dir|escape:'htmlall':'UTF-8'}docs/readme_en.pdf" target="_blank">{l s='English' mod='kreabelhome'}</a></li>
						<li><a href="{$module_dir|escape:'htmlall':'UTF-8'}docs/readme_fr.pdf" target="_blank">{l s='French' mod='kreabelhome'}</a></li>
					</ul>
				</p>
			</div>
		</div>
		<div class="v6-tab-item" id="v6-description">
			<div class="panel">
				<h3><i class="icon icon-credit-card"></i> {l s='Description' mod='kreabelhome'}</h3>
				<p>
					<strong>{l s='Customer testimonies' mod='kreabelhome'}</strong><br />
					{l s='This testimonials module allows you to display customer reviews on pages such as the home page, the shopping cart page, the product page and a dedicated page to access all customer reviews on a given product.' mod='kreabelhome'}<br />
					{l s='I can configure it using the following configuration form.' mod='kreabelhome'}
				</p>
				<br />
				<p>
					{l s='This module will boost your sales, by encouraging your customers to pay attention to certain products!' mod='kreabelhome'}
				</p>
			</div>
		</div>
		<div class="v6-tab-item active" id="v6-configurations">
			<div class="configuration-tabs">
				<ul class="configuration-anchor">
					<li class="active"> <a href="#config-form" data-id="config-form">{l s='Configurations' mod='cd_productalert'}</a> </li>
				</ul>
				<div class="configuration-tab-item active" id="config-form">
					{$form}
				</div>
			</div>
		</div>

		
	</div>
	<div class="col-md-2">
		<div class="panel v6-menu-left">
			<ul class="">
				<li >
					<a href="#v6-documentation" data-id="v6-documentation">
						<i class="icon icon-tags"></i> {l s='Documentation' mod='kreabelhome'}
					</a>
				</li>
				<li >
					<a href="#v6-description" data-id="v6-description">
						<i class="icon icon-tags"></i> {l s='Description' mod='kreabelhome'}
					</a>
				</li>
				<li class="active">
					<a href="#v6-configurations" data-id="v6-configurations">
						<i class="icon icon-cogs"></i> {l s='Congirurations' mod='kreabelhome'}
					</a>
				</li>
			</ul>
		</div>
	</div>
</div>