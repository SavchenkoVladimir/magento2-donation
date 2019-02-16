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

use Savchenko\Donation\Controller\Adminhtml\DonationAbstarct;

/**
 * Class Save
 * @package Savchenko\Donation\Controller\Adminhtml\Donation
 */
class Save extends \Magento\Backend\App\Action
{

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Savchenko\Donation\Model\Donation
     */
    protected $collection;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Save constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Savchenko\Donation\Model\DonationFactory $collectionFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Savchenko\Donation\Model\DonationFactory $collectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->collection = $collectionFactory->create();
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();

        if ($data) {
            try {
                $storeId = $this->storeManager->getStore()->getStoreId();
                $this->collection->saveDonationData($data, $storeId);
                $this->messageManager->addSuccessMessage(__('Successfully saved the item.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);

                return $resultRedirect->setPath('*/*/edit', ['id' => $storeId]);
            } catch (\Exception $exception) {
                $this->messageManager->addErrorMessage($exception->getMessage());
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData($data);

                return $resultRedirect->setPath('*/*/edit', ['id' => $storeId]);
            }
        }

        return $resultRedirect->setPath('*/*/');
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
}
