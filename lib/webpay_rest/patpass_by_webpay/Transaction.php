<?php
namespace Transbank\PatPassByWebpay;

use Transbank\PatPassByWebpay\Exceptions\TransactionCreateException;
use Transbank\PatPassByWebpay\Exceptions\TransactionCommitException;
use Transbank\PatPassByWebpay\Exceptions\TransactionStatusException;

class Transaction
{

    const CREATE_TRANSACTION_ENDPOINT = 'rswebpaytransaction/api/webpay/v1.0/transactions';

    const COMMIT_TRANSACTION_ENDPOINT = 'rswebpaytransaction/api/webpay/v1.0/transactions';

    const GET_TRANSACTION_STATUS_ENDPOINT = 'rswebpaytransaction/api/webpay/v1.0/transactions/$TOKEN$';

    public static function create($buyOrder, $sessionId, $amount, $returnUrl, $details, $options = null)
    {
        if ($options == null) {
            $commerceCode = PatPassByWebpay::getCommerceCode();
            $apiKey = PatPassByWebpay::getApiKey();
            $baseUrl = PatPassByWebpay::getIntegrationTypeUrl();
        } else {
            $commerceCode = $options->getCommerceCode();
            $apiKey = $options->getApiKey();
            $baseUrl = PatPassByWebpay::getIntegrationTypeUrl($options->getIntegrationType());
        }

        $headers = [
            "Tbk-Api-Key-Id" => $commerceCode,
            "Tbk-Api-Key-Secret" => $apiKey
        ];

        $payload = json_encode([
            "buy_order" => $buyOrder,
            "session_id" => $sessionId,
            "amount" => $amount,
            "return_url" => $returnUrl,
            "wpm_detail" => $details
        ]);

        $http = PatPassByWebpay::getHttpClient();

        $httpResponse = $http->post($baseUrl,
            self::CREATE_TRANSACTION_ENDPOINT,
            $payload,
            ['headers' => $headers]
        );

        $httpCode = $httpResponse->getStatusCode();
        if ($httpCode != 200 && $httpCode != 204) {
            $reason = $httpResponse->getReasonPhrase();
            $message = "Could not obtain a response from the service: $reason (HTTP code $httpCode)";
            $body = json_decode($httpResponse->getBody(), true);

            if (isset($body["error_message"])) {
                $tbkErrorMessage = $body["error_message"];
                $message = "$message. Details: $tbkErrorMessage";
            }
            throw new TransactionCreateException($message, -1);
        }

        $responseJson = json_decode($httpResponse->getBody(), true);
        if (isset($responseJson["error_message"])) {
            throw new TransactionCreateException($responseJson['error_message']);
        }

        $transactionCreateResponse = new TransactionCreateResponse($responseJson);

        return $transactionCreateResponse;
    }

    public static function commit($token, $options = null)
    {
        if ($options == null) {
            $commerceCode = PatpassByWebpay::getCommerceCode();
            $apiKey = PatpassByWebpay::getApiKey();
            $baseUrl = PatpassByWebpay::getIntegrationTypeUrl();
        } else {
            $commerceCode = $options->getCommerceCode();
            $apiKey = $options->getApiKey();
            $baseUrl = PatpassByWebpay::getIntegrationTypeUrl($options->getIntegrationType());
        }

        $headers = [
            "Tbk-Api-Key-Id" => $commerceCode,
            "Tbk-Api-Key-Secret" => $apiKey
        ];

        $http = PatpassByWebpay::getHttpClient();
        $httpResponse = $http->put($baseUrl,
            self::COMMIT_TRANSACTION_ENDPOINT . "/" . $token,
            null,
            ['headers' => $headers]
        );

        $httpCode = $httpResponse->getStatusCode();
        if ($httpCode != 200 && $httpCode != 204) {
            $reason = $httpResponse->getReasonPhrase();
            $message = "Could not obtain a response from the service: $reason (HTTP code $httpCode)";
            $body = json_decode($httpResponse->getBody(), true);

            if (isset($body["error_message"])) {
                $tbkErrorMessage = $body["error_message"];
                $message = "$message. Details: $tbkErrorMessage";
            }
            throw new TransactionCommitException($message, -1);
        }

        $responseJson = json_decode($httpResponse->getBody(), true);

        if (isset($responseJson["error_message"])) {
            throw new TransactionCommitException($responseJson['error_message']);
        }

        $transactionCommitResponse = new TransactionCommitResponse($responseJson);

        return $transactionCommitResponse;
    }


    public static function getStatus($token, $options = null)
    {
        $url = str_replace('$TOKEN$', $token, self::GET_TRANSACTION_STATUS_ENDPOINT);
        if ($options == null) {
            $commerceCode = PatpassByWebpay::getCommerceCode();
            $apiKey = PatpassByWebpay::getApiKey();
            $baseUrl = PatpassByWebpay::getIntegrationTypeUrl();
        } else {
            $commerceCode = $options->getCommerceCode();
            $apiKey = $options->getApiKey();
            $baseUrl = PatpassByWebpay::getIntegrationTypeUrl($options->getIntegrationType());
        }

        $headers = [
            "Tbk-Api-Key-Id" => $commerceCode,
            "Tbk-Api-Key-Secret" => $apiKey
        ];

        $http =   PatpassByWebpay::getHttpClient();
        $httpResponse = $http->get($baseUrl,
            $url,
            ['headers' => $headers]);


        $httpCode = $httpResponse->getStatusCode();
        if ($httpCode != 200 && $httpCode != 204) {
            $reason = $httpResponse->getReasonPhrase();
            $message = "Could not obtain a response from the service: $reason (HTTP code $httpCode)";
            $body = json_decode($httpResponse->getBody(), true);

            if (isset($body["error_message"])) {
                $tbkErrorMessage = $body["error_message"];
                $message = "$message. Details: $tbkErrorMessage";
            }
            throw new TransactionStatusException($message, -1);
        }

        $responseJson = json_decode($httpResponse->getBody(), true);

        if (isset($responseJson["error_message"])) {
            throw new TransactionStatusException($responseJson['error_message']);
        }

        $transactionStatusResponse = new TransactionStatusResponse($responseJson);

        return $transactionStatusResponse;
    }
}
