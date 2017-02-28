<?php

namespace App\GraphQL\Mutation;

use Folklore\GraphQL\Support\Mutation;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use App\Models\User;
use GraphQL;
use JWTAuth;

class FollowUserMutation extends Mutation
{
    protected $attributes = [
        'name' => 'FollowUser',
        'description' => 'Follow a user.'
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
            ],
            'user_id' => [
                'type' => Type::nonNull(Type::int()),
                'rules' => ['exists:users,id']
            ]
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $info)
    {
        $user = JWTAuth::setToken($args['token'])->toUser();
        
        $followable = User::find($args['user_id']);
        
        $user->following()->attach($followable->id);
        
        return $followable;
    }
}
