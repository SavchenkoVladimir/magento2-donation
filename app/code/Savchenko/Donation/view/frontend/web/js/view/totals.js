/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to peers.rom@gmail.com so we can send you a copy immediately.
 *
 * @category Savchenko
 * @package Savchenko_Donation
 * @copyright Copyright (c) Vladimir Savchenko
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

define([
    'ko',
    'uiComponent',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/totals',
    'Magento_Catalog/js/price-utils'

], function (
    ko,
    Component,
    quote,
    totals,
    priceUtils
) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Savchenko_Donation/totals'
        },
        isEnabled: window.checkoutConfig.is_enabled,

        getValue: function () {
            var price = totals.getSegment('donation_amount').value;

            return priceUtils.formatPrice(price, quote.getPriceFormat());
        }
    });
});
