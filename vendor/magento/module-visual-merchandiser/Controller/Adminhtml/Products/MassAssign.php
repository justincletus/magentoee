<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\VisualMerchandiser\Controller\Adminhtml\Products;

class MassAssign extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\VisualMerchandiser\Model\Position\Cache
     */
    protected $cache;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $layoutFactory;

    /**
     * @var string
     */
    protected $cacheKey;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\VisualMerchandiser\Model\Position\Cache $cache
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\VisualMerchandiser\Model\Position\Cache $cache,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\View\LayoutFactory $layoutFactory
    ) {
        parent::__construct($context);
        $this->productRepository = $productRepository;
        $this->cache = $cache;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->layoutFactory = $layoutFactory;
    }

    /**
     * Index action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        $this->cacheKey = $this->getRequest()->getParam(
            \Magento\VisualMerchandiser\Model\Position\Cache::POSITION_CACHE_KEY
        );
        $productIds = [];
        $notValidSkus = [];
        $response = $this->_objectManager->create('Magento\Framework\DataObject');
        $response->setError(false);

        $layout = $this->layoutFactory->create();

        $sku = $this->_request->getParam('add_product_sku');
        if (trim($sku) == "") {
            $this->messageManager->addError(__('No SKU entered'));
            $response->setError(true);
        } else {
            $skus = preg_split('/\n|\r\n?/', $sku);

            foreach ($skus as $skuItem) {
                if (strlen(trim($skuItem)) > 0) {
                    try {
                        $productIds[] = $this->productRepository->get(trim($skuItem))->getId();
                    } catch (\Magento\Framework\Exception\NoSuchEntityException $e ) {
                        $notValidSkus[] = trim($skuItem);
                    }
                }
            }

            $action = $this->_request->getParam('action');
            if ($action == 'assign') {
                $this->add($productIds);
            } else if ($action == 'remove') {
                $this->remove($productIds);
            } else {
                $this->messageManager->addError(__('Undefined Action'));
                $response->setError(true);
            }
        }

        if (!$this->messageManager->hasMessages()) {
            if (!empty($notValidSkus)) {
                $this->messageManager->addError(
                    sprintf(__("Products with the following SKUs do not exist: %s"), implode($notValidSkus, ', '))
                );
            }

            if (!empty($productIds)) {
                $this->messageManager->addSuccess(sprintf(__("%s SKU(s) processed successfully"), count($productIds)));
            }
        }
        $layout->initMessages();
        $response->setHtmlMessage($layout->getMessagesBlock()->getGroupedHtml());
        return $this->resultJsonFactory->create()->setJsonData($response->toJson());
    }

    /**
     * @param array $productIds
     * @return void
     */
    protected function add(array $productIds)
    {
        $this->cache->prependPositions($this->cacheKey, $productIds);
    }

    /**
     * @param array $productIds
     * @return void
     */
    protected function remove(array $productIds)
    {
        $positions = $this->cache->getPositions($this->cacheKey);
        foreach ($productIds as $productId) {
            unset($positions[$productId]);
        }
        $this->cache->saveData(
            $this->cacheKey,
            $this->cache->reorderPositions($positions)
        );
    }
}
