<?php

namespace Tests\Feature\Api;

use App\Models\EmitenEvent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmitenEventApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_events_page_is_available_to_authenticated_users(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/events')
            ->assertOk();
    }

    public function test_events_api_returns_filtered_bbca_events_for_selected_month(): void
    {
        $user = User::factory()->create();

        EmitenEvent::create([
            'stock_symbol' => 'BBCA',
            'title' => 'RUPS Tahunan 2024',
            'description' => 'Rapat umum pemegang saham',
            'type' => 'rups',
            'event_date' => '2024-04-22',
            'value' => null,
        ]);

        EmitenEvent::create([
            'stock_symbol' => 'BBCA',
            'title' => 'Dividen Interim 2024',
            'description' => 'Pembagian dividen interim',
            'type' => 'dividen',
            'event_date' => '2024-11-15',
            'value' => 235,
        ]);

        $this->actingAs($user)
            ->getJson('/api/events?symbol=BBCA&month=2024-04')
            ->assertOk()
            ->assertJsonCount(1, 'events')
            ->assertJsonPath('events.0.title', 'RUPS Tahunan 2024');
    }
}
