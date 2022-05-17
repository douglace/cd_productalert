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
 *
 * Don't forget to prefix your containers with your own identifier
 * to avoid any conflicts with others containers.
 */

var _bo_cd_ = {
    init: function() {
        this.trigerMenuTab()
    },
    trigerMenuTab: function() {
        let menues = document.querySelectorAll('.v6-menu-left ul a'),
            configsMenue = document.querySelectorAll('.configuration-anchor a');
        Array.prototype.forEach.call(Object.keys(menues), (i) => {
            let el = menues[i]
            el.addEventListener('click', (event) => {
                event.preventDefault()
                this.displayHideTab(el)
            })
        })

        Array.prototype.forEach.call(Object.keys(configsMenue), (i) => {
            let el = configsMenue[i]
            el.addEventListener('click', (event) => {
                event.preventDefault()
                this.displayHideConfig(el)
            })
        })
    },
    /**
     * 
     * @param {HTMLAnchorElement} anchor 
     */
    displayHideTab: function(anchor) {
        let current_li = document.querySelector('.v6-menu-left .active')
        let tab = document.querySelector('#' + anchor.dataset['id'])
        let current_tab = document.querySelector('.v6-tab-item.active')
        if (current_tab != tab) {
            current_tab.classList.remove('active')
            current_li.classList.remove('active')
            tab.classList.add('active')
            tab.scrollIntoView({ behavior: "smooth", block: "end", inline: "nearest" });
            anchor.parentNode.classList.add('active')
        }
    },
    /**
     * 
     * @param {HTMLAnchorElement} anchor 
     */
    displayHideConfig: function(anchor) {
        let current_li = document.querySelector('.configuration-anchor .active')
        let config = document.querySelector('#' + anchor.dataset['id'])
        let current_config = document.querySelector('.configuration-tab-item.active')
        if (current_config != config) {
            current_config.classList.remove('active')
            current_li.classList.remove('active')
            config.classList.add('active')
            config.scrollIntoView({ behavior: "smooth", block: "end", inline: "nearest" });
            anchor.parentNode.classList.add('active')
        }
    }
}

window.addEventListener("DOMContentLoaded", (event) => {
    _bo_cd_.init()
});