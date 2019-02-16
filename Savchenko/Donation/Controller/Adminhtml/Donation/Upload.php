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

namespace Savchenko\Donation\Controller\Adminhtml\Donation;

use Magento\Framework\Controller\ResultFactory;
use Savchenko\Donation\Controller\Adminhtml\DonationAbstarct;
/**
 * Class Upload
 * @package Savchenko\Donation\Controller\Adminhtml\Donation
 */
class Upload extends \Magento\Backend\App\Action
{

    /**
     * @var \Savchenko\Donation\Model\ImageUploader
     */
    protected $imageUploader;

    /**
     * @var \Savchenko\Donation\Model\ResourceModel\Donation\Collection
     */
    protected $collection;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Magento\Framework\AuthorizationInterface
     */
    protected $authorization;

    /**
     * Upload constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Savchenko\Donation\Model\ImageUploader $imageUploader
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Savchenko\Donation\Model\ImageUploader $imageUploader,
        \Savchenko\Donation\Model\ResourceModel\Donation\CollectionFactory $contactCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\AuthorizationInterface $authorization
    )
    {
        $this->collection = $contactCollectionFactory->create();
        $this->imageUploader = $imageUploader;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
        $this->authorization = $authorization;
        parent::__construct($context);
    }

    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(DonationAbstarct::ADMIN_RESOURCE);
    }

    /**
     * Upload file controller action.
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $imageId = $this->_request->getParam('param_name', 'image');

        try {
            $result = $this->imageUploader->saveFileToTmpDir($imageId);
        } catch (\Exception $exception) {
            $this->logger->addError($exception->getMessage());
            $result = ['errorcode' => $exception->getCode()];
        }

        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}
