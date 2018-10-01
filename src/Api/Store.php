<?php
namespace Fortnite\Api;

use Fortnite\Client;

use Fortnite\Api\StoreItem;

class Store extends AbstractApi {
    const STORE_ENDPOINT  = "https://fortnite-public-service-prod11.ol.epicgames.com/fortnite/api/storefront/v2/catalog";

    private $store;

    public function __construct(Client $client) 
    {
        parent::__construct($client);
    }

    public function info() : object
    {
        if ($this->store === null) {
            $this->store = $this->get(self::STORE_ENDPOINT);
        }
        return $this->store;
    }

    public function expiration() : \DateTime
    {
        return $this->info()->expiration;
    }

    public function daily() : array
    {
        $returnItems = [];

        $storeItems = $this->storefront('BRDailyStorefront');

        if ($storeItems === null) return $returnItems;

        foreach ($storeItems->catalogEntries as $entry) {
            $returnItems[] = new StoreItem($this->client, $entry);
        }

        return $returnItems;
    }

    public function weekly() : array
    {
        $returnItems = [];

        $storeItems = $this->storefront('BRWeeklyStorefront');

        if ($storeItems === null) return $returnItems;

        foreach ($storeItems->catalogEntries as $entry) {
            $returnItems[] = new StoreItem($this->client, $entry);
        }

        return $returnItems;
    }

    private function storefront(string $name) : ?object
    {
        foreach ($this->info()->storefronts as $store) {
            if ($store->name === $name) return $store;
        }

        return null;
    }

}