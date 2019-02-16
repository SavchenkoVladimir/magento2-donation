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
 * Class DonationConfigProvider
 *
 * @category Magecom
 * @author Magecom
 * @package Savchenko\Donation\Model
 */
class DonationConfigProvider implements \Magento\Checkout\Model\ConfigProviderInterface
{

    /**
     * @var \Savchenko\Donation\Model\Donation
     */
    protected $collection;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * DonationConfigProvider constructor.
     * @param DonationFactory $collectionFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Savchenko\Donation\Model\DonationFactory $collectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    )
    {
        $this->collection = $collectionFactory->create();
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        $config = [];
        $storeId = $this->storeManager->getStore()->getStoreId();
        $storeSetup = $this->collection
            ->getCollection()
            ->addFieldToFilter('store_id', $storeId)
            ->load()
            ->getFirstItem();

        $config['is_enabled'] = (bool)$storeSetup->getIsEnabled();
        $config['title'] = $storeSetup->getTitle();
        $config['donation_image_url'] = $storeSetup->getImageUrl();
        $config['description'] = $storeSetup->getDescription();
        $config['donation_amounts'] = $this->getNormalizedDonationAmountOptions($storeSetup->getDonationAmount());

        return $config;
    }

    /**
     * @param $donationAmountOptions
     * @return array
     */
    protected function getNormalizedDonationAmountOptions($donationAmountOptions)
    {
        $normalizedOptions = [];

        if ($donationAmountOptions) {
            $options = json_decode($donationAmountOptions);

            foreach ($options as $option) {
                $number = (float)$option;

                if (is_numeric($number)) {
                    $normalizedOptions[] = $number;
                }
            }
        }

        return $normalizedOptions;
    }
}
