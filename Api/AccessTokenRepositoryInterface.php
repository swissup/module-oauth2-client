<?php

namespace Swissup\OAuth2Client\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Swissup\OAuth2Client\Api\Data\AccessTokenInterface;

interface AccessTokenRepositoryInterface
{
    /**
     * Save access token.
     *
     * @param AccessTokenInterface $accessToken
     * @return AccessTokenInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(AccessTokenInterface $accessToken);

    /**
     * Retrieve access token by ID.
     *
     * @param int $id
     * @return AccessTokenInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($id);

    /**
     * Delete access token.
     *
     * @param AccessTokenInterface $accessToken
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(AccessTokenInterface $accessToken);

    /**
     * Delete access token by ID.
     *
     * @param int $id
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($id);

    /**
     * Retrieve access tokens matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Magento\Framework\Api\SearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);
}
