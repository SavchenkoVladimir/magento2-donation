<?php
/**
 * Magecom
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@magecom.net so we can send you a copy immediately.
 *
 * @category Savchenko
 * @package Savchenko_Donation
 * @copyright Copyright (c) 2019 Magecom, Inc. (http://www.magecom.net)
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Savchenko\Donation\Model;

/**
 * Class Donation
 * @package Savchenko\Donation\Model
 */
class Donation extends \Magento\Framework\Model\AbstractModel
{

    const DONATION_KEY = 'donation_amount';

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init('Savchenko\Donation\Model\ResourceModel\Donation');
    }

    /**
     * @param $data
     * @param $storeId
     */
    public function saveDonationData($data, $storeId)
    {
        $storeSetup = $this->getCollection()
            ->addFieldToFilter('store_id', $storeId)
            ->load()
            ->getFirstItem();

        $storeSetup->setStoreId($storeId);

        if (isset($data['is_enabled'])) {
            $storeSetup->setIsEnabled($data['is_enabled']);
        }

        if (isset($data['title'])) {
            $storeSetup->setTitle($data['title']);
        }

        if (isset($data['description'])) {
            $storeSetup->setDescription($data['description']);
        }

        if (isset($data['bar'][0]['url'])) {
            $storeSetup->setImageUrl($data['bar'][0]['url']);
        }

        if (isset($data[self::DONATION_KEY])) {
            $storeSetup->setDonationAmount($this->prepareDonationAmountOptions($data[self::DONATION_KEY]));
        }

        return $storeSetup->save();
    }

    /**
     * @param $amountOptions
     * @return string
     */
    protected function prepareDonationAmountOptions($amountOptions)
    {
        $preparedAmountOptions = [];
        $options = explode(' ', $amountOptions);

        foreach ($options as $option) {
            $number = (float)$option;

            if (is_numeric($number)) {
                $preparedAmountOptions[] = number_format($number, 2, '.', '');
            }
        }

        return json_encode($preparedAmountOptions);
    }
}
