## Introduction

`dotta-php` is a lightweight and intuitive package designed to streamline the integration process of [dotta API][dottaapidoc] and empower businesses to harness the power of [dotta biometric service][dottawebsite] effortlessly.

[dotta][dottawebsite] offers a wealth of functionality for performing real-time identity verification in the most convenient and efficient approach, but getting started and putting all the codes together can sometimes be complex and time-consuming. With `dotta-php`, we've simplified the integration process, allowing you to focus on building amazing applications without getting bogged down in implementation details.

## Getting Started

1. Install the `dotta-php` package from [Packagist][packagistlink].

```
composer require shepherrrd/dotta-php
```

2.  Setup `dotta-php` with the configuration

    ```
    //setup dotta config
    public $apiKey = env('dotta-apikey);
    public $publicKey = env('dotta-publickey);
    public $privateKey = env('dotta-privatekey);
    public $environment = env('dotta-environment) // DottaEnvironment::PRODUCTION;
    public $baseUrlProduction = env('dotta-produrl);
    public $baseUrlSandbox env('dotta-sandboxurl');
    public $httpClient = new client() //guzzlehttpclient;

    $config = new Dotta\Model\Config(
            $apikey,
            $publicKey,
            $privateKey,
            $environment,
            $baseUrlProduction,
            $baseUrlSandbox,
            $httpClient

    );
    ```

//Initialize the dotta class with the config
$dotta = new Dotta\Dotta($config);

3. You can now access Any member of the Dotta Class

```
$photo = $request->files('photo) ?? "images/usedotta.jpg";
$faceAttribute = $dotta->getFaceAttributes($photo);
```

**Dotta Configurations Options**
| **Option** | **Description** |
| ---------- | --------------- |
| ApiKey | Base64 encode string of your dotta public and private API keys concatenated in this format PUBLICKEY:PRIVATEKEY |
| PublicKey | Your dotta public API key |
| PrivateKey | Your dotta private API key |
| Environment | Enum to specify which dotta environment you want to use |
| BaseUrlProduction | API base url for dotta's production environment. |
| BaseUrlSandbox | API base url for dotta's sandbox or test environment. |

Pass the your public and private key if you don't know how to get a base64 string encoding of your keys. Otherwise, just pass the ApiKey. When you pass the ApiKey, you won't need to pass the public and private keys.

[dottawebsite]: https://withdotta.com
[dottaapidoc]: https://docs.withdotta.com
[packagistlink]: https::packagist.com/shepherrrd/dotta-php
