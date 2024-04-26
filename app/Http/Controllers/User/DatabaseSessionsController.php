<?php

namespace App\Http\Controllers\User;

use App\Http\Agent;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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

        $connections = collect(DB::connection(config('session.connection'))->table(config('session.table', 'sessions'))
            ->where('user_id', $request->user()->getAuthIdentifier())
            ->orderBy('last_activity', 'desc')
            ->get());

        return response()->json(
            $connections->map(function ($session) use ($request) {
                $agent = $this->createAgent($session);
                return [
                    'id' => $session->id,
                    'ipAddress' => $session->ip_address,
                    'isCurrentDevice' => $session->id === $request->session()->getId(),
                    'lastActive' => Carbon::createFromTimestamp($session->last_activity)->diffForHumans(),
                    'agent' => [
                        'isDesktop' => $agent->isDesktop(),
                        'isTablet' => $agent->isTablet(),
                        'isMobile' => $agent->isMobile(),
                        'platform' => $agent->platform(),
                        'browser' => $agent->browser(),
                    ],
                ];
            })
        );
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
     * Create a new agent instance from the given session.
     */
    private function createAgent(mixed $session): Agent
    {
        return tap(new Agent(), fn($agent) => $agent->setUserAgent($session->user_agent));
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
