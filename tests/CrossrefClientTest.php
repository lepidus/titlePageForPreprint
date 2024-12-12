<?php

namespace APP\plugins\generic\titlePageForPreprint\tests;

use PKP\tests\PKPTestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use APP\submission\Submission;
use APP\publication\Publication;
use APP\plugins\reports\titlePageForPreprint\classes\clients\CrossrefClient;

class CrossrefClientTest extends PKPTestCase
{
    private $mockGuzzleClient;
    private $crossrefClient;
    private $mapDoiToWorkType = [
        '10.666/949494' => 'journal-article',
        '10.987/131415' => 'book-chapter'
    ];
    private $submissions;

    public function setUp(): void
    {
        $this->submissions = $this->createTestSubmissions();
        $this->mockGuzzleClient = $this->createMockGuzzleClient();
        $this->crossrefClient = new CrossrefClient($this->mockGuzzleClient);
    }

    private function createMockGuzzleClient()
    {
        $mockResponses = [];

        foreach ($this->mapDoiToWorkType as $doi => $workType) {
            $responseBody = [
                'status' => 'ok',
                'message' => [
                    'total-results' => 1,
                    'items' => [
                        [
                            'DOI' => $doi,
                            'type' => $workType
                        ]
                    ]
                ]
            ];
            $mockResponses[] = new Response(200, [], json_encode($responseBody));
        }

        $mockHandler = new MockHandler($mockResponses);
        $guzzleClient = new Client(['handler' => $mockHandler]);

        return $guzzleClient;
    }

    private function createTestSubmissions(): array
    {
        $submissions = [];
        $submissionId = 100;

        foreach ($this->mapDoiToWorkType as $doi => $workType) {
            $submissions[] = $this->createSubmission($submissionId++, $doi);
        }

        return $submissions;
    }

    private function createSubmission(int $submissionId, string $doi): Submission
    {
        $publication = new Publication();
        $publication->setData('id', $submissionId + 3);
        $publication->setData('originalDocumentDoi', $doi);

        $submission = new Submission();
        $submission->setData('id', $submissionId);
        $submission->setData('currentPublicationId', $publication->getId());
        $submission->setData('publications', [$publication]);

        return $submission;
    }

    public function testGetSubmissionWorkType()
    {
        foreach ($this->submissions as $submission) {
            $submissionWorkType = $this->crossrefClient->getSubmissionWorkType($submission);
            $originalDocumentDoi = $submission->getCurrentPublication()->getData('originalDocumentDoi');

            $this->assertEquals($this->mapDoiToWorkType[$originalDocumentDoi], $submissionWorkType);
        }
    }
}
