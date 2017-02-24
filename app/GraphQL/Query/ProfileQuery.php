<?php

namespace App\GraphQL\Query;

use Folklore\GraphQL\Support\Query;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use GraphQL;
use JWTAuth;

class ProfileQuery extends Query
{
    protected $attributes = [
        'name' => 'Profile',
        'description' => 'Get profile info.'
    ];

    public function type()
    {
        return GraphQL::type('User');
    }

    public function args()
    {
        return [
            'token' => [
                'type' => Type::nonNull(Type::string())
            ]
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $info)
    {
        return JWTAuth::setToken($args['token'])->toUser();
    }
}
