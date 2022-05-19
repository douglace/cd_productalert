{extends file='page.tpl'}

{block name="page_title"}
    {$title|escape:'htmlall':'UTF-8'}
{/block}
{block name="page_content_container"}
    <section class="product-alert">
        <form action="{$action_link}" method="post">
            <div class="row">
                <div class="col-md-6 col-xs-12">
                    {if isset($attributes) && !empty($attributes)} 
                        {foreach from=$attributes item=group key=k}
                            <div class="form-group">
                                <label class="form-label">{$group.name}</label>
                                <select class="form-control" name="attribute[{$group.group_id}]">
                                    <option value="">--</option>
                                    {foreach from=$group.attributes item=attr key=attr_key}
                                        <option {if isset($alert) && $alert && isset($alert->attributes) && in_array($attr.id_attribute,$alert->attributes)}selected="selected"{/if} value="{$attr.id_attribute}">
                                            {$attr.name}
                                        </option>
                                    {/foreach}
                                </select>
                            </div>
                        {/foreach}
                    {/if}
                    {if isset($features) && !empty($features)} 
                        {foreach from=$features item=feature key=fk}
                            <div class="form-group">
                                <label class="form-label">{$feature.name}</label>
                                <select class="form-control" name="feature[{$feature.feature_id}]">
                                <option value="">--</option>
                                {foreach from=$feature.values item=value key=val_key}
                                    <option {if isset($alert) && $alert && isset($alert->features) && in_array($value.id_feature_value,$alert->features)}selected="selected"{/if} value="{$value.id_feature_value}">
                                        {$value.value}
                                    </option>
                                {/foreach}
                                </select>
                            </div>
                        {/foreach}
                    {/if}
                </div>

                <div class="col-md-6 col-xs-12">
                {if isset($alert) && $alert}
                    <input value="{$alert->id}" name="id_alert" type="hidden"/>
                {/if}
                    <div class="form-group">
                        <label class="form-label">
                            {l s="Donnez un nom à cette alerte" d="Modules.Cdproductalert.alert"}
                            <span class="text-danger">*</span>
                        </label>
                        <input value="{if isset($alert) && $alert}{$alert->alert_name|escape:'htmlall':'UTF-8'}{/if}" name="alert_name" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label class="form-label">
                            {l s="Votre budget" d="Modules.Cdproductalert.alert"}
                        </label>
                        <input name="alert_price" value="{if isset($alert) && $alert}{$alert->alert_price|escape:'htmlall':'UTF-8'}{/if}" class="form-control" />
                    </div>
                    {if isset($suppliers) && !empty($suppliers)} 
                        <div class="form-group">
                            <label class="form-label">{l s="Fournisseurs" d="Modules.Cdproductalert.alert"}</label>
                            <select class="form-control" name="supplier">
                            <option value="">Sélectionnez un fournisseur</option>
                            {foreach from=$suppliers item=supplier key=fk}
                                <option {if isset($alert) && $alert && $alert->id_supplier == $supplier.id_supplier}selected="selected"{/if} value="{$supplier.id_supplier}">
                                    {$supplier.name}
                                </option>
                            {/foreach}
                            </select>
                        </div>
                    {/if}

                    {if isset($manufacturers) && !empty($manufacturers)} 
                        <div class="form-group">
                            <label class="form-label">{l s="Fabriquant" d="Modules.Cdproductalert.alert"}</label>
                            <select class="form-control" name="manufacturer">
                                <option value="">Sélectionnez une marque</option>
                            {foreach from=$manufacturers item=manufacturer key=fk}
                                <option {if (isset($ref_fabricant) && $ref_fabricant == $manufacturer.id_manufacturer) ||
                                            (isset($alert) && $alert && $alert->id_manufacturer == $manufacturer.id_manufacturer)}
                                        selected="selected"
                                        {/if} value="{$manufacturer.id_manufacturer}">
                                    {$manufacturer.name}
                                </option>
                            {/foreach}
                            </select>
                        </div>
                    {/if} 
                </div>
            </div>
            
            <div class="row ">
                <div class="col-md-12 text-center">
                    <button type="submit" name="submitNewCustomerAlert" value="1" class="btn btn-primary">Enregistrer</button>
                </div>
            </div>
        </form>
    </section>
{/block}