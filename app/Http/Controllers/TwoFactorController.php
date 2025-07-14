<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;

class TwoFactorController extends Controller
{
    protected $google2fa;

    public function __construct()
    {
        $this->middleware('auth');
        $this->google2fa = new Google2FA();
    }

    public function enable(Request $request)
    {
        $user = $request->user();

        $secret = $this->google2fa->generateSecretKey();

        $otpUrl = $this->google2fa->getQRCodeUrl(
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

        // ✅ Si AJAX : JSON
        if ($request->expectsJson()) {
            return response()->json([
                'secret' => $secret,
                'qr' => $qrDataUri,
            ]);
        }

        // Sinon fallback normal
        return redirect()->back()->with([
            '2fa_setup' => true,
            '2fa_secret' => $secret,
            '2fa_qr' => $qrDataUri,
        ]);
    }


    public function verify(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'digits:6'],
        ]);

        $user = $request->user();
        $secret = session('2fa_secret');

        if (!$secret) {
            return redirect()->back()->withErrors(['otp' => 'Clé manquante. Veuillez recommencer.']);
        }

        $valid = $this->google2fa->verifyKey($secret, $request->otp);

        if (!$valid) {
            return redirect()->back()->withErrors(['otp' => 'Code incorrect. Veuillez réessayer.']);
        }

        $user->google2fa_secret = Crypt::encrypt($secret);
        $user->two_factor_enabled = true;

        $backupCodes = $this->generateBackupCodes();
        $user->backup_codes = Crypt::encrypt(json_encode($backupCodes));

        $user->save();

        session()->forget(['2fa_setup', '2fa_secret', '2fa_qr']);

        return redirect()->back()->with('backup_codes', $backupCodes);
    }

    public function disable(Request $request)
    {
        $user = $request->user();

        $user->update([
            'google2fa_secret' => null,
            'two_factor_enabled' => false,
            'backup_codes' => null,
        ]);

        session()->forget(['2fa_setup', '2fa_secret', '2fa_qr']);

        return redirect()->back()->with('status', '2FA désactivée.');
    }

    private function generateBackupCodes(): array
    {
        return collect(range(1, 5))->map(fn () => strtoupper(Str::random(10)))->toArray();
    }
}
