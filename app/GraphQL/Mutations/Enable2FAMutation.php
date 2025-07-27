<?php

namespace App\GraphQL\Mutations;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FA\Google2FA;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;

class Enable2FAMutation extends Mutation
{

    protected $attributes = [
        'name' => 'enable2FA',
        'description' => 'Génère une clé 2FA pour l’utilisateur connecté',
    ];

    public function type(): Type
    {
        return Type::nonNull(Type::listOf(Type::string()));
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
        $google2fa = new Google2FA();

        $secret = $google2fa->generateSecretKey();

        $otpUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );

        $qr = Builder::create()
            ->writer(new PngWriter())
            ->data($otpUrl)
            ->size(300)
            ->margin(10)
            ->build();

        $qrDataUri = $qr->getDataUri();

        session([
            '2fa_setup' => true,
            '2fa_secret' => $secret,
            '2fa_qr' => $qrDataUri,
        ]);

        return [$secret, $qrDataUri];
    }
}
