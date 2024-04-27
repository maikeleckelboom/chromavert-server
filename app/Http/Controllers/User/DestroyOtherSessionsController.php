<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class DestroyOtherSessionsController extends Controller
{
    public function __invoke(Request $request): Response
    {
        if (!$this->isDatabaseDriver()) {
            return response()->noContent();
        }

        DB::connection(config('session.connection'))->table(config('session.table', 'sessions'))
            ->where('user_id', $request->user()->getAuthIdentifier())
            ->where('id', '!=', $request->session()->getId())
            ->delete();

        return response()->noContent();
    }

    private function isDatabaseDriver(): bool
    {
        return config('session.driver') === 'database';
    }
}
