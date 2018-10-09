<?php
namespace Fortnite\Api;

use Fortnite\Client;

use Fortnite\Api\Store\Item;

class Store extends AbstractApi {
    const STORE_ENDPOINT  = "https://fortnite-public-service-prod11.ol.epicgames.com/fortnite/api/storefront/v2/catalog";

    private $store;

    public function __construct(Client $client) 
    {
        parent::__construct($client);
    }

    /**
     * Gets store information.
     *
     * @return object The store info.
     */
    public function info() : object
    {
        if ($this->store === null) {
            $this->store = $this->get(self::STORE_ENDPOINT);
        }
        return $this->store;
    }

    /**
     * Get the expiration time for the store.
     *
     * @return \DateTime Expiration time.
     */
    public function expiration() : \DateTime
    {
        return $this->info()->expiration;
    }

    /**
     * Get the daily items in the store.
     *
     * @return array Array of Api\Store\Item.
     */
    public function daily() : array
    {
        $returnItems = [];

        $storeItems = $this->storefront('BRDailyStorefront');

        if ($storeItems === null) return $returnItems;

        foreach ($storeItems->catalogEntries as $entry) {
            $returnItems[] = new Store\Item($this->client, $entry);
        }

        return $returnItems;
    }

    /**
     * Get the weekly items in the store.
     *
     * @return array Array of Api\Store\Item.
     */
    public function weekly() : array
    {
        $returnItems = [];

        $storeItems = $this->storefront('BRWeeklyStorefront');

        if ($storeItems === null) return $returnItems;

        foreach ($storeItems->catalogEntries as $entry) {
            $returnItems[] = new Store\Item($this->client, $entry);
        }

        return $returnItems;
    }

    /**
     * Gets a store front based on it's name.
     *
     * @param string $name The store front name.
     * @return object|null The store data, or null if the store
     */
    private function storefront(string $name) : ?object
    {
        foreach ($this->info()->storefronts as $store) {
            if ($store->name === $name) return $store;
        }

        return null;
    }

}