<?php

namespace App\GraphQL\Query;

use GraphQL;
use JWTAuth;
use GraphQL\Type\Definition\Type;
use Folklore\GraphQL\Support\Query;
use GraphQL\Type\Definition\ResolveInfo;

use App\Models\User;

class FeedQuery extends Query
{
    protected $attributes = [
        'name' => 'Feed',
        'description' => 'User feed.'
    ];

    public function type()
    {
        return Type::listOf(GraphQL::type('Post'));
    }

    public function args()
    {
        return [
            'token' => [
                'type' => Type::nonNull(Type::string())
            ],
            'limit' => [
                'type' => Type::int(),
                'defaultValue' => 10
            ],
            'offset' => [
                'type' => Type::int(),
                'defaultValue' => 0
            ]
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $info)
    {
        $user = JWTAuth::setToken($args['token'])->toUser();
        
        return $user->feed()
            ->limit($args['limit'])
            ->offset($args['offset'])
            ->orderBy('id', 'desc')
            ->get();
    }
}
