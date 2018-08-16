<?php
namespace Fortnite\Api;

use Fortnite\Client;

use Fortnite\Api\Profile;

class Item extends AbstractApi {

    const PROFILE_API     = 'https://fortnite-public-service-prod11.ol.epicgames.com/fortnite/api/game/v2/profile/';

    private $item;
    private $id;

    public function __construct(Client $client, string $id, object $item) 
    {
        parent::__construct($client);

        $this->id = $id;
        $this->item = $item;
    }

    public function id() : string
    {
        return $this->id;
    }

    public function templateId() : string
    {
        return $this->info()->templateId;
    }

    public function isFavorite() : bool
    {
        return $this->info()->attributes->favorite;
    }

    public function xp() : int
    {
        return $this->info()->attributes->xp;
    }

    public function level() : int
    {
        return $this->info()->attributes->level;
    }

    public function info() : object
    { 
        return $this->item;
    }

    public function type() : string
    {
        $explode = explode(':', $this->templateId());
        return substr($explode[0], strlen('Athena'));
    }

    public function name() : string
    {
        return explode(':', $this->templateId())[1];
    }

    public function equip() : void
    {
        $this->postJson(sprintf(self::PROFILE_API . '%s/client/EquipBattleRoyaleCustomization?profileId=athena', $this->client->getAccountId()), [
            'slotName' => $this->type(),
            'itemToSlot' => $this->id(),
            'indexWithinSlot' => 0,
            'variantUpdates' => []
        ]);
    }

}