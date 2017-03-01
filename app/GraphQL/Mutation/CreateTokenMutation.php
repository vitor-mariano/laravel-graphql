<?php

namespace App\GraphQL\Mutation;

use Folklore\GraphQL\Support\Mutation;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use GraphQL;
use JWTAuth;

class CreateTokenMutation extends Mutation
{
    protected $attributes = [
        'name' => 'CreateToken',
        'description' => 'Get user credentials to create a authentication token.'
    ];

    public function type()
    {
        return GraphQL::type('Auth');
    }

    public function args()
    {
        return [
            'email' => [
                'type' => Type::nonNull(Type::string()),
                'rules' => ['required', 'email']
            ],
            'password' => [
                'type' => Type::nonNull(Type::string()),
                'rules' => ['required', 'min:6']
            ]
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $info)
    {
        if (!$token = JWTAuth::attempt($args)) {
            return ['error' => 'Invalid credentials.'];
        }
        
        return compact('token');
    }
}
