<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Rma\Model\Service;

use Magento\Rma\Model\Rma;
use Magento\Framework\Api\FilterBuilder;
use Magento\Rma\Api\RmaRepositoryInterface;
use Magento\Rma\Api\RmaManagementInterface;
use Magento\Rma\Model\Rma\PermissionChecker;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Authorization\Model\UserContextInterface;

/**
 * Class RmaManagement
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class RmaManagement implements RmaManagementInterface
{
    /**
     * Permission checker
     *
     * @var PermissionChecker
     */
    protected $permissionChecker;

    /**
     * Rma repository
     *
     * @var RmaRepositoryInterface
     */
    protected $rmaRepository;

    /**
     * User context
     *
     * @var UserContextInterface
     */
    protected $userContext;

    /**
     * Filter builder
     *
     * @var FilterBuilder
     */
    protected $filterBuilder;

    /**
     * Search criteria builder
     *
     * @var SearchCriteriaBuilder
     */
    protected $criteriaBuilder;

    /**
     * Constructor
     *
     * @param PermissionChecker $permissionChecker
     * @param RmaRepositoryInterface $rmaRepository
     * @param UserContextInterface $userContext
     * @param FilterBuilder $filterBuilder
     * @param SearchCriteriaBuilder $criteriaBuilder
     */
    public function __construct(
        PermissionChecker $permissionChecker,
        RmaRepositoryInterface $rmaRepository,
        UserContextInterface $userContext,
        FilterBuilder $filterBuilder,
        SearchCriteriaBuilder $criteriaBuilder
    ) {
        $this->permissionChecker = $permissionChecker;
        $this->rmaRepository = $rmaRepository;
        $this->userContext = $userContext;
        $this->filterBuilder = $filterBuilder;
        $this->criteriaBuilder = $criteriaBuilder;
    }

    /**
     * Save RMA
     *
     * @param \Magento\Rma\Api\Data\RmaInterface $rmaDataObject
     * @return \Magento\Rma\Api\Data\RmaInterface
     */
    public function saveRma(\Magento\Rma\Api\Data\RmaInterface $rmaDataObject)
    {
        $this->permissionChecker->checkRmaForCustomerContext();
        return $this->rmaRepository->save($rmaDataObject);
    }

    /**
     * Return list of rma data objects based on search criteria
     *
     * @param \Magento\Framework\Api\SearchCriteria $searchCriteria
     * @return \Magento\Rma\Api\Data\RmaSearchResultInterface
     */
    public function search(\Magento\Framework\Api\SearchCriteria $searchCriteria)
    {
        $this->permissionChecker->checkRmaForCustomerContext();
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                $this->criteriaBuilder->addFilters(
                    [
                        $filter->getConditionType() => $filter,
                    ]
                );
            }
        }
        $filter = $this->filterBuilder->setField(Rma::CUSTOMER_ID)->setValue($this->userContext->getUserId())->create();
        $this->criteriaBuilder->addFilters(
            [
                'eq' => $filter,
            ]
        );

        return $this->rmaRepository->getList($this->criteriaBuilder->create());
    }
}
