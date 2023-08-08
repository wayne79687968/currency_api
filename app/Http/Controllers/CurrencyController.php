<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;

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
}
