<?php 
namespace DottaDemo;
require_once  __DIR__. "/vendor/autoload.php";
use Dotta\Enums\DottaEnvironment as DottaEnvironment;
use Dotta\Dotta as Dotta;
use GuzzleHttp\Client;
use Dotta\DottaConfig as DottaConfig;

class DottaDemo {
   public $apiKey;
    public $publicKey;
    public $privateKey;
    public $environment;
    public $baseUrlProduction;
    public $baseUrlSandbox;
    public $httpClient;
    public $config;
    public $dotta;
    private $image = __DIR__ . "/Images/female.jpeg";
    private $image2 = __DIR__ . "/Images/male.jpg";

    public function __construct()
    {
        $this->apiKey = getenv('DOTTA_API_KEY');
        $this->publicKey = getenv('DOTTA_PUBLIC_KEY');
        $this->privateKey = getenv('DOTTA_PRIVATE_KEY');
        $this->environment = DottaEnvironment::SANDBOX;
        $this->baseUrlProduction = 'https://apps.securedrecords.com/dotta-biometrics/api';
        $this->baseUrlSandbox = 'https://apps.securedrecords.com/DevDottaBiometrics/api';
        $this->httpClient = new Client();
        $this->config = new DottaConfig(getenv('DOTTA_API_KEY'), $this->publicKey,$this->privateKey, $this->environment, $this->baseUrlProduction, $this->baseUrlSandbox, $this->httpClient);
         $this->dotta = new Dotta($this->config);
    }
    private function getImageContent($path) {
        return file_get_contents($path);
    }
    private function getImageMimeType($path) {
        return mime_content_type($path);
    }
    private function createTemporaryFileFromExisting($existingFilePath) {
        // Read the content of the existing file
        $fileContent = file_get_contents($existingFilePath);
    
        // Create a temporary file and get its name
        $tempFilePath = tempnam(sys_get_temp_dir(), 'upload_');
    
        // Write the content into the temporary file
        file_put_contents($tempFilePath, $fileContent);
    
        // Return the path of the temporary file
        return $tempFilePath;
    }
    
    
    public function testFaceDetect()
    {
        //print_r ($this->config);
        $content = $this->getImageContent($this->image);
        $mimeType = $this->getImageMimeType($this->image);
        $tempFilePath = $this->createTemporaryFileFromExisting($this->image);
        $fakeFileArray = [
            'name' => basename($this->image), // get the filename
            'type' => $mimeType,
            'tmp_name' => $tempFilePath, // path to the image
            'size' => filesize($tempFilePath) // get the file size
        ];
        echo (json_encode($fakeFileArray) . "\n");
        $response = $this->dotta->faceDetection($fakeFileArray);
        return $response;
    }

    public function testFaceAttributes()
    {
        $content = $this->getImageContent($this->image);
        $mimeType = $this->getImageMimeType($this->image);
        $mimeType = $this->getImageMimeType($this->image);
        $tempFilePath = $this->createTemporaryFileFromExisting($this->image);
        $fakeFileArray = [
            'name' => basename($this->image), // get the filename
            'type' => $mimeType,
            'tmp_name' => $tempFilePath, // path to the image
            'size' => filesize($tempFilePath) // get the file size
        ];
        $response = $this->dotta->getFaceAttributes($fakeFileArray);
        return $response;
    }

    public function testFaceMatch()
    {
        $content1 = $this->getImageContent($this->image);
        $content2 = $this->getImageContent($this->image2);
        $mimeType = $this->getImageMimeType($this->image);
        $mimeType2 = $this->getImageMimeType($this->image2);
        $mimeType = $this->getImageMimeType($this->image);
        $tempFilePath = $this->createTemporaryFileFromExisting($this->image);
        $tempFilePath2 = $this->createTemporaryFileFromExisting($this->image2);
        $fakeFileArray = [
            'name' => basename($this->image), // get the filename
            'type' => $mimeType,
            'tmp_name' => $tempFilePath, // path to the image
            'size' => filesize($tempFilePath) // get the file size
        ];
        $fakeFileArray2 = [
            'name' => basename($this->image2), // get the filename
            'type' => $mimeType2,
            'tmp_name' => $tempFilePath2, // path to the image
            'size' => filesize($tempFilePath2) // get the file size
        ];
        $response = $this->dotta->faceMatch($fakeFileArray, $fakeFileArray2);
        return $response;
    }

    public function testFaceActiveLiveness()
    {
        $content1 = $this->getImageContent($this->image);
        $content2 = $this->getImageContent($this->image2);        
        $mimeType = $this->getImageMimeType($this->image);
        $mimeType2 = $this->getImageMimeType($this->image2);
        $mimeType = $this->getImageMimeType($this->image);
        $tempFilePath = $this->createTemporaryFileFromExisting($this->image);
        $tempFilePath2 = $this->createTemporaryFileFromExisting($this->image2);
        $fakeFileArray = [
            'name' => basename($this->image), // get the filename
            'type' => $mimeType,
            'tmp_name' => $tempFilePath, // path to the image
            'size' => filesize($tempFilePath) // get the file size
        ];
        $fakeFileArray2 = [
            'name' => basename($this->image2), // get the filename
            'type' => $mimeType2,
            'tmp_name' => $tempFilePath2, // path to the image
            'size' => filesize($tempFilePath2) // get the file size
        ];
        $response = $this->dotta->activeLivenessCheck([$fakeFileArray, $fakeFileArray2]);
        return $response;
    }

 


}


$dottaDemo = new DottaDemo();
echo "Face Detect: " . json_encode($dottaDemo->testFaceDetect()) . "\n";
echo "Face Attributes: " . json_encode($dottaDemo->testFaceAttributes()) . "\n";
echo "Face Match: " . json_encode($dottaDemo->testFaceMatch()) . "\n";
echo "Face Active Liveness: " . json_encode($dottaDemo->testFaceActiveLiveness()) . "\n";







