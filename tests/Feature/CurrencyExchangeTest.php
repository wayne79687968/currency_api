<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CurrencyExchangeTest extends TestCase
{
    /**
     * Test the exchange API.
     *
     * @return void
     */
    public function testExchange()
    {
        $response = $this->get("/api/exchange?source=USD&target=JPY&amount=$1,525");

        $response->assertStatus(200)
                 ->assertJson([
                     "result" => "success",
                 ]);

        $data = $response->json();
        $this->assertIsString($data["data"]["amount"]);
        $this->assertStringStartsWith("$", $data["data"]["amount"]);
    }
    
    public function testMissingAmount()
    {
        $response = $this->get("/api/exchange?source=USD&target=JPY");

        $response->assertStatus(400)
                 ->assertJson([
                     "result" => "error",
                     "msg" => "Missing parameters",
                 ]);
    }

    public function testInvalidSource()
    {
        $response = $this->get("/api/exchange?source=BTC&target=JPY&amount=$1,525");

        $response->assertStatus(400)
                 ->assertJson([
                     "result" => "error",
                     "msg" => "There is no source for BTC in the rate data",
                 ]);
    }

    public function testInvalidTarget()
    {
        $response = $this->get("/api/exchange?source=USD&target=BTC&amount=$1,525");

        $response->assertStatus(400)
                 ->assertJson([
                     "result" => "error",
                     "msg" => "There is no target for BTC in the rate data",
                 ]);
    }

    public function testExchangeByExchangerateApi()
    {
        // 模擬外部 API 的回應
        $mockResponse = json_encode([
            'result' => 'success',
            'conversion_rates' => [
                'JPY' => 111.801,
            ],
        ]);

        // 建立一個模擬的 HTTP 客戶端
        $mock = new \GuzzleHttp\Handler\MockHandler([
            new \GuzzleHttp\Psr7\Response(200, [], $mockResponse),
        ]);

        $handlerStack = \GuzzleHttp\HandlerStack::create($mock);
        $client = new \GuzzleHttp\Client(['handler' => $handlerStack]);

        // 將模擬的 HTTP 客戶端綁定到服務容器
        $this->app->instance(\GuzzleHttp\Client::class, $client);

        // 發送請求到 API
        $response = $this->get('/api/exchangerate?source=USD&target=JPY&amount=$1,525');

        // 檢查回應
        $response->assertStatus(200)
                 ->assertJson([
                     'msg' => 'success',
                 ]);

        $data = $response->json();
        $this->assertIsString($data['data']['amount']);
        $this->assertStringStartsWith('$', $data['data']['amount']);
    }
}
