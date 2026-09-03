<?php

namespace Tests\Feature\Api;

use Tests\TestCase;

class MarketQuoteTest extends TestCase
{
    public function test_quote_endpoint_is_public_for_login_page(): void
    {
        $response = $this->getJson('/api/market/quote?symbol=BBCA');

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'symbol',
                'price',
                'previous_close',
                'open',
                'high',
                'low',
                'market_cap',
                'currency',
            ]);

        $this->assertTrue($response->json('success'));
        $this->assertSame('BBCA.JK', $response->json('symbol'));
    }
}
