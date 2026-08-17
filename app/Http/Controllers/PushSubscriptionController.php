<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PushSubscriptionController extends Controller
{
    public function store(Request $request)
    {
        $dados = $request->validate([
            'endpoint' => 'required|string',
            'keys.p256dh' => 'required|string',
            'keys.auth' => 'required|string',
        ]);

        $request->user()->updatePushSubscription(
            $dados['endpoint'],
            $dados['keys']['p256dh'],
            $dados['keys']['auth']
        );

        return response()->json(['status' => 'ok']);
    }

    public function destroy(Request $request)
    {
        $dados = $request->validate([
            'endpoint' => 'required|string',
        ]);

        $request->user()->deletePushSubscription($dados['endpoint']);

        return response()->json(['status' => 'ok']);
    }
}
