<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Support\Model\Report\Group\General;

/**
 * Data Count report
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class DataCountSection extends \Magento\Support\Model\Report\Group\AbstractSection
{
    /**
     * Report title
     */
    const REPORT_TITLE = 'Data Count';

    /**
     * Keys for data count entities
     */
    const KEY_DATA = 'data';
    const KEY_REPORT_TYPE = 'report_type';

    /**
     * Methods that generate data count report for entities
     */
    const DATA_COUNT_GENERATE = 'generateDataCount';
    const DATA_COUNT_CATEGORIES_GENERATE = 'generateCategoriesDataCount';
    const DATA_COUNT_ATTRIBUTES = 'generateAttributesDataCount';
    const DATA_COUNT_CUSTOMER_SEGMENTS = 'generateCustomerSegmentsDataCount';
    const DATA_COUNT_PRODUCTS = 'generateProductsDataCount';
    const DATA_COUNT_PRODUCT_ATTRIBUTES_TABLE_SIZE = 'generateProductsAttributesTableSizeDataCount';

    /**
     * @var int
     */
    protected $counter = 0;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $storeConnection;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $taxConnection;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $customerConnection;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $customerSegmentConnection;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $orderConnection;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $catalogConnection;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $salesRuleConnection;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $targetRuleConnection;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $cmsConnection;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $bannerConnection;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $urlRewriteConnection;

    /**
     * @var \Magento\Support\Model\DataFormatter
     */
    protected $dataFormatter;

    /**
     * @var \Magento\Support\Model\ResourceModel\Report\DataCount\Attributes
     */
    protected $attributes;

    /**
     * @var \Magento\Support\Model\ResourceModel\Report\DataCount\ProductAttributes
     */
    protected $productAttributes;

    /**
     * Entities data for report generation
     *
     * @var array
     */
    protected $entities = [
        [
            self::KEY_DATA => [
                'connection' => 'storeConnection',
                'tableName' => 'store',
                'title' => 'Stores'
            ],
            self::KEY_REPORT_TYPE => self::DATA_COUNT_GENERATE
        ],
        [
            self::KEY_DATA => [
                'connection' => 'taxConnection',
                'tableName' => 'tax_calculation_rule',
                'title' => 'Tax Rules'
            ],
            self::KEY_REPORT_TYPE => self::DATA_COUNT_GENERATE
        ],
        [
            self::KEY_DATA => [
                'connection' => 'customerConnection',
                'tableName' => 'customer_entity',
                'title' => 'Customers'
            ],
            self::KEY_REPORT_TYPE => self::DATA_COUNT_GENERATE
        ],
        [
            self::KEY_DATA => [
                'type' => 'customer',
                'connection' => 'customerConnection'
            ],
            self::KEY_REPORT_TYPE => self::DATA_COUNT_ATTRIBUTES
        ],
        [
            self::KEY_DATA => [
                'type' => 'customer_address',
                'connection' => 'customerConnection'
            ],
            self::KEY_REPORT_TYPE => self::DATA_COUNT_ATTRIBUTES
        ],
        [
            self::KEY_DATA => [],
            self::KEY_REPORT_TYPE => self::DATA_COUNT_CUSTOMER_SEGMENTS
        ],
        [
            self::KEY_DATA => [
                'connection' => 'orderConnection',
                'tableName' => 'sales_order',
                'title' => 'Sales Orders',
            ],
            self::KEY_REPORT_TYPE => self::DATA_COUNT_GENERATE
        ],
        [
            self::KEY_DATA => [
                'connection' => 'catalogConnection',
                'tableName' => 'catalog_category_entity',
                'title' => 'Categories',
            ],
            self::KEY_REPORT_TYPE => self::DATA_COUNT_CATEGORIES_GENERATE
        ],
        [
            self::KEY_DATA => [
                'type' => 'category',
                'connection' => 'catalogConnection'
            ],
            self::KEY_REPORT_TYPE => self::DATA_COUNT_ATTRIBUTES
        ],
        [
            self::KEY_DATA => [],
            self::KEY_REPORT_TYPE => self::DATA_COUNT_PRODUCTS
        ],
        [
            self::KEY_DATA => [
                'type' => 'product',
                'connection' => 'catalogConnection'
            ],
            self::KEY_REPORT_TYPE => self::DATA_COUNT_ATTRIBUTES
        ],
        [
            self::KEY_DATA => [],
            self::KEY_REPORT_TYPE => self::DATA_COUNT_PRODUCT_ATTRIBUTES_TABLE_SIZE
        ],
        [
            self::KEY_DATA => [
                'connection' => 'salesRuleConnection',
                'tableName' => 'salesrule',
                'title' => 'Shopping Cart Price Rules'
            ],
            self::KEY_REPORT_TYPE => self::DATA_COUNT_GENERATE
        ],
        [
            self::KEY_DATA => [
                'connection' => 'salesRuleConnection',
                'tableName' => 'catalogrule',
                'title' => 'Catalog Price Rules'
            ],
            self::KEY_REPORT_TYPE => self::DATA_COUNT_GENERATE
        ],
        [
            self::KEY_DATA => [
                'connection' => 'targetRuleConnection',
                'tableName' => 'magento_targetrule',
                'title' => 'Target Rules'
            ],
            self::KEY_REPORT_TYPE => self::DATA_COUNT_GENERATE
        ],
        [
            self::KEY_DATA => [
                'connection' => 'cmsConnection',
                'tableName' => 'cms_page',
                'title' => 'CMS Pages'
            ],
            self::KEY_REPORT_TYPE => self::DATA_COUNT_GENERATE
        ],
        [
            self::KEY_DATA => [
                'connection' => 'bannerConnection',
                'tableName' => 'magento_banner',
                'title' => 'Banners'
            ],
            self::KEY_REPORT_TYPE => self::DATA_COUNT_GENERATE
        ],
        [
            self::KEY_DATA => [
                'connection' => 'urlRewriteConnection',
                'tableName' => 'url_rewrite',
                'title' => 'URL Rewrites'
            ],
            self::KEY_REPORT_TYPE => self::DATA_COUNT_GENERATE
        ],
        [
            self::KEY_DATA => [
                'connection' => 'storeConnection',
                'tableName' => 'cache',
                'title' => 'Core Cache Records'
            ],
            self::KEY_REPORT_TYPE => self::DATA_COUNT_GENERATE
        ],
        [
            self::KEY_DATA => [
                'connection' => 'storeConnection',
                'tableName' => 'cache_tag',
                'title' => 'Core Cache Tags'
            ],
            self::KEY_REPORT_TYPE => self::DATA_COUNT_GENERATE
        ]
    ];

    /**
     * Array with generated entity data and count
     *
     * @var array
     */
    protected $dataCount = [];

    /**
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Store\Model\ResourceModel\Store $storeConnection
     * @param \Magento\Tax\Model\ResourceModel\TaxClass $taxConnection
     * @param \Magento\Customer\Model\ResourceModel\Customer $customerConnection
     * @param \Magento\CustomerSegment\Model\ResourceModel\Segment $customerSegmentConnection
     * @param \Magento\Sales\Model\ResourceModel\Order $orderConnection
     * @param \Magento\Catalog\Model\ResourceModel\Category $catalogConnection
     * @param \Magento\SalesRule\Model\ResourceModel\Rule $salesRuleConnection
     * @param \Magento\TargetRule\Model\ResourceModel\Rule $targetRuleConnection
     * @param \Magento\Cms\Model\ResourceModel\Page $cmsConnection
     * @param \Magento\Banner\Model\ResourceModel\Banner $bannerConnection
     * @param \Magento\UrlRewrite\Model\ResourceModel\UrlRewrite $urlRewriteConnection
     * @param \Magento\Support\Model\DataFormatter $dataFormatter
     * @param \Magento\Support\Model\ResourceModel\Report\DataCount\Attributes $attributes
     * @param \Magento\Support\Model\ResourceModel\Report\DataCount\ProductAttributes $productAttributes
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Store\Model\ResourceModel\Store $storeConnection,
        \Magento\Tax\Model\ResourceModel\TaxClass $taxConnection,
        \Magento\Customer\Model\ResourceModel\Customer $customerConnection,
        \Magento\CustomerSegment\Model\ResourceModel\Segment $customerSegmentConnection,
        \Magento\Sales\Model\ResourceModel\Order $orderConnection,
        \Magento\Catalog\Model\ResourceModel\Category $catalogConnection,
        \Magento\SalesRule\Model\ResourceModel\Rule $salesRuleConnection,
        \Magento\TargetRule\Model\ResourceModel\Rule $targetRuleConnection,
        \Magento\Cms\Model\ResourceModel\Page $cmsConnection,
        \Magento\Banner\Model\ResourceModel\Banner $bannerConnection,
        \Magento\UrlRewrite\Model\ResourceModel\UrlRewrite $urlRewriteConnection,
        \Magento\Support\Model\DataFormatter $dataFormatter,
        \Magento\Support\Model\ResourceModel\Report\DataCount\Attributes $attributes,
        \Magento\Support\Model\ResourceModel\Report\DataCount\ProductAttributes $productAttributes,
        array $data = []
    ) {
        parent::__construct($logger, $data);
        $this->storeConnection           = $storeConnection->getConnection();
        $this->taxConnection             = $taxConnection->getConnection();
        $this->customerConnection        = $customerConnection->getConnection();
        $this->customerSegmentConnection = $customerSegmentConnection->getConnection();
        $this->orderConnection           = $orderConnection->getConnection();
        $this->catalogConnection         = $catalogConnection->getConnection();
        $this->salesRuleConnection       = $salesRuleConnection->getConnection();
        $this->targetRuleConnection      = $targetRuleConnection->getConnection();
        $this->cmsConnection             = $cmsConnection->getConnection();
        $this->bannerConnection          = $bannerConnection->getConnection();
        $this->urlRewriteConnection      = $urlRewriteConnection->getConnection();
        $this->dataFormatter             = $dataFormatter;
        $this->attributes                = $attributes;
        $this->productAttributes         = $productAttributes;
    }

    /**
     * Generate data and count information
     *
     * Supported counting for:
     * Stores, Tax Rules, Customers, Customer Attributes, Customer Address Attributes, Customer Segments, Orders,
     * Categories, Category Attributes, Products, Product Attributes, Shopping Cart Price Rules, Catalog Price Rules,
     * Target Rules, CMS Pages, Banners, URL Rewrites, Core Cache records, Core Cache Tag records, Log Visitors,
     * Log Visitors Online, Log URLs, Log Quotes, Log Customers
     *
     * @return array
     */
    public function generate()
    {
        foreach ($this->entities as $entity) {
            try {
                switch ($entity[self::KEY_REPORT_TYPE]) {
                    case self::DATA_COUNT_GENERATE:
                        $data = $entity[self::KEY_DATA];
                        $this->generateDataCount($data['connection'], $data['tableName'], $data['title']);
                        $this->counter++;
                        break;
                    case self::DATA_COUNT_CATEGORIES_GENERATE:
                        $data = $entity[self::KEY_DATA];
                        $this->generateCategoriesDataCount($data['connection'], $data['tableName'], $data['title']);
                        $this->counter++;
                        break;
                    case self::DATA_COUNT_ATTRIBUTES:
                        $data = $entity[self::KEY_DATA];
                        $this->generateAttributesDataCount($data['type'], $data['connection']);
                        $this->counter++;
                        break;
                    case self::DATA_COUNT_CUSTOMER_SEGMENTS:
                        $table = $this->customerSegmentConnection->getTableName('magento_customersegment_segment');
                        $this->generateCustomerSegmentsDataCount($table);
                        $this->counter++;
                        break;
                    case self::DATA_COUNT_PRODUCTS:
                        $table = $this->catalogConnection->getTableName('catalog_product_entity');
                        $this->generateProductsDataCount($table);
                        $this->counter++;
                        break;
                    case self::DATA_COUNT_PRODUCT_ATTRIBUTES_TABLE_SIZE:
                        $info = $this->productAttributes->getProductAttributesRowSizeForFlatTable();
                        $this->generateProductsAttributesTableSizeDataCount($info);
                        $this->counter++;
                        break;
                }
            } catch (\Exception $e) {
                $this->logger->error($e);
            }
        }

        return [
            self::REPORT_TITLE => [
                'headers' => ['Entity', 'Count', 'Extra'],
                'data' => $this->dataCount,
                'count' => $this->counter
            ]
        ];
    }

    /**
     * Generate Data Count
     *
     * @param string $connection
     * @param string $tableName
     * @param string $title
     * @return void
     */
    protected function generateDataCount($connection, $tableName, $title)
    {
        $info = $this->countTableRows($connection, $tableName);
        $this->dataCount[] = [$title, isset($info[0]['cnt']) ? $info[0]['cnt'] : 0];

    }

    /**
     * Generate Categories Data Count
     *
     * @param string $connection
     * @param string $tableName
     * @param string $title
     * @return void
     */
    protected function generateCategoriesDataCount($connection, $tableName, $title)
    {
        $info = $this->countTableRows($connection, $tableName);
        $this->dataCount[] = [$title, isset($info[0]['cnt']) ? --$info[0]['cnt'] : 0];
    }

    /**
     * Count Table Rows
     *
     * @param string $connection
     * @param string $tableName
     * @return array
     */
    protected function countTableRows($connection, $tableName)
    {
        $table = $this->$connection->getTableName($tableName);
        $info = $this->$connection->fetchAll("SELECT COUNT(1) as cnt FROM `{$table}`");

        return $info;
    }

    /**
     * Generate Attribute Data Count
     *
     * @param string $attributesType
     * @param string $connection
     * @return void
     */
    protected function generateAttributesDataCount($attributesType, $connection)
    {
        $info = $this->attributes->getAttributesCount($attributesType, $this->$connection);
        foreach ($info as $infoEntry) {
            $this->dataCount[] = $infoEntry;
        }
    }

    /**
     * Generate Customer Segments Data Count
     *
     * @param string $table
     * @return void
     */
    protected function generateCustomerSegmentsDataCount($table)
    {
        $info = $this->customerSegmentConnection->fetchAll("SELECT `is_active` FROM `{$table}`");
        if ($info) {
            $counter = 0;
            foreach ($info as $data) {
                if ($data['is_active']) {
                    $counter++;
                }
            }
            $this->dataCount[] = ['Customer Segments', sizeof($info), 'Active Segments: ' . $counter];
        } else {
            $this->dataCount[] = ['Customer Segments', 0];
        }
    }

    /**
     * Generate Products Data Count
     *
     * @param string $table
     * @return void
     */
    protected function generateProductsDataCount($table)
    {
        $info = $this->catalogConnection->fetchAll(
            "SELECT COUNT(1) as cnt, `type_id` FROM `{$table}` GROUP BY `type_id`"
        );
        if ($info) {
            $counter = 0;
            $extra = '';
            foreach ($info as $data) {
                $counter += $data['cnt'];
                $extra .= $data['type_id'] . ': ' . $data['cnt'] . '; ';
            }
            $this->dataCount[] = ['Products', $counter, 'Product Types: ' . $extra];
        } else {
            $this->dataCount[] = ['Products', 0];
        }
    }

    /**
     * Generate Products Attributes Flat Table Row Size Data Count
     *
     * @param bool|int $info
     * @return void
     */
    protected function generateProductsAttributesTableSizeDataCount($info)
    {
        $this->dataCount[] = [
            'Product Attributes Flat Table Row Size',
            $info > 0 ? $this->dataFormatter->formatBytes($info) : 'n/a',
            $info . ' bytes'
        ];
    }
}
