<?php

declare(strict_types=1);

namespace Sushilk\Supabase\Http;

use Psr\Http\Message\ResponseInterface;

class Response
{
    public function body() {

    }

    public function json() {
        return json_decode(true);
    }
}
