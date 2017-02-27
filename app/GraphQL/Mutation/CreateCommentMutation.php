<?php

namespace App\GraphQL\Mutation;

use Folklore\GraphQL\Support\Mutation;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use GraphQL;
use JWTAuth;

class CreateCommentMutation extends Mutation
{
    protected $attributes = [
        'name' => 'CreateComment',
        'description' => 'Create a new comment.'
    ];

    public function type()
    {
        return GraphQL::type('Comment');
    }

    public function args()
    {
        return [
            'token' => [
                'type' => Type::nonNull(Type::string())
            ],
            'post_id' => [
                'type' => Type::nonNull(Type::int()),
                'rules' => ['exists:posts,id']
            ],
            'body' => [
                'type' => Type::nonNull(Type::string())
            ]
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $info)
    {
        $user = JWTAuth::setToken($args['token'])->toUser();
        
        return $user->comments()->create(
            collect($args)->only('body', 'post_id')->toArray()
        );
    }
}
