<?php

declare(strict_types=1);

namespace Sushilk\Supabase\Http;

use GuzzleHttp\Client;

class Response
{
    protected Client $http;

    public function body(): string
    {
        return $this->getBody();
    }

    public function json(): mixed
    {
        return json_decode((string) $this->http->getBody()->getContents(), true);
    }
}
