<?php
namespace Fortnite\Api;

use Fortnite\Client;

class SystemFile extends AbstractApi {

    const SYSTEM_API = 'https://fortnite-public-service-prod11.ol.epicgames.com/fortnite/api/cloudstorage/system';

    private $systemFile;
    private $fileInfo;

    public function __construct(Client $client, object $fileInfo) 
    {
        parent::__construct($client);

        $this->fileInfo = $fileInfo;
    }

    /**
     * Get filename.
     *
     * @return string
     */
    public function filename() : string 
    {
        return $this->fileInfo->filename;
    }

    /**
     * Get unique filename.
     *
     * @return string
     */
    public function uniqueFilename() : string 
    {
        return $this->fileInfo->uniqueFilename;
    }

    /**
     * Get SHA1 file hash.
     *
     * @return string
     */
    public function hash() : string
    {
        return $this->fileInfo->hash;
    }

    /**
     * Get SHA256 file hash.
     *
     * @return string
     */
    public function hash256() : string
    {
        return $this->fileInfo->hash256;
    }

    /**
     * Get length.
     *
     * @return string
     */
    public function length() : string
    {
        return $this->fileInfo->length;
    }
    

    /**
     * Get upload date.
     *
     * @return \DateTime
     */
    public function uploaded() : \DateTime
    {
        return new \DateTime($this->fileInfo->uploaded);
    }

    /**
     * Reads contents of the SystemFile.
     *
     * @return string
     */
    public function read() : string
    {
        if ($this->systemFile === null) {
            $this->systemFile = $this->get(sprintf(self::SYSTEM_API . '/%s', $this->uniqueFilename()));
        }

        return $this->systemFile;
    }

    /**
     * Gets list of all comments in file.
     *
     * @return array
     */
    public function comments() : array
    {
        $comments = [];
        $lines = explode(PHP_EOL, $this->read());
        foreach ($lines as $line) {
            $line = trim(preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $line));
            if (empty($line)) continue;
            if ($line[0] !== ';') continue;

            $comments[] = substr($line, 1);
        }

        return $comments;
    }

    /**
     * Gets list of all groups in file
     * 
     * Example: Unreal Engine .ini files will group variables under a group header like so:
     * [/Script/FortniteGame.FortOnlineAccount] <-- header
     * bShouldJoinFounderChat=false
     * bShouldRequestGeneralChatRooms=false
     * bShouldJoinGlobalChat=false
     *
     * @return array
     */
    public function groups() : array
    {
        $groups = [];
        $groupName = "";

        $lines = preg_split ('/\r\n|\n|\r/', $this->read());
        foreach ($lines as $line) {
            $line = trim(preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $line));
            if (empty($line)) continue;
            if ($line[0] === '[') {
                $groupName = substr($line, 1, mb_strlen($line) - 2);
            } else {
                $pieces = explode('=', $line, 2);
                $groups[$groupName][$pieces[0]] = $pieces[1];
            }
        }
        return $groups;
    }

}