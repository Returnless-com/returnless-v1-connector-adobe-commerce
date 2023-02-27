<?php
namespace Returnless\Integration\Model\Api;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\ObjectManagerInterface;
use Magento\GiftCardAccount\Model\Giftcardaccount as GiftCardModel;
use Magento\GiftCardAccount\Model\Pool;
use Magento\Sales\Model\Order;
use Magento\Store\Model\StoreManagerInterface;
use Returnless\Integration\Api\GiftCardAccountInterface;
use Returnless\Integration\Api\ResponseGiftCardAccountInterface;
use Returnless\Integration\Api\ResponseGiftCardAccountInterfaceFactory;
use Psr\Log\LoggerInterface;

/**
 * Class ProductRepository
 */
class GiftCardAccount implements GiftCardAccountInterface
{
    /**
     * const NAMESPACE_MODULE
     */
    const NAMESPACE_MODULE = 'Returnless_Integration';

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var Order
     */
    private $orderModel;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ResponseGiftCardAccountInterfaceFactory
     */
    private $responseGiftCardAccount;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param Order $orderModel
     * @param LoggerInterface $logger
     * @param StoreManagerInterface $storeManager
     * @param ResponseGiftCardAccountInterfaceFactory $responseGiftCardAccount
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        Order $orderModel,
        LoggerInterface $logger,
        StoreManagerInterface $storeManager,
        ResponseGiftCardAccountInterfaceFactory $responseGiftCardAccount
    ) {
        $this->objectManager = $objectManager;
        $this->orderModel = $orderModel;
        $this->logger = $logger;
        $this->storeManager = $storeManager;
        $this->responseGiftCardAccount = $responseGiftCardAccount;
    }

    /**
     * {@inheritDoc}
     *
     * @param string[] $giftCard
     * @return ResponseGiftCardAccountInterface
     * @throws NoSuchEntityException
     */
    public function createGiftCardAccount(array $giftCard)
    {
        $responseItem = $this->responseGiftCardAccount->create();
        $codePool = $this->objectManager->create(Pool::class);
        $codes = $codePool->getCollection()->addFieldToFilter('status', Pool::STATUS_FREE)->getSize();
        if (!$codes) {
            $codePool->generatePool();
        }
        $model = $this->objectManager->create(GiftCardModel::class);
        $order = $this->orderModel->loadByIncrementId($giftCard['order_id']);
        if ($order->getId()) {
            try {
                $websiteId = $this->storeManager->getStore($order->getStoreId())->getWebsiteId();
            } catch (\Exception $e) {
                $websiteId = $this->storeManager->getDefaultStoreView()->getWebsiteId();
                $this->logger->error($e->getMessage());
            }
        } else {
            $websiteId = $this->storeManager->getDefaultStoreView()->getWebsiteId();
        }
        $data = [
            'status' => 1,
            'is_redeemable' => 1,
            'website_id' => $websiteId,
            'balance' => $giftCard['giftcard_amount'],
        ];
        $model->addData($data);

        try {
            $model->save();
            if ($model->getId()) {
                $responseItem->setId($model->getId())
                    ->setGiftcardcode($model->getCode());
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
        return $responseItem;
    }
}
