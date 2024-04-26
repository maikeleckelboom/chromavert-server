<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\DatabaseSessionResource;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DatabaseSessionsController extends Controller
{

    /**
     * Get the current sessions.
     */
    public function index(Request $request): JsonResponse
    {
        if (config('session.driver') !== 'database') {
            return response()->json(['message' => 'session-driver-not-supported']);
        }

        $connections = DB::connection(config('session.connection'))->table(config('session.table', 'sessions'))
            ->where('user_id', $request->user()->getAuthIdentifier())
            ->orderBy('last_activity', 'desc')
            ->get();

        return response()->json(DatabaseSessionResource::collection($connections));
    }


    /**
     * Destroy the given session.
     *
     * @throws AuthenticationException
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $session = DB::connection(config('session.connection'))
            ->table(config('session.table', 'sessions'))
            ->where('id', $id)
            ->where('user_id', $request->user()->getAuthIdentifier())
            ->first();

        if (is_null($session)) {
            return response()->json(['message' => 'session-not-invalidated.']);
        }

        try {
            $this->invalidate($id);
            return response()->json(['message' => 'session-invalidated']);
        } catch (Exception $e) {
            throw new AuthenticationException($e->getMessage());
        }
    }

    /**
     * Delete the session from the database.
     *
     */
    private function invalidate(string $id): void
    {
        DB::connection(config('session.connection'))->table(config('session.table', 'sessions'))
            ->where('id', $id)
            ->delete();
    }

}
