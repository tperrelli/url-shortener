<?php

namespace App\Services;

use Exception;
use Goutte\Client;
use App\Models\Url;

class UrlService
{
    /**
     * class constructor
     * 
     * @param Goutter\Client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * This function retrieves the most 100 visited urls
     * 
     * @return collection
     */
    public function getTop100()
    {
        return Url::take(100)->orderBy('visits', 'DESC')->get();
    }

    /**
     * This function saves a new url
     * 
     * @param array $data
     * @return App\Models\Url
     */
    public function save(array $data): Url
    {
        $crawler = $this->client->request('GET', $data['link']);

        $created = $crawler->filter('title')->each(function($node) use($data) {
            $title = $node->text();

            $generated = $this->generateUrl($data['link']);

            $data['title'] = $title;
            $data['tiny_link'] = $generated;

            return Url::create($data);
        });

        return $created[0];
    }

    /**
     * This function retrieves a url by it's shortened version
     * 
     * @param string $url
     * @throws Exception
     * @return App\Models\Url
     */
    public function retrieveByShortUrl(string $url): Url
    {
        $tinyUrl = Url::firstWhere('tiny_link', $url);

        if (is_null($tinyUrl)) {
            throw new Exception('The provided url doesn\'t exist.');
        }
        
        return $tinyUrl;
    }

    /**
     * This function keeps trying to generate a unique url
     * 
     * @param string $url
     * @return string
     */
    private function generateUrl(string $url): string
    {
        $generated = 'http://'.$this->generateUniqueId();

        if ($this->exists($generated)) {
            $this->generateUrl($url);
        }

        return $generated;
    }

    /**
     *  This function check if the current url exists in database
     * 
     * @param string $url
     * @return bool
     */
    private function exists(string $url): bool
    {
        $record = Url::firstWhere('tiny_link', $url);

        return $record !== null;
    }

    /**
     * This function generate a unique string
     * 
     * @return string
     */
    private function generateUniqueId(): string
    {
        return substr(md5(uniqid(rand(), true)), 0, 6);
    }
}