<?php

namespace GiftGroup\GeoPage\Model\DataProvider\CityPage;

use GiftGroup\GeoPage\Model\Config;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Review\Model\Review;
use Magento\Review\Model\ResourceModel\Review\CollectionFactory as ReviewCollectionFactory;
use Magento\Review\Model\ResourceModel\Rating\Option\Vote\CollectionFactory as RatingCollectionFactory;
use Magento\Review\Model\RatingFactory;

class ProductReviews
{
    private $reviews = null;

    private $storeManager;

    private $reviewCollectionFactory;

    private $ratingCollectionFactory;

    private $ratingFactory;

    public function __construct(
        StoreManagerInterface $storeManager,
        ReviewCollectionFactory $reviewCollectionFactory,
        RatingCollectionFactory $ratingCollectionFactory,
        RatingFactory $ratingFactory
    ) {
        $this->storeManager = $storeManager;
        $this->reviewCollectionFactory = $reviewCollectionFactory;
        $this->ratingCollectionFactory = $ratingCollectionFactory;
        $this->ratingFactory = $ratingFactory;
    }

    public function getReviews($limit = Config::PRODUCT_REVIEW_LIST_DEFAULT_LIMIT, $storeId = null)
    {
        if (null === $this->reviews) {
            if ($storeId == null) {
                $storeId = $this->storeManager->getStore()->getId();
            }            
            $collection = $this->reviewCollectionFactory->create()->addStoreFilter(
                $storeId
            )->addFieldToFilter(
                'entity_id', ['eq' => 1]
            )->addStatusFilter(
                Review::STATUS_APPROVED
            )->setPageSize($limit)->setCurPage(1)->setDateOrder();
            $collection->getSelect()->orderRand();

            $this->reviews = $collection->getItems();
            foreach ($this->reviews as $review) {
                $ratingCollection = $this->ratingCollectionFactory->create()->setReviewFilter(
                    $review->getId()
                )->setStoreFilter(
                    $storeId
                )->addRatingInfo(
                    $storeId
                )->load();
                $review->setRatingVotes($ratingCollection);
            }
        }
        return $this->reviews;
    }

    public function getRatingSummary($reviewId)
    {
        return $this->ratingFactory->create()->getReviewSummary($reviewId);
    }
}
