<?php

namespace App\Lib;

use Github\Client;

class Github
{
    const DETAIL_PUBLIC_REPOS = 'public_repos';
    const REP_URL = 'html_url';
    const REP_FULLNAME = 'full_name';
    const REP_DESCRIPTION = 'description';

    protected $perPage = 100;

    protected $client;
    protected $api;

    protected $username;
    protected $details;

    public function __construct($username)
    {
        $this->username = $username;

        $this->client = new Client();
    }

    public function getRepositories()
    {
        $repositories = [];

        try {
            $repositories = $this
                ->loadDetails()
                ->fetchRepositories();

        } catch (\Exception $e) {
            // pode ser feito um log
        }

        return $repositories;
    }


    protected function loadDetails()
    {
        $api = $this->client->api('user');

        try {
            $this->details = $api->show($this->username);
        } catch (\Exception $e) {
            throw new \Exception("Error: processing request details.", 404);
        }

        if (!isset($this->details) || !is_array($this->details))
            throw new \Exception("Error: data type of details invalid.", 400);

        return $this;
    }


    protected function fetchRepositories()
    {
        $repositories = [];
        $page = 1;

        $countRepositories = $this->details[self::DETAIL_PUBLIC_REPOS];
        $api = $this->client->api('user');
        $api->setPerPage($this->perPage);

        while (true) {
            $api->setPage($page);

            try {
                $repositories = array_merge($repositories, $api->repositories($this->username));
            } catch (\Exception $e) {
                throw new \Exception("Error: processing request repositories.", 404);
            }

            if ($countRepositories < ($page * $this->perPage))
                break;

            $page++;
        }

        return $repositories;
    }
}