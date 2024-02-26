<?php 
namespace Dotta\Models;
class DottaConfig
{
    public $apiKey;
    public $publicKey;
    public $privateKey;
    public $environment;
    public $baseUrlProduction;
    public $baseUrlSandbox;

    public $httpClient;

    public function __construct($apiKey, $publicKey, $privateKey, $environment, $baseUrlProduction, $baseUrlSandbox, $httpClient)
    {
        
        
        $this->apiKey = $apiKey;
        $this->publicKey = $publicKey;
        $this->privateKey = $privateKey;
        $this->environment = $environment;
        $this->baseUrlProduction = $baseUrlProduction;
        $this->baseUrlSandbox = $baseUrlSandbox;
        $this->httpClient = $httpClient; 
    }
}