<?php

namespace App\Http\Resources\Auth;

use App\Http\Agent;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class DatabaseSessionResource extends JsonResource
{

    public static $wrap = null;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $agent = $this->createAgent($this->resource);
        return [
            'id' => $this->id,
            'ipAddress' => $this->ip_address,
            'device' => $this->getDeviceType($agent),
            'platform' => $agent->platform(),
            'browser' => $agent->browser(),
            'isCurrentDevice' => $this->id === $request->session()->getId(),
            'lastActive' => Carbon::createFromTimestamp($this->last_activity)->diffForHumans(),
        ];
    }

    /**
     * Create a new agent instance from the given session.
     */
    private function createAgent($session): Agent
    {
        return tap(new Agent(), fn($agent) => $agent->setUserAgent($session->user_agent));
    }

    private function getDeviceType($agent): string
    {
        return match (true) {
            $agent->isDesktop() => 'desktop',
            $agent->isTablet() => 'tablet',
            $agent->isMobile() => 'mobile',
            default => 'unknown',
        };
    }
}
