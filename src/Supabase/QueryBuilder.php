<?php

declare(strict_types=1);

namespace Sushilk\Supabase;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class QueryBuilder
 *
 * Provides a fluent interface for building and executing queries
 * against a Supabase REST API endpoint.
 */
class QueryBuilder
{
    /**
     * The query parameters to be sent with the request.
     *
     * @var array<string, mixed>
     */
    protected array $query = [];

    /**
     * Whether the request should be executed asynchronously.
     */
    protected bool $async = false;

    /**
     * QueryBuilder constructor.
     *
     * @param  Client  $http  The Guzzle HTTP client instance.
     * @param  string  $table  The name of the database table to query.
     */
    public function __construct(
        protected Client $http,
        protected string $table
    ) {}

    /**
     * Set whether the next request should be asynchronous.
     *
     * @param  bool  $state  True to enable async, false for synchronous.
     * @return $this
     */
    public function async(bool $state = true): static
    {
        $this->async = $state;

        return $this;
    }

    /**
     * Set the columns to be retrieved in the SELECT clause.
     *
     * @param  string  $columns  Comma-separated list of columns (defaults to '*').
     * @return $this
     */
    public function select(string $columns = '*'): static
    {
        $this->query['select'] = $columns;

        return $this;
    }

    /**
     * Adds a basic filter to the query.
     *
     * @param  string  $column  The name of the database column.
     * @param  mixed  $value  The value to filter by.
     * @param  string  $operator  The operator to use (e.g., 'eq', 'gt', 'lt'). Defaults to 'eq'.
     * @return static Returns the current instance for method chaining.
     */
    public function where(
        string $column,
        mixed $value,
        string $operator = 'eq'
    ): static {
        if (is_array($value)) {
            $mapped = array_map(function (mixed $item): string {
                return is_scalar($item) || $item instanceof \Stringable ? (string) $item : '';
            }, $value);
            $value = '('.implode(',', $mapped).')';
        } elseif (is_bool($value)) {
            $value = $value ? 'true' : 'false';
        } elseif (is_null($value)) {
            $value = 'null';
        }

        /** @var string|int|float $value */
        $this->query[$column] = "{$operator}.{$value}";

        return $this;
    }

    /**
     * Execute the query and return the response data.
     *
     * Returns a Promise when in async mode, or the decoded JSON array when synchronous.
     *
     * @return array<string, mixed>|PromiseInterface|null
     *
     * @throws GuzzleException
     */
    public function get(): array|PromiseInterface|null
    {
        $url = "rest/v1/{$this->table}";

        if ($this->async) {
            /** @var PromiseInterface $promise */
            $promise = $this->http->getAsync($url, ['query' => $this->query])->then(
                /**
                 * @return array<string, mixed>|null
                 */
                function (ResponseInterface $response): ?array {
                    /** @var array<string, mixed>|null $data */
                    $data = json_decode($response->getBody()->getContents(), true);

                    return $data;
                });

            return $promise;
        }

        $response = $this->http->get($url, [
            'query' => $this->query,
        ]);

        /** @var array<string, mixed>|null $data */
        $data = json_decode($response->getBody()->getContents(), true);

        return $data;
    }

    /**
     * Inserts a new record into the database table.
     *
     * Supports both synchronous and asynchronous execution based on the object's configuration.
     * When executing asynchronously, it returns a Promise that resolves to the decoded JSON array or null.
     *
     * @param  array<string, mixed>  $data  The data payload to insert.
     * @return array<string, mixed>|PromiseInterface|null
     *                                                    - array: Decoded response data for synchronous requests.
     *                                                    - PromiseInterface: Resolves to array|null for asynchronous requests.
     *                                                    - null: If the response body is empty or invalid.
     *
     * @throws GuzzleException If the HTTP request fails or response decoding fails.
     *
     * @throw \InvaildArgumentException If the data provided in not vaild associative array.
     */
    public function insert(array $data = []): array|PromiseInterface|null
    {
        if (empty($data)) {
            return null;
        }

        $stringKeys = array_filter(array_keys($data), 'is_string');
        if (empty($stringKeys)) {
            throw new \InvalidArgumentException('Data must be key-value pairs.');
        }

        $url = "rest/v1/{$this->table}";

        if ($this->async) {
            $promise = $this->http->getAsync($url, [
                'json' => $data,
            ])->then(function (ResponseInterface $response): ?array {
                /** @var array<string, mixed>|null $data */
                $data = json_decode($response->getBody()->getContents(), true);

                return $data;
            });

            return $promise;
        }

        $response = $this->http->post($url, [
            'json' => $data,
        ]);

        /** @var array<string, mixed>|null $postData */
        $postData = json_decode($response->getBody()->getContents(), true);

        return $postData;
    }

    /**
     * Updates an existing record based on the built criteria.
     *
     * @param  array<string, mixed>  $data  The data payload to update.
     * @return array<mixed>|null
     *
     * @throws GuzzleException If the HTTP request fails.
     * @throws \InvalidArgumentException If the data provided is not a valid associative array.
     */
    public function update(array $data = []): ?array
    {
        if (empty($data)) {
            return null;
        }

        $stringKeys = array_filter(array_keys($data), 'is_string');

        if (empty($stringKeys)) {
            throw new \InvalidArgumentException('Data must be valid key-value pairs.');
        }

        // 1. Initialize fallback URL to prevent 'Variable $url might not be defined'
        $url = "rest/v1/{$this->table}";

        // 2. Safeguard against non-stringable types inside the query property
        if (isset($this->query['id'])) {
            $rawId = $this->query['id'];
            $cleanId = (is_string($rawId) || is_numeric($rawId)) ? (string) $rawId : '';
            $url = "rest/v1/{$this->table}?id={$cleanId}";
        }

        $response = $this->http->patch($url, [
            'json' => $data,
            'headers' => [
                'Prefer' => 'return=representation',
            ],
        ]);

        /** @var array<mixed>|null $updateData */
        $updateData = json_decode($response->getBody()->getContents(), true);

        return $updateData;
    }

    /**
     * Deletes a record based on the built criteria.
     *
     * @return array<mixed>|null
     *
     * @throws GuzzleException If the HTTP request fails.
     */
    public function delete(): ?array
    {
        // 1. Initialize fallback URL to prevent 'Variable $url might not be defined'
        $url = "rest/v1/{$this->table}";

        // 2. Safeguard against non-stringable types inside the query property
        if (isset($this->query['id'])) {
            $rawId = $this->query['id'];
            $cleanId = (is_string($rawId) || is_numeric($rawId)) ? (string) $rawId : '';
            $url = "rest/v1/{$this->table}?id={$cleanId}";
        }

        $response = $this->http->delete($url);

        /** @var array<mixed>|null $deleteData */
        $deleteData = json_decode($response->getBody()->getContents(), true);

        return $deleteData;
    }
}
