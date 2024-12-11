<?php

namespace App\Services;

use AleeDhillon\MetaFive\Entities\Trade;
use App\Services\Data\UpdateTradingAccount;
use App\Services\Data\UpdateTradingUser;
use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CTraderService
{
    private string $host = "https://live-quantumcapital.webapi.ctrader.com";
    private string $port = "8443";
    private string $login = "10012";
    private string $password = "Test1234.";
    private string $baseURL = "https://live-quantumcapital.webapi.ctrader.com:8443";
    private string $token = "6f0d6f97-3042-4389-9655-9bc321f3fc1e";
    private string $brokerName = "quantumcapitalglobal";
    private string $environmentName = "live";

    public function connectionStatus(): array
    {
        return [
            'code' => 0,
            'message' => "OK",
        ];
    }

    public function getUser($meta_login)
    {
        $response = Http::acceptJson()->get($this->baseURL . "/v2/webserv/traders/{$meta_login}?token={$this->token}")->json();
        //TraderTO
        Log::debug($response);
        return $response;
    }

    /**
     * @throws ConnectionException
     * @throws Exception
     * Change trader balance
     */
    public function createTrade($meta_login, $amount, $comment, $type): Trade
    {
        $fullUrl = $this->baseURL . "/v2/webserv/traders/$meta_login/changebalance?token=$this->token";

        $payload = [
            'login' => (int) $meta_login,
            'preciseAmount' => (double) $amount,
            'type' => $type,
            'comment' => $comment,
        ];

        $response = Http::acceptJson()
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post($fullUrl, $payload);

        // Log the response details
        if ($type == 'DEPOSIT') {
            Log::info('DEPOSIT Response', [
                'status_code' => $response->status(),
                'response_body' => $response->json(),
            ]);
        }

        if (!$response->successful()) {
            Log::error('API Request Failed', [
                'status_code' => $response->status(),
                'error_body' => $response->json(),
            ]);
            throw new Exception('Failed to process the trade. Please check the logs for more details.');
        }

        // Create the Trade object
        $responseBody = $response->json();
        $trade = new Trade();
        $trade->setAmount($amount);
        $trade->setComment($comment);
        $trade->setType($type);
        $trade->setTicket($responseBody['balanceHistoryId'] ?? null);

        $this->getUserInfo($meta_login);
        return $trade;
    }

    public function getUserInfo($meta_login): void
    {
        $data = $this->getUser($meta_login);
        if ($data) {
            (new UpdateTradingUser)->execute($meta_login, $data);
            (new UpdateTradingAccount)->execute($meta_login, $data);
        }
    }
}

class CTraderAccessRights
{
    const FULL_ACCESS = "FULL_ACCESS";
    const CLOSE_ONLY = "CLOSE_ONLY";
    const NO_TRADING = "NO_TRADING";
    const NO_LOGIN = "NO_LOGIN";
}

class CTraderAccountType
{
    const HEDGED = "HEDGED";
    const NETTED = "NETTED";
}

class ChangeTraderBalanceType
{
    const DEPOSIT = "DEPOSIT";
    const DEPOSIT_NONWITHDRAWABLE_BONUS = "DEPOSIT_NONWITHDRAWABLE_BONUS";
    const WITHDRAW = "WITHDRAW";
    const WITHDRAW_NONWITHDRAWABLE_BONUS = "WITHDRAW_NONWITHDRAWABLE_BONUS";
}
