<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Support\MailManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

class SettingsTestMailController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email',
        ]);

        try {
            (new MailManager())->sendTestEmail($request->input('test_email'));

            return back()->with('success', 'E-mail de test envoyé avec succès à ' . $request->input('test_email'));
        } catch (\Exception $e) {
            return back()->withErrors(['mail' => 'Erreur lors de l\'envoi de l\'e-mail : ' . $e->getMessage()]);
        }
    }
}
