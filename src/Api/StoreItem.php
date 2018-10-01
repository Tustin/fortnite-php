<?php
namespace Fortnite\Api;

use Fortnite\Client;

class StoreItem extends AbstractApi {
    private $item;

    public function __construct(Client $client, object $item) 
    {
        parent::__construct($client);

        $this->item = $item;
    }

    /**
     * Gets the item info.
     *
     * @return object Item info.
     */
    public function info() : object
    {
        return $this->item;
    }

    /**
     * Gets the internal dev name for the item.
     *
     * @return string Dev name.
     */
    public function devName() : string
    {
        return $this->info()->devName;
    }

    /**
     * Gets the offer id for the item.
     * 
     * This is needed to purchase an item.
     *
     * @return string Offer ID.
     */
    public function offerId() : string
    {
        return $this->info()->offerId;
    }

    /**
     * Gets the offer type.
     *
     * @return string Offer type.
     */
    public function offerType() : string
    {
        return $this->info()->offerType;
    }

    /**
     * Gets the asset path.
     * 
     * This is used for rendering the item in game.
     *
     * @return string Asset path.
     */
    public function assetPath() : string
    {
        return $this->info()->displayAssetPath;
    }

    /**
     * Gets the type of currency used to purchase item.
     *
     * @return string Currency type.
     */
    public function currencyType() : string
    {
        return $this->info()->prices->currencyType;
    }

    /**
     * Gets the base price of the item.
     * 
     * This is the price before any discounts.
     *
     * @return float Base price.
     */
    public function basePrice() : float
    {
        return $this->info()->prices->basePrice;
    }

    /**
     * Gets the final price of the item.
     * 
     * This is the price after any discounts are applied.
     *
     * @return float Final price.
     */
    public function finalPrice() : float
    {
        return $this->info()->prices->finalPrice;
    }

    /**
     * Checks if the item is giftable.
     *
     * @return boolean Is item giftable?
     */
    public function giftable() : bool
    {
        return $this->info()->giftInfo->bIsEnabled; // ????/
    }

    /**
     * Checks if the item can be refunded.
     *
     * @return boolean Can be refunded?
     */
    public function refundable() : bool
    {
        return $this->info()->refundable;
    }

    /**
     * Gets all subitems this item may contain.
     * 
     * This is for items packs that come with multiple items.
     *
     * TODO (Tustin): Why does this not return array of Api\StoreItem?
     * Moreover, won't some items not have the itemGrants property??
     * 
     * @return array Array of template IDs.
     */
    public function grants() : array
    {
        $returnItems = [];

        foreach ($this->info()->itemGrants as $item) {
            $returnItems[] = $item->templateId;
        }

        return $returnItems;
    }

    /**
     * Gets the DateTime the item expires at.
     *
     * @return \DateTime Expiration time.
     */
    public function expiration() : \DateTime
    {
        return $this->info()->expiration;
    }
}