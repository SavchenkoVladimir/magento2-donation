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

namespace Savchenko\Donation\Plugin\Order;

use Savchenko\Donation\Model\Donation;

/**
 * Class TotalsAddDonation
 *
 * @category Magecom
 * @author Magecom
 * @package Savchenko\Donation\Plugin\Order
 */
class TotalsAddDonation
{

    /**
     * @var \Magento\Framework\DataObject
     */
    protected $dataObject;

    /**
     * TotalsAddDonation constructor.
     * @param \Magento\Framework\DataObject $dataObject
     */
    public function __construct(\Magento\Framework\DataObject $dataObject)
    {
        $this->dataObject = $dataObject;
    }

    /**
     * @param \Magento\Sales\Block\Order\Totals\Interceptor $totals
     */
    public function beforeGetTotals($totals)
    {
        if (!$totals->getTotal(Donation::DONATION_KEY)) {
            $order = $totals->getOrder();
            $totalData = [
                'code' => 'donation',
                'strong' => true,
                'value' => $order->getDonationAmount(),
                'label' => __('Donation amount')
            ];
            $this->dataObject->setData($totalData);
            $totals->addTotal($this->dataObject);
        }
    }
}
