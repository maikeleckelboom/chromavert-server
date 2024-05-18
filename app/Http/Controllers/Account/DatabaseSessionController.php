<?php

namespace App\Http\Controllers\Account;

use App\Http\Agent;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DatabaseSessionController extends Controller
{

    /**
     * Get the current sessions.
     */
    public function index(Request $request): JsonResponse
    {
        if (!$this->isDatabaseDriver()) {
            return response()->json(['message' => 'session-driver-not-supported']);
        }

        $sessions = DB::connection(config('session.connection'))->table(config('session.table', 'sessions'))
            ->where('user_id', $request->user()->getAuthIdentifier())
            ->orderBy('last_activity', 'desc')
            ->get()
            ->collect();

        return response()->json(
            $sessions->map(function ($session) use ($request) {
                $agent = $this->createAgent($session);
                return [
                    'id' => $session->id,
                    'ipAddress' => $session->ip_address,
                    'device' => $this->getDeviceType($agent),
                    'platform' => $agent->platform(),
                    'browser' => $agent->browser(),
                    'isCurrentDevice' => $session->id === $request->session()->getId(),
                    'lastActive' => Carbon::createFromTimestamp($session->last_activity)->diffForHumans(),
                ];
            })->all()
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
        } catch (Exception $e) {
            throw new AuthenticationException($e->getMessage());
        }

        return response()->json(['message' => 'session-invalidated']);
    }

    /**
     * Destroy all other sessions for the current user.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws AuthenticationException
     */
    public function destroyOtherSessions(Request $request): JsonResponse
    {
        if (!$this->isDatabaseDriver()) {
            return response()->json(['message' => 'session-driver-not-supported']);
        }

        try {
            $this->invalidateOtherSessions($request);
        } catch (Exception $e) {
            throw new AuthenticationException($e->getMessage());
        }

        return response()->json(['message' => 'other-sessions-invalidated']);
    }

    /**
     * @param $request
     * @return void
     */
    private function invalidateOtherSessions($request): void
    {
        DB::connection(config('session.connection'))->table(config('session.table', 'sessions'))
            ->where('user_id', $request->user()->getAuthIdentifier())
            ->where('id', '!=', $request->session()->getId())
            ->delete();
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

    /**
     * Create a new agent instance from the given session.
     */
    private function createAgent($session): Agent
    {
        return tap(new Agent(), fn($agent) => $agent->setUserAgent($session->user_agent));
    }

    /**
     * Get the device type for the given agent.
     */
    private function getDeviceType($agent): string
    {
        return match (true) {
            $agent->isDesktop() => 'desktop',
            $agent->isTablet() => 'tablet',
            $agent->isMobile() => 'mobile',
            default => 'unknown',
        };
    }

    /**
     * Determine if the session driver is using the database.
     */
    private function isDatabaseDriver(): bool
    {
        return config('session.driver') === 'database';
    }
}
