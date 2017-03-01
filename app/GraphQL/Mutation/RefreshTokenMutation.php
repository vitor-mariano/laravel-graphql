<?php

namespace App\GraphQL\Mutation;

use Folklore\GraphQL\Support\Mutation;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use GraphQL;
use JWTAuth;

class RefreshTokenMutation extends Mutation
{
    protected $attributes = [
        'name' => 'RefreshToken',
        'description' => 'Refresh expired token.'
    ];

    public function type()
    {
        return GraphQL::type('Auth');
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
        $token = JWTAuth::refresh($args['token']);
        
        return compact('token');
    }
}
