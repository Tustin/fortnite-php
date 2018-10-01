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

    public function info() : object
    {
        return $this->item;
    }

    public function devName() : string
    {
        return $this->info()->devName;
    }

    public function offerId() : string
    {
        return $this->info()->offerId;
    }

    public function offerType() : string
    {
        return $this->info()->offerType;
    }

    public function assetPath() : string
    {
        return $this->info()->displayAssetPath;
    }

    public function currencyType() : string
    {
        return $this->info()->prices->currencyType;
    }

    public function basePrice() : float
    {
        return $this->info()->prices->basePrice;
    }

    public function finalPrice() : float
    {
        return $this->info()->prices->finalPrice;
    }

    public function giftable() : bool
    {
        return $this->info()->giftInfo->bIsEnabled; // ????/
    }

    public function refundable() : bool
    {
        return $this->info()->refundable;
    }

    public function grants() : array
    {
        $returnItems = [];

        foreach ($this->info()->itemGrants as $item) {
            $returnItems[] = $item->templateId;
        }

        return $returnItems;
    }

    public function expiration() : \DateTime
    {
        return $this->info()->expiration;
    }

}