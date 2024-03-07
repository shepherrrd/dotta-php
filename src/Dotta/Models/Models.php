<?php 
namespace Dotta\Models;

class DottaEnvironment {
    
    const SANDBOX = 1;
    const PRODUCTION = 2;
}
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

class DottaResponse
{
    public $status;
    public $message;
    public $data;
    public function __construct($status = false, $message = '', $data = null)
    {
        $this->status = $status;
        $this->message = $message;
        $this->data = $data;
    }
}


class ErrorResponse
{
    public $errorCode;
    public $errorMessage;
}

class FaceAttributesResponse extends ErrorResponse
{
    public $male;
    public $female;
    public $age;
    public $smile;
    public $eyesOpen;
    public $passiveLiveness;
    public $orientation;
}

class FaceDetectResponse extends ErrorResponse
{
    public $pointX;
    public $pointY;
    public $width;
    public $padding;
    public $angle;
    public $orientation;
}

class FaceMatchResponse extends ErrorResponse
{
    public $similarityScore;
}

class FaceActiveLivenessResponse extends ErrorResponse
{
    public $livenessScore;
}
 
class HttpDottaResponse
{
    public $status;
    public $message;
    public $data;
}

class HttpDottaFaceAttributesResponse extends HttpDottaResponse
{
    public $data; // Instance of HttpDottaFaceAttributesResponseData
}

class HttpDottaFaceAttributesResponseData
{
    public $errorCode;
    public $errorMessage;
    public $male;
    public $female;
    public $age;
    public $smile;
    public $eyesOpen;
    public $passiveLiveness;
    public $orientation;
}

class HttpDottaFaceDetectResponse extends HttpDottaResponse
{
    public $data; // Instance of HttpDottaFaceDetectResponseData
}

class HttpDottaFaceDetectResponseData
{
    public $errorCode;
    public $errorMessage;
    public $pointX;
    public $pointY;
    public $width;
    public $padding;
    public $angle;
    public $orientation;
}

class HttpDottaFaceMatchResponse extends HttpDottaResponse
{
    public $data; // Instance of HttpDottaFaceMatchResponseData
}

class HttpDottaFaceMatchResponseData
{
    public $errorCode;
    public $errorMessage;
    public $similarityScore;
}

class HttpDottaFaceActiveLivenessResponse extends HttpDottaResponse
{
    public $data; // Instance of HttpDottaFaceActiveLivenessResponseData
}

class HttpDottaFaceActiveLivenessResponseData
{
    public $errorCode;
    public $errorMessage;
    public $livenessScore;
}
