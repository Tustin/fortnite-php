<?php

namespace Fortnite\Model;


class FortniteNews
{
    public $image = null;
    public $hidden = null;
    public $title = null;

    /**
     * Constructs a new Fortnite\Model\FortniteNews instance.
     * @param array $stats Array of mapped news
     */
    public function __construct($news)
    {
        foreach ($news as $key => $value) {

            switch ($key) {
                case "image":
                    $this->image = $value;
                    break;
                case "hidden":
                    $this->hidden = $value;
                    break;
                case "title":
                    $this->title = html_entity_decode($value);
                    break;
                case "body":
                    $this->body = html_entity_decode($value);
                    break;
                case "_type":
                    break;
                default:
                    throw new Exception('News name ' . $key . ' is not supported');
            }
        }

    }
}
