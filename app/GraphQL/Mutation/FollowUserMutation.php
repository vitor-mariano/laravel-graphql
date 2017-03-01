<?php

namespace App\GraphQL\Mutation;

use InvalidArgumentException;
use Folklore\GraphQL\Support\Mutation;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use App\Models\User;
use GraphQL;
use JWTAuth;

class FollowUserMutation extends Mutation
{
    const FOLLOWABLE_ID_FIELD = 'user_id';
    
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
            self::FOLLOWABLE_ID_FIELD => [
                'type' => Type::nonNull(Type::int()),
                'rules' => ['exists:users,id']
            ]
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $info)
    {
        $user = JWTAuth::setToken($args['token'])->toUser();
        
        if ($user->id === $args['user_id']) {
            throw new InvalidArgumentException(
                sprintf('Invalid argument "%s": cannot follow yourself.', self::FOLLOWABLE_ID_FIELD)
            );
        }
        
        $followable = User::find($args['user_id']);
        
        $user->following()->attach($followable->id);
        
        return $followable;
    }
}
