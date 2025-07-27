<?php

namespace App\GraphQL\Mutations;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;
use Illuminate\Support\Facades\Auth;

class Disable2FAMutation extends Mutation
{
    protected $attributes = [
        'name' => 'disable2fa',
        'description' => 'Désactive la double authentification (2FA) de l’utilisateur',
    ];

    public function type(): Type
    {
        return Type::nonNull(Type::string());
    }

    public function authorize(mixed $root, array $args, mixed $ctx, ?\GraphQL\Type\Definition\ResolveInfo $info = null, ?\Closure $getSelectFields = null): bool
    {
        return auth()->check();
    }

    public function args(): array
    {
        return [];
    }

    public function resolve($root, $args)
    {
        $user = Auth::user();

        $user->update([
            'google2fa_secret' => null,
            'two_factor_enabled' => false,
            'backup_codes' => null,
        ]);

        session()->forget(['2fa_setup', '2fa_secret', '2fa_qr']);

        return '2FA désactivée avec succès.';
    }
}
