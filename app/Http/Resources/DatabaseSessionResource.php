<?php

namespace App\Http\Resources;

use App\Http\Agent;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class DatabaseSessionResource extends JsonResource
{
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
            'isCurrentDevice' => $this->id === $request->session()->getId(),
            'lastActive' => Carbon::createFromTimestamp($this->last_activity)->diffForHumans(),
            'agent' => [
                'isDesktop' => $agent->isDesktop(),
                'isTablet' => $agent->isTablet(),
                'isMobile' => $agent->isMobile(),
                'platform' => $agent->platform(),
                'browser' => $agent->browser(),
            ],
        ];
    }

    /**
     * Create a new agent instance from the given session.
     */
    private function createAgent($session): Agent
    {
        return tap(new Agent(), fn($agent) => $agent->setUserAgent($session->user_agent));
    }
}
