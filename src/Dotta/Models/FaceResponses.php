<?php 
namespace Dotta\Models;

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
 
