<?php 

namespace Dotta;
use Exception;
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



class Dotta {
    public $apiKey;
    public $publicKey;
    public $privateKey;
    public $environment;
    public $baseUrlProduction;
    public $baseUrlSandbox;
    private $allowFileExtensions = ['.png', '.jpeg', '.jpg'];
    private $httpClient;

     /**
     * Constructor for Dotta class.
     * 
     * @param DottaConfig $config Configuration object.
     */
    public function __construct($config) {
        
        $this->publicKey = $config->publicKey;
        $this->privateKey = $config->privateKey;
        $this->environment = $config->environment;
        $this->baseUrlProduction = $config->baseUrlProduction;
        $this->baseUrlSandbox = $config->baseUrlSandbox;
        $this->httpClient = $config->httpClient;

        if (!empty($config->apiKey)) {
            $this->apiKey = $config->apiKey;
        } else {
            $plainText = $config->publicKey . ':' . $config->privateKey;
            $base64String = base64_encode($plainText);
            $this->apiKey = $base64String;
        }
    }
     /**
     * Retrieves face attributes from a given photo.
     *
     * @param array $photo Array representing the photo file.
     * @return DottaResponse The response object with face attributes.
     */

    public function getFaceAttributes($photo) : DottaResponse {
        $faceAttributesResponse = new DottaResponse();

        try {
            if ($photo === null) {
                return new DottaResponse(false, "Photo with a face is required");
            }

            $photoExtension ="." . strtolower(pathinfo($photo['name'], PATHINFO_EXTENSION));
            if (!in_array($photoExtension, $this->allowFileExtensions)) {
                return new DottaResponse(false, "File extension not allowed. Allowed extensions are " . implode(" ", $this->allowFileExtensions));
            }
            $photoMimeType = $this->getPhotoMimeType($photoExtension);
            if (empty($photoMimeType)) {
                return new DottaResponse(false, "Photo has an invalid mimetype");
            }

            $imageContent = fopen($photo['tmp_name'], 'r');
            $multipart = [
                [
                    'name'     => 'Photo',
                    'contents' => $imageContent,
                    'filename' => $photo['name'],
                    'headers'  => ['Content-Type' => $photoMimeType]
                ]
            ];

            $baseUrl = $this->environment == DottaEnvironment::PRODUCTION ? $this->baseUrlProduction : $this->baseUrlSandbox;
            $response = $this->httpClient->request('POST', $baseUrl . '/Face/Attributes', [
                'headers' => ['Authorization' => 'Basic ' . $this->apiKey],
                'multipart' => $multipart
            ]);

            $body = $response->getBody();

            $responseData = json_decode($body, true);

if ($response->getStatusCode() == 200){
    // Deserialize to HttpDottaFaceAttributesResponse
    $responseDTO = new HttpDottaFaceAttributesResponse();
    $responseDTO->status = $responseData['status'];
    $responseDTO->message = $responseData['message'];

    // Manually map data properties
    $responseDTO->data = new HttpDottaFaceAttributesResponseData();
    $responseDTO->data->age = $responseData['data']['age'] ?? null;
    $responseDTO->data->errorCode = $responseData['data']['errorCode'] ?? null;
    $responseDTO->data->errorMessage = $responseData['data']['errorMessage'] ?? null;
    $responseDTO->data->eyesOpen = $responseData['data']['eyesOpen'] ?? null;
    $responseDTO->data->female = $responseData['data']['female'] ?? null;
    $responseDTO->data->male = $responseData['data']['male'] ?? null;
    $responseDTO->data->orientation = $responseData['data']['orientation'] ?? null;
    $responseDTO->data->passiveLiveness = $responseData['data']['passiveLiveness'] ?? null;
    $responseDTO->data->smile = $responseData['data']['smile'] ?? null;

    // Set faceAttributesResponse
    $faceAttributesResponse = new DottaResponse();
    $faceAttributesResponse->status = $responseDTO->status;
    $faceAttributesResponse->message = $responseDTO->message;
    $faceAttributesResponse->data = $responseDTO->data;

} else {
    // Deserialize to HttpDottaResponse for error handling
    $errorResponse = new HttpDottaResponse();
    $errorResponse->status = $responseData['status'];
    $errorResponse->message = $responseData['message'];

    $faceAttributesResponse = new DottaResponse();
    $faceAttributesResponse->status = $errorResponse->status;
    $faceAttributesResponse->message = $errorResponse->message;
}

          if (is_resource($imageContent))
              fclose($imageContent);

            return $faceAttributesResponse;

        } catch (Exception $ex) {
            return new DottaResponse(false, $ex->getMessage());
        }
}

 /**
     * Performs face detection on a given photo.
     *
     * @param array $photo Array representing the photo file.
     * @return DottaResponse The response object with face detection data.
     */
public function faceDetection($photo) : DottaResponse {
    $faceDetectResponse = new DottaResponse();

    try {
        // Check if photo file is empty
        if ($photo === null) {
            return new DottaResponse(false, "Photo with a face is required");
        }

        // Check for allowed photo file extension
        $photoExtension ="." . strtolower(pathinfo($photo['name'], PATHINFO_EXTENSION));
        if (!in_array($photoExtension, $this->allowFileExtensions)) {
            return new DottaResponse(false, "File extension not allowed. Allowed extensions are " . implode(" ", $this->allowFileExtensions));
        }

        // Get photo mime type
        $photoMimeType = $this->getPhotoMimeType($photoExtension);
        if (empty($photoMimeType)) {
            return new DottaResponse(false, "Photo has an invalid mimetype");
        }

        // Build content for multipart HTTP request
        $imageContent = fopen($photo['tmp_name'], 'r');
        $multipart = [
            [
                'name'     => 'Photo',
                'contents' => $imageContent,
                'filename' => $photo['name'],
                'headers'  => ['Content-Type' => $photoMimeType]
            ]
        ];

        // Make network request and serialize response
        $baseUrl = $this->environment == DottaEnvironment::PRODUCTION ? $this->baseUrlProduction : $this->baseUrlSandbox;
        $response = $this->httpClient->request('POST', $baseUrl . '/Face/Detect', [
            'headers' => ['Authorization' => 'Basic ' . $this->apiKey],
            'multipart' => $multipart
        ]);

        $body = $response->getBody();
        $data = json_decode($body, true);

       if ($response->getStatusCode() == 200) {
            // Success - Deserialize and map to FaceDetectResponse
            $responseDTO = new HttpDottaFaceDetectResponse();
            $responseDTO->status = $data['status'];
            $responseDTO->message = $data['message'];

            $responseDTO->data = new HttpDottaFaceDetectResponseData();
            $responseDTO->data->angle = $data['data']['angle'] ?? null;
            $responseDTO->data->errorCode = $data['data']['errorCode'] ?? null;
            $responseDTO->data->errorMessage = $data['data']['errorMessage'] ?? null;
            $responseDTO->data->orientation = $data['data']['orientation'] ?? null;
            $responseDTO->data->padding = $data['data']['padding'] ?? null;
            $responseDTO->data->pointX = $data['data']['pointX'] ?? null;
            $responseDTO->data->pointY = $data['data']['pointY'] ?? null;
            $responseDTO->data->width = $data['data']['width'] ?? null;
            $faceDetectResponse->status = $responseDTO->status;
            $faceDetectResponse->message = $responseDTO->message;
            $faceDetectResponse->data = $responseDTO->data;
        } else {
            // Error - Deserialize and map to HttpDottaResponse
            $errorResponse = new HttpDottaResponse();
            $errorResponse->status = $data['status'];
            $errorResponse->message = $data['message'];
            $faceDetectResponse->status = $errorResponse->status;
            $faceDetectResponse->message = $errorResponse->message;
        }
        if (is_resource($imageContent))
             fclose($imageContent);

        return $faceDetectResponse;

    } catch (Exception $ex) {
        return new DottaResponse(false, $ex->getMessage());
    }
}



/**
     * Compares two photos and returns the similarity score.
     *
     * @param array $photoOne Array representing the first photo file.
     * @param array $photoTwo Array representing the second photo file.
     * @return DottaResponse Response object with the face match data.
     */
    public function faceMatch($photoOne, $photoTwo) {
        $faceMatchResponse = new DottaResponse();

        try {
            // Check if photo files are empty
            if ($photoOne === null || $photoTwo === null) {
                return new DottaResponse(false, "Reference photo and probe photo with a face is required");
            }

            // Check for allowed photo file extensions
            $photoOneExtension = "." . strtolower(pathinfo($photoOne['name'], PATHINFO_EXTENSION));
            $photoTwoExtension = "." . strtolower(pathinfo($photoTwo['name'], PATHINFO_EXTENSION));
            if (!in_array($photoOneExtension, $this->allowFileExtensions) || !in_array($photoTwoExtension, $this->allowFileExtensions)) {
                return new DottaResponse(false, "File extension not allowed. Allowed extensions are " . implode(" ", $this->allowFileExtensions));
            }

            // Get photos MIME types
            $photoOneMimeType = $this->getPhotoMimeType($photoOneExtension);
            $photoTwoMimeType = $this->getPhotoMimeType($photoTwoExtension);
            if (empty($photoOneMimeType) || empty($photoTwoMimeType)) {
                return new DottaResponse(false, "Photo has an invalid mimetype");
            }

            // Build content for multipart HTTP request
            $imageOneContent = fopen($photoOne['tmp_name'], 'r');
            $imageTwoContent = fopen($photoTwo['tmp_name'], 'r');
            $multipart = [
                [
                    'name'     => 'PhotoOne',
                    'contents' => $imageOneContent,
                    'filename' => $photoOne['name'],
                    'headers'  => ['Content-Type' => $photoOneMimeType]
                ],
                [
                    'name'     => 'PhotoTwo',
                    'contents' => $imageTwoContent,
                    'filename' => $photoTwo['name'],
                    'headers'  => ['Content-Type' => $photoTwoMimeType]
                ]
            ];

            // Make network request and serialize response
            $baseUrl = $this->environment == DottaEnvironment::PRODUCTION ? $this->baseUrlProduction : $this->baseUrlSandbox;
            $response = $this->httpClient->request('POST', $baseUrl . '/Face/Match', [
                'headers' => ['Authorization' => 'Basic ' . $this->apiKey],
                'multipart' => $multipart
            ]);

            $body = $response->getBody();
            $data = json_decode($body, true);

            if ($response->getStatusCode() == 200) {
                // Success - Deserialize and map to FaceMatchResponse
                $responseDTO = new HttpDottaFaceMatchResponse();
                $responseDTO->status = $data['status'];
                $responseDTO->message = $data['message'];

                $responseDTO->data = new HttpDottaFaceMatchResponseData();
                $responseDTO->data->errorCode = $data['data']['errorCode'] ?? null;
                $responseDTO->data->errorMessage = $data['data']['errorMessage'] ?? null;
                $responseDTO->data->similarityScore = $data['data']['similarityScore'] ?? null;

                $faceMatchResponse->status = $responseDTO->status;
                $faceMatchResponse->message = $responseDTO->message;
                $faceMatchResponse->data = $responseDTO->data;
            } else {
                // Error - Deserialize and map to HttpDottaResponse
                $errorResponse = new HttpDottaResponse();
                $errorResponse->status = $data['status'];
                $errorResponse->message = $data['message'];

                $faceMatchResponse->status = $errorResponse->status;
                $faceMatchResponse->message = $errorResponse->message;
            }
            if (is_resource($imageOneContent && $imageTwoContent)){
            fclose($imageOneContent);
            fclose($imageTwoContent);
            }
            return $faceMatchResponse;

        } catch (Exception $ex) {
            return new DottaResponse(false, $ex->getMessage());
        }
    }
    /**
     * Performs active liveness analysis on a collection of photos.
     *
     * @param array $photos Array of photo files.
     * @return DottaResponse Response object with the active liveness analysis data.
     */
    public function activeLivenessCheck($photos) {
        $faceActiveLivenessResponse = new DottaResponse();

        try {
            // Check if photo collection is empty
            if (empty($photos)) {
                return new DottaResponse(false, "The collection of photos cannot be empty");
            }

            $multipart = [];
            foreach ($photos as $photo) {
                // Check for allowed photo file extension
                $photoExtension ="." . strtolower(pathinfo($photo['name'], PATHINFO_EXTENSION));
                if (!in_array($photoExtension, $this->allowFileExtensions)) {
                    return new DottaResponse(false, "Invalid file extension detected. Allowed extensions are " . implode(" ", $this->allowFileExtensions));
                }

                // Get photo MIME type
                $photoMimeType = $this->getPhotoMimeType($photoExtension);
                if (empty($photoMimeType)) {
                    return new DottaResponse(false, "Invalid photo MIME type detected");
                }

                // Build content for multipart HTTP request
                $imageContent = fopen($photo['tmp_name'], 'r');
                $multipart[] = [
                    'name'     => 'Photos',
                    'contents' => $imageContent,
                    'filename' => $photo['name'],
                    'headers'  => ['Content-Type' => $photoMimeType]
                ];
            }

            // Make network request and serialize response
            $baseUrl = $this->environment == DottaEnvironment::PRODUCTION ? $this->baseUrlProduction : $this->baseUrlSandbox;
            $response = $this->httpClient->request('POST', $baseUrl . '/Face/ActiveLiveness', [
                'headers' => ['Authorization' => 'Basic ' . base64_encode($this->apiKey)],
                'multipart' => $multipart
            ]);

            $body = $response->getBody();
            $data = json_decode($body, true);

            if ($response->getStatusCode() == 200) {
                // Success - Deserialize and map to FaceActiveLivenessResponse
                $faceActiveLivenessResponse->status = $data['status'];
                $faceActiveLivenessResponse->message = $data['message'];
                $faceActiveLivenessResponse->data = new HttpDottaFaceActiveLivenessResponseData();
                $faceActiveLivenessResponse->data->livenessScore = $data['data']['livenessScore'] ?? null;
                // ... Map other properties of data ...
            } else {
                // Error handling
                $faceActiveLivenessResponse->status = false;
                $faceActiveLivenessResponse->message = $data['message'] ?? 'An error occurred.';
            }

            // Close file resources
            foreach ($multipart as $part) {
                if (isset($part['contents']) && is_resource($part['contents'])) {
                    fclose($part['contents']);
                }
            }

            return $faceActiveLivenessResponse;

        } catch (Exception $ex) {
            return new DottaResponse(false, $ex->getMessage());
        }
    }


/**
     * Determines the MIME type based on the photo file extension.
     * 
     * @param string $photoExtension The file extension of the photo.
     * @return string|null The MIME type or null if not recognized.
     */

     private static function getPhotoMimeType($photoExtension) : string | null {
        $photoExtension = strtolower($photoExtension);
    
        if ($photoExtension == ".jpg" || $photoExtension == ".jpeg") {
            return "image/jpeg";
        } else if ($photoExtension == ".png") {
            return "image/png";
        } else {
            return null;
        }
    }


}





