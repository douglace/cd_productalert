{if isset($alert) && $alert->id}
<section class="alert-item" id="alert-{$alert->id}">
    <h2>{$alert->alert_name|escape:'htmlall':'UTF-8'}</h2>
    
    <div class="row">
        <div class="col-md-6 col-xs-12">
            <table class="alert-item-table table table-bordered table-hover">
                {if isset($alert->attributes) && !empty($alert->attributes) && $alert->attributes}
                    {foreach from=$alert->attributes item=attribute}
                        <tr>
                            <th>{$attribute.group->name}</th>
                            <td>{$attribute.attribute->name}</td>
                        </tr>
                    {/foreach}
                {/if}

                {if isset($alert->features) && !empty($alert->features)}
                    {foreach from=$alert->features item=feature}
                        <tr>
                            <th>{$feature.feature->name}</th>
                            <td>{$feature.value->value}</td>
                        </tr>
                    {/foreach}
                {/if}
                {if isset($alert->supplier) && $alert->supplier->id}
                    <tr>
                        <th>{l s="Fournisseur" d="Modules.Cdproductalert.view"}</th>
                        <td>{$alert->supplier->name}</td>
                    </tr>
                {/if}
                {if isset($alert->manufacturer) && $alert->manufacturer->id}
                    <tr>
                        <th>{l s="Marque" d="Modules.Cdproductalert.view"}</th>
                        <td>{$alert->manufacturer->name}</td>
                    </tr>
                {/if}
                {if isset($alert->alert_price) && $alert->alert_price}
                    <tr>
                        <th>{l s="Votre budget" d="Modules.Cdproductalert.view"}</th>
                        <td>{$alert->price|escape:'htmlall':'UTF-8'}</td>
                    </tr>
                {/if}
            </table>
        </div>
        <div class="col-md-6 col-xs-12">
            {if isset($alert->products) && !empty($alert->products)}
                <p class="alert__found-title">
                    {l s='Produits similaires' d="Modules.Cdproductalert.view"}
                </p>
                <table class="table table-hover table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>{l s='Nom du produit' d="Modules.Cdproductalert.view"}</th>
                            <th>{l s='Quantité en stock' d="Modules.Cdproductalert.view"}</th>
                            <th>{l s='Prix de l\'article' d="Modules.Cdproductalert.view"}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach from=$alert->products item=product key=k}
                            <tr>
                                <td>
                                    <a href="{$product.link}" _target="blank">{$product.name}</a>
                                </td>
                                <td>
                                    <em>{$product.qty}</em>
                                </td>
                                <td>
                                    <em>{$product.price}</em>
                                </td>
                            </tr>
                        {/foreach}
                    </tbody>
                    
                </table>
            
            {else}
             <p class="alert alert-warning">
                {l s='Aucun gabarit pour le moment' d="Modules.Cdproductalert.view"}
             </p>
            {/if}
        </div>
    </div>
</section>
{/if}
