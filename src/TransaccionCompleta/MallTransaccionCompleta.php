<?php

namespace Transbank\TransaccionCompleta;

use Transbank\Utils\HttpClient;

class MallTransaccionCompleta
{
    /**
     * @var array contains key-value pairs of
     *            integration_type => url_of_that_integration
     */
    public static $INTEGRATION_TYPES = [
        'LIVE' => 'https://wwww.pagoautomaticocontarjetas.cl/',
        'TEST' => 'https://webpay3gint.transbank.cl/',
        'MOCK' => '',
    ];
    /**
     * @var HttpClient|null
     */
    public static $httpClient = null;
    private static $apiKey = Options::DEFAULT_API_KEY;
    private static $commerceCode = Options::DEFAULT_TRANSACCION_COMPLETA_MALL_COMMERCE_CODE;
    private static $childCommerceCodes = Options::DEFAULT_TRANSACCION_COMPLETA_MALL_CHILD_COMMERCE_CODE;
    private static $integrationType = Options::DEFAULT_INTEGRATION_TYPE;

    /**
     * @return string
     */
    public static function getApiKey()
    {
        return self::$apiKey;
    }

    /**
     * @param string $apiKey
     */
    public static function setApiKey($apiKey)
    {
        self::$apiKey = $apiKey;
    }

    /**
     * @return mixed
     */
    public static function getCommerceCode()
    {
        return self::$commerceCode;
    }

    /**
     * @param mixed $commerceCode
     */
    public static function setCommerceCode($commerceCode)
    {
        self::$commerceCode = $commerceCode;
    }

    /**
     * @return string
     */
    public static function getIntegrationType()
    {
        return self::$integrationType;
    }

    /**
     * @param string $integrationType
     */
    public static function setIntegrationType($integrationType)
    {
        self::$integrationType = $integrationType;
    }

    /**
     * @return array
     */
    public static function getIntegrationTypes()
    {
        return self::$INTEGRATION_TYPES;
    }

    /**
     * @return array
     */
    public static function getChildCommerceCodes()
    {
        return self::$childCommerceCodes;
    }

    /**
     * @param array $childCommerceCode
     *
     * @return MallTransaccionCompleta
     */
    public static function setChildCommerceCodes($childCommerceCodes)
    {
        self::$childCommerceCodes = $childCommerceCodes;
    }

    /**
     * @return HttpClient
     */
    public static function getHttpClient()
    {
        if (!isset(self::$httpClient) || self::$httpClient == null) {
            self::$httpClient = new HttpClient();
        }

        return self::$httpClient;
    }

    public static function getIntegrationTypeUrl($integrationType = null)
    {
        if ($integrationType == null) {
            return self::$INTEGRATION_TYPES[self::$integrationType];
        }

        return self::$INTEGRATION_TYPES[$integrationType];
    }

    public static function configureForTesting()
    {
        self::$apiKey = Options::DEFAULT_API_KEY;
        self::$commerceCode = Options::DEFAULT_TRANSACCION_COMPLETA_MALL_COMMERCE_CODE;
        self::$integrationType = Options::DEFAULT_INTEGRATION_TYPE;
        self::$childCommerceCodes = Options::DEFAULT_TRANSACCION_COMPLETA_MALL_CHILD_COMMERCE_CODE;
    }

    /**
     * Get the default options if none are given.
     *
     * @param Options|null $options
     *
     * @return Options
     */
    public static function getDefaultOptions(Options $options = null)
    {
        if ($options !== null) {
            return $options;
        }

        return new Options(static::getApiKey(), static::getCommerceCode(), static::getIntegrationType());
    }
}
