<?php 

namespace Dotta;
use Exception;
use Dotta\Models\DottaResponse;
use Dotta\Models\HttpDottaFaceAttributesResponse;
use Dotta\Models\HttpDottaFaceAttributesResponseData;
use Dotta\Models\HttpDottaFaceDetectResponse;
use Dotta\Models\HttpDottaFaceDetectResponseData;
use Dotta\Models\HttpDottaResponse;
use Dotta\Enums\DottaEnvironment;

class Dotta {
    public $apiKey;
    public $publicKey;
    public $privateKey;
    public $environment;
    public $baseUrlProduction;
    public $baseUrlSandbox;
    private $allowFileExtensions = ['.png', '.jpeg', '.jpg'];
    private $httpClient;

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

    public function getFaceAttributes($photo) {
        $faceAttributesResponse = new DottaResponse();

        try {
            if ($photo === null) {
                return new DottaResponse(false, "Photo with a face is required");
            }

            $photoExtension = strtolower(pathinfo($photo['name'], PATHINFO_EXTENSION));
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
    $responseDTO = new Models\HttpDottaFaceAttributesResponse();
    $responseDTO->status = $responseData['status'];
    $responseDTO->message = $responseData['message'];

    // Manually map data properties
    $responseDTO->data = new Models\HttpDottaFaceAttributesResponseData();
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
    $errorResponse = new Models\HttpDottaResponse();
    $errorResponse->status = $responseData['status'];
    $errorResponse->message = $responseData['message'];

    $faceAttributesResponse = new DottaResponse();
    $faceAttributesResponse->status = $errorResponse->status;
    $faceAttributesResponse->message = $errorResponse->message;
}

            fclose($imageContent);

            return $faceAttributesResponse;

        } catch (Exception $ex) {
            return new DottaResponse(false, $ex->getMessage());
        }
}

public function faceDetection($photo) {
    $faceDetectResponse = new DottaResponse();

    try {
        // Check if photo file is empty
        if ($photo === null) {
            return new DottaResponse(false, "Photo with a face is required");
        }

        // Check for allowed photo file extension
        $photoExtension = strtolower(pathinfo($photo['name'], PATHINFO_EXTENSION));
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
            'headers' => ['Authorization' => 'Basic ' . base64_encode($this->apiKey)],
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

        fclose($imageContent);

        return $faceDetectResponse;

    } catch (Exception $ex) {
        return new DottaResponse(false, $ex->getMessage());
    }
}

private function getPhotoMimeType($photoExtension) {
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