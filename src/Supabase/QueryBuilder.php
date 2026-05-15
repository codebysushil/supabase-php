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
}
