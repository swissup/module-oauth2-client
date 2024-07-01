<?php

namespace Swissup\OAuth2Client\Model;

use Swissup\OAuth2Client\Api\AccessTokenRepositoryInterface;
use Swissup\OAuth2Client\Api\Data\AccessTokenInterface;
use Swissup\OAuth2Client\Model\ResourceModel\AccessToken as AccessTokenResource;
use Swissup\OAuth2Client\Model\ResourceModel\AccessToken\CollectionFactory as AccessTokenCollectionFactory;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Api\SearchResultsInterfaceFactory;

class AccessTokenRepository implements AccessTokenRepositoryInterface
{
    private $resource;
    private $accessTokenFactory;
    private $accessTokenCollectionFactory;
    private $searchResultsFactory;
    private $collectionProcessor;

    public function __construct(
        AccessTokenResource $resource,
        AccessTokenFactory $accessTokenFactory,
        AccessTokenCollectionFactory $accessTokenCollectionFactory,
        SearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->accessTokenFactory = $accessTokenFactory;
        $this->accessTokenCollectionFactory = $accessTokenCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @param AccessTokenInterface $accessToken
     * @return AccessTokenInterface
     * @throws CouldNotSaveException
     */
    public function save(AccessTokenInterface $accessToken)
    {
        try {
            $this->resource->save($accessToken);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the access token: %1',
                $exception->getMessage()
            ));
        }
        return $accessToken;
    }

    /**
     * @return mixed
     */
    public function create()
    {
        return $this->accessTokenFactory->create();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function get($id)
    {
        $accessToken = $this->create();
        $this->resource->load($accessToken, $id);
        return $accessToken;
    }

    /**
     * @param $id
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getById($id)
    {
        $accessToken = $this->create();
        $this->resource->load($accessToken, $id);
        if (!$accessToken->getId()) {
            throw new NoSuchEntityException(__('Access token with id "%1" does not exist.', $id));
        }
        return $accessToken;
    }

    public function getByCredentialsHash($hash)
    {
        $accessToken = $this->create();
        $this->resource->load($accessToken, $hash, AccessTokenInterface::CREDENTIAL_HASH);
        if (!$accessToken->getId()) {
            throw new NoSuchEntityException(__('Access token with hash "%1" does not exist.', $hash));
        }
        return $accessToken;
    }

    /**
     * @param AccessTokenInterface $accessToken
     * @return true
     * @throws CouldNotDeleteException
     */
    public function delete(AccessTokenInterface $accessToken)
    {
        try {
            $this->resource->delete($accessToken);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the access token: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @param $id
     * @return true
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($id)
    {
        return $this->delete($this->getById($id));
    }

    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->accessTokenCollectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }
}
