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

namespace Savchenko\Donation\Model\Donation;

/**
 * Class DataProvider
 * @package Savchenko\Donation\Model\Donation
 */
class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{

    /**
     * @var \Savchenko\Donation\Model\ResourceModel\Donation\Collection
     */
    protected $collection;

    /**
     * @var array
     */
    protected $loadedData;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * DataProvider constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param \Savchenko\Donation\Model\ResourceModel\Donation\CollectionFactory $collectionFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Savchenko\Donation\Model\ResourceModel\Donation\CollectionFactory $collectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $meta = [],
        array $data = []
    )
    {
        $this->collection = $collectionFactory->create();
        $this->storeManager = $storeManager;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * @inheritdoc
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }

        $storeId = $this->storeManager->getStore()->getStoreId();
        $this->collection->addFieldToFilter('store_id', $storeId);
        $item = $this->collection->load()->getFirstItem();
        $item = $this->prepareDonationAmountOptions($item);

        $this->loadedData[$item->getId()] = $item->getData();

        return $this->loadedData;
    }

    /**
     * @param $item
     * @return mixed
     */
    protected function prepareDonationAmountOptions($item)
    {
        $donationAmountsJson = $item->getDonationAmount();

        if ($donationAmountsJson) {
            $donationAmounts = json_decode($donationAmountsJson);
            $item->setDonationAmount(implode(' ', $donationAmounts));
        }

        return $item;
    }
}
