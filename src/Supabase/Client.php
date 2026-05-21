<?php

declare(strict_types=1);

namespace Sushilk\Supabase;

use GuzzleHttp\Client as GuzzleClient;

/**
 * A lightweight wrapper for interacting with the Supabase REST API.
 */
final class Client
{
    /**
     * @var GuzzleClient The HTTP client instance used for API requests.
     */
    public GuzzleClient $client;

    /**
     * Initializes the Supabase client with connection credentials.
     *
     * @param  string  $url  The base URL of your Supabase project (e.g., https://xyz.supabase.co).
     * @param  string  $token  The API Key / Service Role Token for authentication.
     */
    public function __construct(
        private readonly string $url,
        private readonly string $token
    ) {
        $this->client = new GuzzleClient([
            'base_uri' => rtrim($this->url, '/') . '/',
            'headers' => [
                'apikey' => $this->token,
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    /**
     * Create a new query builder instance for the given table
     *
     * @param  string  $table  Supabase table name
     */
    public function from(string $table): QueryBuilder
    {
        return new QueryBuilder($this->client, $table);
    }

    /**
     * @param  string  $table  Supabase's table
     */
    public function get(string $table): mixed
    {
        $url = 'rest/v1/' . $table;
        $response = $this->client->get($url);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function post(
        string $table,
        array $data = []
    ): mixed {
        $url = 'rest/v1/' . $table;
        $response = $this->client->post($url, ['json' => $data]);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @param string $table  Supabase table name.
     *
     * @parsm int $id supabase table rows id
     *
     * @param array<string, mixed> $data
     */
    public function put(
        string $table,
        ?int $id = null,
        array $data = []
    ): mixed {
        $url = 'rest/v1/' . $table . '?id=eq.' . $id;
        $response = $this->client->put($url, ['json' => $data]);
        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @param string $table  Supabase table name.
     *
     * @param int $id  Supabase rows id.
     *
     * @param array<string, mixed> $data
     */
    public function patch(
        string $table,
        ?int $id = null,
        array $data = []
    ): mixed {
        $url = 'rest/v1/' . $table . '?id=eq.' . $id;
        //$url = http_build_query($url);
        $response = $this->client->patch($url, ['headers' => ['Prefer' => 'return=representation'], 'json' => $data]);
        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @param string $table  Supabase table name.
     *
     * @param int $id Supabase row id.
     */
    public function delete(
        string $table,
        ?int $id = null
    ): mixed {
        $url = 'rest/v1/' . $table . '?id=eq.' . $id;
        $response = $this->client->delete($url);
        return json_decode($response->getBody()->getContents(), true);
    }
}
