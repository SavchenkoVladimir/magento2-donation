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
    'jquery',
    'ko',
    'uiComponent',
    'Magento_Checkout/js/model/quote',
    'mage/storage',
    'Magento_Checkout/js/model/url-builder',
    'Magento_Customer/js/model/customer',
    'Magento_Checkout/js/model/payment/method-converter',
    'Magento_Checkout/js/model/payment-service',
    'Magento_Ui/js/model/messageList',
    'mage/translate'

], function ($,
             ko,
             Component,
             quote,
             storage,
             urlBuilder,
             customer,
             methodConverter,
             paymentService,
             globalMessageList,
             translate
) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Savchenko_Donation/donation'
        },
        isDonate: ko.observable(false),
        donationImageSrc: window.checkoutConfig.donation_image_url,
        donationAmountOptions: window.checkoutConfig.donation_amounts,
        chosenAmount: ko.observableArray(['--Please Select--']),
        isEnabled: window.checkoutConfig.is_enabled,

        changeDonationAmount: function (data, event) {
            if (!this.isDonate()) {
                return;
            }

            var serviceUrl = urlBuilder.createUrl('/donation/:cartId/amount/:donationAmount', {
                cartId: quote.getQuoteId(),
                donationAmount: event.target.value
            });
            var donation = this;

            return storage.put(
                serviceUrl, false
            ).done(
                function (response) {
                    if (response) {
                        donation.updateTotals();
                    }
                }
            ).fail(
                function (response) {
                    globalMessageList.addErrorMessage({
                        message: translate('An error occurred on the server. Please try to place the order again.')
                    });
                }
            );
        },

        updateTotals: function () {
            var serviceUrl = '';
            var deferred = deferred || $.Deferred();

            if (!customer.isLoggedIn()) {
                serviceUrl = urlBuilder.createUrl('/guest-carts/:cartId/payment-information', {
                    cartId: quote.getQuoteId()
                });
            } else {
                serviceUrl = urlBuilder.createUrl('/carts/mine/payment-information', {});
            }

            return storage.get(
                serviceUrl, false
            ).done(
                function (response) {
                    quote.setTotals(response.totals);
                    paymentService.setPaymentMethods(methodConverter(response.payment_methods));
                    deferred.resolve();
                }
            ).fail(
                function (response) {
                    globalMessageList.addErrorMessage({
                        message: translate('An error occurred on the server. Please try to place the order again.')
                    });
                }
            );
        }
    });
});
