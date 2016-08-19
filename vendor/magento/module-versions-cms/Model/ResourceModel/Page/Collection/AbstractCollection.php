<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\VersionsCms\Model\ResourceModel\Page\Collection;

use Magento\Cms\Model\Page;

/**
 * Cms page revision collection
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class AbstractCollection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Array of admin users in loaded collection
     *
     * @var array
     */
    protected $_usersHash = null;

    /**
     * Initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_map['fields']['user_id'] = 'main_table.user_id';
        $this->_map['fields']['page_id'] = 'main_table.page_id';
    }

    /**
     * Add filtering by page id.
     * Parameter $page can be int or cms page object.
     *
     * @param Page|array $page
     * @return $this
     */
    public function addPageFilter($page)
    {
        if ($page instanceof Page) {
            $page = $page->getId();
        }

        if (is_array($page)) {
            $page = ['in' => $page];
        }

        $this->addFieldTofilter('page_id', $page);

        return $this;
    }

    /**
     * Adds filter by version access level specified by owner.
     *
     * @param mixed $userId
     * @param string|string[] $accessLevel
     * @return $this
     */
    public function addVisibilityFilter(
        $userId,
        $accessLevel = \Magento\VersionsCms\Model\Page\Version::ACCESS_LEVEL_PUBLIC
    ) {
        $_condition = [];

        if (is_array($userId)) {
            $_condition[] = $this->_getConditionSql($this->_getMappedField('user_id'), ['in' => $userId]);
        } elseif ($userId) {
            $_condition[] = $this->_getConditionSql($this->_getMappedField('user_id'), $userId);
        }

        if (is_array($accessLevel)) {
            $_condition[] = $this->_getConditionSql(
                $this->_getMappedField('access_level'),
                ['in' => $accessLevel]
            );
        } else {
            $_condition[] = $this->_getConditionSql($this->_getMappedField('access_level'), $accessLevel);
        }

        $this->getSelect()->where(implode(' OR ', $_condition));

        return $this;
    }

    /**
     * Mapping user_id to user column with additional value for non-existent users
     *
     * @return $this
     */
    public function addUserColumn()
    {
        $userField = $this->getConnection()->getIfNullSql('main_table.user_id', '-1');
        $this->getSelect()->columns(['user' => $userField]);

        $this->_map['fields']['user'] = $userField;

        return $this;
    }

    /**
     * Join username from system user table
     *
     * @return $this
     */
    public function addUserNameColumn()
    {
        if (!$this->getFlag('user_name_column_joined')) {
            $userField = $this->getConnection()->getIfNullSql('ut.username', '-1');
            $this->getSelect()->joinLeft(
                ['ut' => $this->getTable('admin_user')],
                'ut.user_id = main_table.user_id',
                ['username' => $userField]
            );

            $this->setFlag('user_name_column_joined', true);
        }

        return $this;
    }

    /**
     * Retrieve array of admin users in collection
     *
     * @param bool $idAsKey default true if false then name will be used as key and value
     * @return array
     */
    public function getUsersArray($idAsKey = true)
    {
        if (!$this->_usersHash) {
            $this->_usersHash = [];
            foreach ($this->_toOptionHash('user_id', 'username') as $userId => $username) {
                if ($userId) {
                    if ($idAsKey) {
                        $this->_usersHash[$userId] = $username;
                    } else {
                        $this->_usersHash[$username] = $username;
                    }
                } else {
                    $this->_usersHash['-1'] = __('[No Owner]');
                }
            }

            ksort($this->_usersHash);
        }
        return $this->_usersHash;
    }

    /**
     * Add filtering by user id.
     *
     * @param int|null $userId
     * @return $this
     */
    public function addUserIdFilter($userId = null)
    {
        if ($userId === null) {
            $condition = ['null' => true];
        } else {
            $condition = (int)$userId;
        }

        $this->addFieldTofilter('user_id', $condition);

        return $this;
    }
}
