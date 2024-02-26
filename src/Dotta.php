<?php 

namespace Dotta;
use Exception;
use Dotta\Models\DottaResponse;
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