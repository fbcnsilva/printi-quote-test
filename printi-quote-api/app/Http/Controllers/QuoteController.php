<?php

namespace App\Http\Controllers;

use \GuzzleHttp\Client;
use Illuminate\Http\Request;

class QuoteController extends Controller
{
    public function calculate(Request $request)
    {
        $payload = [
            'origin_zip_code' => $request->input('origin_zip_code'),
            'destination_zip_code' => $request->input('destination_zip_code'),
            'volumes' => [
                [
                    'weight' => (float) $request->input('weight'),
                    'volume_type' => 'BOX',
                    'cost_of_goods' => (float) $request->input('cost_of_goods'),
                    'width' => (int) $request->input('width'),
                    'height' => (int) $request->input('height'),
                    'length' => (int) $request->input('length')
                ]
            ]
        ];
        $client = new Client();
        $response = $client->post('https://api.intelipost.com.br/api/v1/quote', [
            'headers' => [
                'api-key'    => '9009f95101bf48b01a50928a2a71ed1ae9083fc1d3c08439b0613dfc38e656c5',
                'accept'     => 'application/json',
            ],
            \GuzzleHttp\RequestOptions::JSON => $payload
        ]);

        if (!$response->getStatusCode() === 200) {
            return response()->json(['error' => 'Internal error'], 500);
        }

        return response()->json(json_decode($response->getBody()->getContents()), 200);
    }
}