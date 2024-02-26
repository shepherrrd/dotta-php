<?php 

namespace Dotta\Models;



class HttpDottaResponse
{
    public $status;
    public $message;
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
