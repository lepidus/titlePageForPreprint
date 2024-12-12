<?php

namespace APP\plugins\generic\titlePageForPreprint\classes\clients;

use APP\core\Application;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Pool;
use GuzzleHttp\Exception\ClientException;

class CrossrefClient
{
    private $guzzleClient;

    public const CROSSREF_API_URL = 'https://api.crossref.org/works';

    public function __construct($guzzleClient = null)
    {
        if (!is_null($guzzleClient)) {
            $this->guzzleClient = $guzzleClient;
        } else {
            $this->guzzleClient = Application::get()->getHttpClient();
        }
    }

    public function getSubmissionWorkType($submission)
    {
        $originalDocumentDoi = $submission->getCurrentPublication()->getData('originalDocumentDoi');

        if (empty($originalDocumentDoi)) {
            return null;
        }

        $requestUrl = htmlspecialchars(self::CROSSREF_API_URL . "?filter=doi:$originalDocumentDoi");
        try {
            $response = $this->guzzleClient->request('GET', $requestUrl, [
                'headers' => [
                    'Accept' => 'application/json'
                ],
            ]);

            $responseJson = json_decode($response->getBody(), true);
            $items = $responseJson['message']['items'];

            if (empty($items)) {
                return null;
            }

            return $items[0]['type'];
        } catch (ClientException $e) {
            $errorMsg = $e->getResponse()->getBody()->getContents();
            error_log("Error while getting DOI work type: $errorMsg");
        }

        return null;
    }
}
