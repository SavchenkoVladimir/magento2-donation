<?php
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

namespace Savchenko\Donation\Observer\Cart;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class CartToOrder
 * @package Savchenko\Donation\Observer\Cart
 */
class CartToOrder implements ObserverInterface
{

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $cartRepository;

    /**
     * DonationToOrder constructor.
     * @param \Magento\Quote\Api\CartRepositoryInterface $cartRepository
     */
    public function __construct(
        \Magento\Quote\Api\CartRepositoryInterface $cartRepository
    )
    {
        $this->cartRepository = $cartRepository;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getData('order');
        $quoteId = $order->getQuoteId();
        $quote = $this->cartRepository->getActive($quoteId);
        $order->setDonationAmount($quote->getDonationAmount());
    }
}
