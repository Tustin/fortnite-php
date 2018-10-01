<?php
namespace Fortnite\Api;

use Fortnite\Client;

class News extends AbstractApi {

    const NEWS_API = 'https://fortnitecontent-website-prod07.ol.epicgames.com/content/api/';

    private $news;

    public function __construct(Client $client, object $news = null) 
    {
        parent::__construct($client);
        
        $this->news = $news;
    }

    /**
     * Get News.
     *
     * @return object News information.
     */
    public function info() : object
    {
        if ($this->news === null) {
            $this->news = $this->get(self::NEWS_API . 'pages/fortnite-game'); // TODO: add support for multiple languages.
        }

        return $this->news;
    }

    /**
     * Get title.
     *
     * @return string Title.
     */
    public function title() : string
    {
        return $this->info()->title;
    }

    /**
     * Get locale.
     *
     * @return string Locale.
     */
    public function locale() : string
    {
        return $this->info()->_locale;
    }

    /**
     * Get last modified DateTime.
     *
     * @return \DateTime Last modified time.
     */
    public function lastModified() : \DateTime
    {
        return new \DateTime($this->info()->lastModified);
    }

    /**
     * Get Battle Royale News.
     *
     * @return News BR news.
     */
    public function battleRoyale() : News
    {
        return new self($this->client, $this->info()->battleroyalenews);
    }

    /**
     * Get Save The World News.
     *
     * @return News STW news.
     */
    public function saveTheWorld() : News
    {
        return new self($this->client, $this->info()->savetheworldnews);
    }

    /**
     * Get each message for the News.
     *
     * @return array Array of Api\Message.
     */
    public function messages() : array
    {
        $returnMessages = [];
        $messages = $this->info()->news->messages;

        if (!isset($messages) || empty($messages)) return $returnMessages;

        foreach ($messages as $message) {
            $returnMessages[] = new Message($this->client, $message);
        }

        return $returnMessages;
    }
}