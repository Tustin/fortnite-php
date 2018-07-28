<?php

namespace Fortnite;

use Fortnite\FortniteClient;

class Challenges {
    private $access_token;
    private $account_id;
    private $quests;


    public function __construct($access_token, $profile_data) {
        $this->access_token = $access_token;
        $this->quests = $this->parseQuests((array)$profile_data);
    }

    public function getWeekly(int $week) {
        return array_filter($this->quests, function($value) use($week) {
            $padded_week = str_pad($week, 3, '0', STR_PAD_LEFT);
            return preg_match("/^questbundle_s(\d+)_week_$padded_week/", $value);
        }, ARRAY_FILTER_USE_KEY);
    }

    public function getWeeklys() {
        return array_filter($this->quests, function($value) {
            return preg_match("/^questbundle_s(\d+)_week/", $value);
        }, ARRAY_FILTER_USE_KEY);
    }

    private function parseQuests($items) {
        $quests = [];

        foreach ($items as $key => $item) {
            if (strpos($item->templateId, "ChallengeBundle:") === false) continue;
            $quest_bundle = explode(":", $item->templateId)[1];
            $quests[$quest_bundle] = [];
            // Each ChallengeBundle "item" has an array of all the quests associated with it.
            foreach ($item->attributes->grantedquestinstanceids as $quest_id) {
                $quest = $items[$quest_id];
                if (!$quest) continue;
                $quests[$quest_bundle][] = $quest;
            }
        }

        return $quests;
    }
}