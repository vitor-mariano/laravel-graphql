<?php

namespace Tests;

trait GraphQL
{
    /**
     * Request GraphQL Endpoint.
     *
     * @param  string $query
     * @param  array  $params
     * @return void
     */
    protected function graphql($query, array $params = [])
    {
        return $this->json('POST', '/graphql', compact('query', 'params'));
    }
}
