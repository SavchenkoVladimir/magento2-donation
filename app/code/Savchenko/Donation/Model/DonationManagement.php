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

namespace Savchenko\Donation\Model;

/**
 * Class DonationManagement
 * @package Savchenko\Donation\Model
 */
class DonationManagement implements \Savchenko\Donation\Api\DonationManagementInterface
{

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $cartRepository;

    /**
     * @var \Magento\Quote\Model\QuoteIdMaskFactory
     */
    protected $quoteIdMaskFactory;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * DonationManagement constructor.
     * @param \Magento\Quote\Api\CartRepositoryInterface $cartRepository
     * @param \Magento\Quote\Model\QuoteIdMaskFactory $quoteIdMaskFactory
     */
    public function __construct(
        \Magento\Quote\Api\CartRepositoryInterface $cartRepository,
        \Magento\Quote\Model\QuoteIdMaskFactory $quoteIdMaskFactory,
        \Psr\Log\LoggerInterface $logger
    )
    {
        $this->cartRepository = $cartRepository;
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->logger = $logger;
    }

    /**
     * @param string $quoteId
     * @param string $amount
     * @return bool
     */
    public function set($quoteId, $amount)
    {
        $amount = (float)$amount;
        $quoteId = $this->quoteIdMaskFactory->create()->load($quoteId, 'masked_id')->getQuoteId();
        $quote = $this->cartRepository->getActive($quoteId);
        $cartTotal = (float)$quote->getGrandTotal();
        $cartDonationAmount = (float)$quote->getDonationAmount();

        if ($cartDonationAmount !== 0) {
            $cartTotal = $cartTotal - $cartDonationAmount;
        }

        $newTotal = $cartTotal + $amount;

        if ($quote->isVirtual()) {
            $addressTotalsData = $quote->getBillingAddress()->getData();
        } else {
            $addressTotalsData = $quote->getShippingAddress()->getData();
        }

        $addressTotalsData['grand_total'] = $newTotal;
        $quote->getShippingAddress()->setData($addressTotalsData);

        try {
            $quote->setDonationAmount($amount);
            $quote->collectTotals();
            $quote->save();
        } catch (\Exception $exception) {
            $this->logger->addError($exception->getMessage());
            return false;
        }

        return true;
    }
}
