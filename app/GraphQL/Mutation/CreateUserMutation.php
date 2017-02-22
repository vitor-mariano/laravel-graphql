<?php

namespace App\GraphQL\Mutation;

use Folklore\GraphQL\Support\Mutation;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use GraphQL;
use App\Models\User;

class CreateUserMutation extends Mutation
{
    protected $attributes = [
        'name' => 'CreateUser',
        'description' => 'Create a new user.'
    ];

    public function type()
    {
        return GraphQL::type('User');
    }

    public function args()
    {
        return [
            'name' => [
                'type' => Type::string(),
                'rules' => ['required']
            ],
            'email' => [
                'type' => Type::string(),
                'rules' => ['required', 'email', 'unique:users']
            ],
            'password' => [
                'type' => Type::string(),
                'rules' => ['required', 'min:6']
            ]
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $info)
    {
        $fields = array_merge($args, [
            'password' => bcrypt($args['password'])
        ]);
        
        return User::create($fields);
    }
}
