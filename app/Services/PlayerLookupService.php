<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class PlayerLookupService
{
    public function findByUid(string $uid): ?array
    {
        $rows = $this->request('playerInfo', ['uid' => $uid]);

        return $rows[0] ?? null;
    }

    public function findByPhone(string $phone): array
    {
        return $this->request('findPhonePlayer', ['phone' => $phone]);
    }

    public function findByIdCard(string $idCard): array
    {
        return $this->request('findshenfenzhengPlayer', ['shenfenzheng' => $idCard]);
    }

    public function ban(string $uid): void
    {
        $this->callAction('banPlayer', $uid);
    }

    public function unban(string $uid): void
    {
        $this->callAction('unbanPlayer', $uid);
    }

    private function callAction(string $method, string $uid): void
    {
        $response = Http::timeout((int) config('game.request_timeout', 15))->get(config('game.stats_base_url'), [
            'method' => $method,
            'uid' => $uid,
        ]);

        $response->throw();
    }

    private function request(string $method, array $query): array
    {
        $response = Http::timeout((int) config('game.request_timeout', 15))->get(config('game.stats_base_url'), [
            'method' => $method,
            ...$query,
        ]);

        $response->throw();

        return $this->extractPlayers($response->json() ?? []);
    }

    private function extractPlayers(array $payload): array
    {
        if ($this->isPlayer($payload)) {
            return [$payload];
        }

        foreach (['data', 'list', 'rows', 'players', 'player', 'user', 'info'] as $key) {
            $value = Arr::get($payload, $key);

            if (! is_array($value)) {
                continue;
            }

            if ($this->isPlayer($value)) {
                return [$value];
            }

            if (array_is_list($value)) {
                return array_values(array_filter($value, fn ($row): bool => is_array($row)));
            }
        }

        if (array_is_list($payload)) {
            return array_values(array_filter($payload, fn ($row): bool => is_array($row)));
        }

        return [];
    }

    private function isPlayer(array $value): bool
    {
        return Arr::hasAny($value, ['_id', 'uid', 'code', 'phone', 'shenfenzheng']);
    }
}
