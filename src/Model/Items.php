<?php
namespace Fortnite\Model;

class Items {
    private $items;

    /**
     * Constructs a new Fortnite\Model\Items instance.
     * @param object $items   Item objects
     */
    public function __construct($items) {
        $this->items = $this->parseItems((array)$items);
    }

    /**
     * Returns item by it's item id.
     * @param  string $id Item id
     * @return object     The item (null if not found)
     */
    public function id($id) {
        foreach ($this->items as $item) {
            if ($item->itemId == $id) return $item;
        }

        return null;
    }

    /**
     * Returns all owned items.
     * @return array The items
     */
    public function all() {
        return $this->items;
    }

    //
    // TODO (Tustin): maybe get all items of a certain type? Not really possible for me since I don't own more than like 5 items and they're all dances and gliders.
    // You would just need to parse 'templateId' for the first part to get the type.
    //

    /**
     * Parses a list of items and removes any non items (for some reason, quests show up in here)
     * @param  array $items Items
     * @return array        Actual items
     */
    private function parseItems($items) {
        $actual = [];
        foreach ($items as $key => $item) {
            if (strpos($item->templateId, "Quest:") !== false) continue;
            $newItem = $item;
            $newItem->itemId = $key; // Add the itemId as a kvp since it only exists as the object identifier initially
            $actual[] = $newItem;
        }
        return $actual;
    }
}
