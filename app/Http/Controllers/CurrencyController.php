<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CurrencyController extends BaseController
{
    protected $rates = [
        "TWD" => [
            "TWD" => 1,
            "JPY" => 3.669,
            "USD" => 0.03281
        ],
        "JPY" => [
            "TWD" => 0.26956,
            "JPY" => 1,
            "USD" => 0.00885
        ],
        "USD" => [
            "TWD" => 30.444,
            "JPY" => 111.801,
            "USD" => 1
        ]
    ];

    public function exchange(Request $request)
    {
        if (!$request->has(["amount", "source", "target"])) {
            return response()->json(["result" => "error", "msg" => "Missing parameters"], 400);
        }

        $amount = str_replace(["$", ","], "", $request->input("amount"));
        $amount = floatval($amount);
        $source = $request->input("source");
        $target = $request->input("target");

        $rates = $this->rates;
        if (!isset($rates[$source])) {
            return response()->json(["result" => "error", "msg" => "There is no source for {$source} in the rate data"], 400);
        } else if (!isset($rates[$source][$target])) {
            return response()->json(["result" => "error", "msg" => "There is no target for {$target} in the rate data"], 400);
        }

        $rate = floatval($rates[$source][$target]);
        $convertedAmount = $amount * $rate;

        $convertedAmount = number_format(round($convertedAmount, 2), 2);

        return response()->json(["result" => "success", "data" => ["amount" => "$" . $convertedAmount]]);
    }

    public function exchangeByExchangerateApi(Request $request)
    {
        if (!$request->has(["amount", "source", "target"])) {
            return response()->json(["result" => "error", "msg" => "Missing parameters"], 400);
        }

        $amount = str_replace(["$", ","], "", $request->input("amount"));
        $amount = floatval($amount);
        $source = $request->input("source");
        $target = $request->input("target");

        $apiKey = env("EXCHANGE_RATE_API_KEY");

        $url = "https://v6.exchangerate-api.com/v6/{$apiKey}/latest/{$source}";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode != 200) {
            Log::error("Exchangerate-api Error, url: {$url}, err_msg: {$response}");
            return response()->json(["result" => "error", "msg" => "Exchangerate-api Error, url: {$url}, err_msg: {$response}"], 400);
        }

        $data = json_decode($response, true);

        if (!isset($data["conversion_rates"][$target])) {
            Log::error("no $target in conversion_rates");
            return response()->json(["result" => "error", "msg" => "no $target in conversion_rates"], 400);
        }

        $rate = floatval($data["conversion_rates"][$target]);
        $convertedAmount = $amount * $rate;
        $convertedAmount = number_format(round($convertedAmount, 2), 2);

        return response()->json(["msg" => "success", "data" => ["amount" => "$" . $convertedAmount]]);
    }
}
