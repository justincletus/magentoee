<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VersionsCms\Model;

/**
 * Increment model
 *
 * Description:
 * For example we operate with such entities page, version and revision.
 * We store increments for version and revision in such way for
 * each page we need separate scope of version.
 * In all version we need separate scope for revisions.
 *
 * When we store counter for version it has node = page_id and level = 0
 * When we store counter for revision it has node = version_id (not increment number) and level = 1
 * In case we will add something after revision something like sub-revision
 * we will need to use node = revision_id and level = 2  (for future).
 * Type is only one value '0' at this time bc revision control used only for pages.
 *
 * @method \Magento\VersionsCms\Model\ResourceModel\Increment _getResource()
 * @method \Magento\VersionsCms\Model\ResourceModel\Increment getResource()
 * @method int getType()
 * @method \Magento\VersionsCms\Model\Increment setType(int $value)
 * @method int getNode()
 * @method \Magento\VersionsCms\Model\Increment setNode(int $value)
 * @method int getLevel()
 * @method \Magento\VersionsCms\Model\Increment setLevel(int $value)
 * @method int getLastId()
 * @method \Magento\VersionsCms\Model\Increment setLastId(int $value)
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Increment extends \Magento\Framework\Model\AbstractModel
{
    /*
     * Increment types
     */
    const TYPE_PAGE = 0;

    /*
     * Increment levels
     */
    const LEVEL_VERSION = 0;

    const LEVEL_REVISION = 1;

    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Magento\VersionsCms\Model\ResourceModel\Increment');
    }

    /**
     * Load increment counter by passed node and level
     *
     * @param int $type
     * @param int $node
     * @param int $level
     * @return $this
     */
    public function loadByTypeNodeLevel($type, $node, $level)
    {
        $this->getResource()->loadByTypeNodeLevel($this, $type, $node, $level);

        return $this;
    }

    /**
     * Get incremented value of counter.
     *
     * @return int
     */
    protected function _getNextId()
    {
        $incrementId = $this->getLastId();
        if ($incrementId) {
            $incrementId++;
        } else {
            $incrementId = 1;
        }

        return $incrementId;
    }

    /**
     * Generate new increment id for passed type, node and level.
     *
     * @param int $type
     * @param int $node
     * @param int $level
     * @return string
     */
    public function getNewIncrementId($type, $node, $level)
    {
        $this->loadByTypeNodeLevel($type, $node, $level);

        // if no counter for such combination we need to create new
        if (!$this->getId()) {
            $this->setIncrementType($type)->setIncrementNode($node)->setIncrementLevel($level);
        }

        $newIncrementId = $this->_getNextId();
        $this->setLastId($newIncrementId)->save();

        return $newIncrementId;
    }
}
