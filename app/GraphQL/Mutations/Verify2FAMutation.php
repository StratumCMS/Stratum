<?php

namespace App\GraphQL\Mutations;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;

class Verify2FAMutation extends Mutation
{
    protected $attributes = [
        'name' => 'verify2fa',
        'description' => 'Valide la 2FA en vérifiant le code OTP',
    ];

    public function type(): Type
    {
        return Type::listOf(Type::string());
    }

    public function authorize(mixed $root, array $args, mixed $ctx, ?\GraphQL\Type\Definition\ResolveInfo $info = null, ?\Closure $getSelectFields = null): bool
    {
        return auth()->check();
    }

    public function args(): array
    {
        return [
            'otp' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Le code OTP à 6 chiffres',
            ],
        ];
    }

    public function resolve($root, $args)
    {
        $user = Auth::user();
        $secret = session('2fa_secret');

        if (!$secret) {
            throw new \Exception('Clé 2FA absente. Veuillez recommencer.');
        }

        $google2fa = new Google2FA();

        if (!$google2fa->verifyKey($secret, $args['otp'])) {
            throw new \Exception('Code 2FA incorrect.');
        }

        $user->google2fa_secret = Crypt::encrypt($secret);
        $user->two_factor_enabled = true;

        $backupCodes = collect(range(1, 5))->map(fn () => strtoupper(Str::random(10)))->toArray();
        $user->backup_codes = Crypt::encrypt(json_encode($backupCodes));
        $user->save();

        session()->forget(['2fa_setup', '2fa_secret', '2fa_qr']);

        return $backupCodes;
    }
}
