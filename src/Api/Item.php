<?php
namespace Fortnite\Api;

use Fortnite\Client;

use Fortnite\Api\Profile;

class Item extends AbstractApi {

    const PROFILE_API     = 'https://fortnite-public-service-prod11.ol.epicgames.com/fortnite/api/game/v2/profile/';

    private $item;
    private $id;

    public function __construct(Client $client, string $id, ?object $item) 
    {
        parent::__construct($client);

        $this->id = $id;
        $this->item = $item;
    }

    /**
     * Gets the item ID.
     *
     * @return string Item ID.
     */
    public function id() : string
    {
        return $this->id;
    }

    /**
     * Gets the template ID.
     *
     * @return string Template ID.
     */
    public function templateId() : string
    {
        return $this->info()->templateId;
    }

    /**
     * Checks if item is favorited.
     *
     * @return boolean Is item favorited?
     */
    public function isFavorite() : bool
    {
        return $this->info()->attributes->favorite ?? false;
    }

    /**
     * Gets the amount of XP for the item.
     *
     * @return integer Item XP.
     */
    public function xp() : int
    {
        return $this->info()->attributes->xp ?? 0;
    }

    /**
     * Gets the item level.
     *
     * @return integer Item level.
     */
    public function level() : int
    {
        return $this->info()->attributes->level ?? 0;
    }

    /**
     * Gets the item info.
     *
     * @return object|null Item info.
     */
    public function info() : ?object
    { 
        return $this->item;
    }

    /**
     * Gets the item type (skin, umbrella, etc)
     *
     * @return string Item type.
     */
    public function type() : string
    {
        $explode = explode(':', $this->templateId());
        return substr($explode[0], strlen('Athena'));
    }

    /**
     * Gets the item name.
     *
     * @return string Item name.
     */
    public function name() : string
    {
        return explode(':', $this->templateId())[1] ?? "";
    }

    /**
     * Equips the current item on the logged in user.
     *
     * @return void
     */
    public function equip() : void
    {
        // TODO (Tustin): Find out what parameters the user might want to supply to this.
        // Not totally sure what each of the arguments are for.
        $this->postJson(sprintf(self::PROFILE_API . '%s/client/EquipBattleRoyaleCustomization?profileId=athena', $this->client->accountId()), [
            'slotName' => $this->type(),
            'itemToSlot' => $this->id(), // ?? is this right??
            'indexWithinSlot' => 0,
            'variantUpdates' => []
        ]);
    }
}