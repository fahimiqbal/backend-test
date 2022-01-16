<?php
namespace App\Tests;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Application;

class ApplicationTest extends TestCase
{
    /**
     * Builds an application instance for all tests.
     *
     * Overwrite it if you need to send any more arguments to the Application's constructor.
     * 
     * @return Application
     */
    protected function provideApplication()
    {
        return new Application([
            'ftp' => [
                'hostname' => 'uploads.ipedis.com',
                'username' => 'tester',
                'password' => 'convertor',
                'destination' => 'pdf'
            ],
            's3' => [
                'access_key_id' => 'accessKeyId123',
                'secret_access_key' => 'secretTokenOfTeh31337',
                'bucketname' => 'ipedis-uploads'
            ],
            'dropbox' => [
                'access_key' => 'fsghfdigsdfgs',
                'secret_token' => 'sgfdgsu43rg',
                'container' => 'pdf-uploads'
            ],
            'pdf-convertor.com' => [
                'app_id' => 'ipedis-fgdfgg87gf7d',
                'access_token' => '234556fghdfgsdfsehery234'
            ]
        ]);
    }

    /************************************
     * WRITE YOUR OWN TESTS HERE
     ************************************/
    public function testReturns400ForInvalidFile()
    {
        $app = $this->provideApplication();

        $request = Request::create('/', 'POST', [
            'upload' => 'dropbox',
            'formats' => []
        ], [], [
            'file' => $this->provideUploadedFile('code.jpg')
        ]);

        $response = $app->handleRequest($request);
        $this->assertInstanceOf(Response::class, $response, 'Application should always return Response object');

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode(), 'Requests with unsupported should return HTTP Code 400 in the response.');
    }


    public function testConvertToPngUploadOnFtp()
    {
        $app = $this->provideApplication();

        $request = Request::create('/', 'POST', [
            'upload' => 'ftp',
            'formats' => ['png']
        ], [], [
            'file' => $this->provideUploadedFile('resume.pdf')
        ]);

        $response = $app->handleRequest($request);

        $data = $this->validateResponse($response, true);

        $this->assertEquals('ftp://uploads.ipedis.com/pdf/resume_pdf.png', $data['formats']['png']); // possible test bug fix for convertFile() method 
    }
    /************************************
     * DO NOT CHANGE ANYTHING BELOW
     ************************************/

    public function testReturningResponse()
    {
        $app = $this->provideApplication();

        $request = $this->createMock(Request::class);
        $response = $app->handleRequest($request);

        $this->assertInstanceOf(Response::class, $response, 'Application should always return Response object');
    }

    public function testUploadingToFTP()
    {
        $app = $this->provideApplication();

        $request = Request::create('/', 'POST', [
            'upload' => 'ftp',
            'formats' => []
        ], [], [
            'file' => $this->provideUploadedFile('resume.pdf')
        ]);

        $response = $app->handleRequest($request);

        $data = $this->validateResponse($response);

        $this->assertEquals('ftp://uploads.ipedis.com/pdf/resume.pdf', $data['url']);
    }

    public function testUploadingToS3()
    {
        $app = $this->provideApplication();

        $request = Request::create('/', 'POST', [
            'upload' => 's3',
            'formats' => []
        ], [], [
            'file' => $this->provideUploadedFile('resume.pdf')
        ]);

        $response = $app->handleRequest($request);

        $data = $this->validateResponse($response);

        $this->assertEquals('http://ipedis-uploads.s3.amazonaws.com/resume.pdf', $data['url']);
    }

    public function testUploadingToDropbox()
    {
        $app = $this->provideApplication();

        $request = Request::create('/', 'POST', [
            'upload' => 'dropbox',
            'formats' => []
        ], [], [
            'file' => $this->provideUploadedFile('resume.pdf')
        ]);

        $response = $app->handleRequest($request);

        $data = $this->validateResponse($response);

        $this->assertEquals('http://ipedis.dropbox.com/pdf-uploads/resume.pdf', $data['url']);
    }

    public function testConvertToWebp()
    {
        $app = $this->provideApplication();

        $request = Request::create('/', 'POST', [
            'upload' => 's3',
            'formats' => ['webp']
        ], [], [
            'file' => $this->provideUploadedFile('resume.pdf')
        ]);

        $response = $app->handleRequest($request);

        $data = $this->validateResponse($response, true);

        $this->assertEquals('http://ipedis-uploads.s3.amazonaws.com/resume_pdf.webp', $data['formats']['webp']); // possible test bug fix for convertFile() method 
    }

    public function testConvertToJpegAndUploadingToS3()
    {
        $app = $this->provideApplication();

        $request = Request::create('/', 'POST', [
            'upload' => 's3',
            'formats' => ['jpg']
        ], [], [
            'file' => $this->provideUploadedFile('resume.pdf')
        ]);

        $response = $app->handleRequest($request);

        $data = $this->validateResponse($response, true);
        $this->assertEquals('http://ipedis-uploads.s3.amazonaws.com/resume_pdf.jpg', $data['formats']['jpg']); // possible test bug fix for convertFile() method 
    }

    public function testUploadingToDropboxAndConvertToWebpAndJpeg()
    {
        $app = $this->provideApplication();

        $request = Request::create('/', 'POST', [
            'upload' => 'dropbox',
            'formats' => ['webp', 'jpg']
        ], [], [
            'file' => $this->provideUploadedFile('resume.pdf')
        ]);

        $response = $app->handleRequest($request);

        $data = $this->validateResponse($response, true);

        $this->assertStringContainsString('http://ipedis.dropbox.com/pdf-uploads/resume.pdf', $data['url']); // Using assertContains() with string haystacks is deprecated and will not be supported in PHPUnit 9

        $this->assertEquals('http://ipedis.dropbox.com/pdf-uploads/resume_pdf.webp', $data['formats']['webp']); // possible test bug fix for convertFile() method 
        $this->assertEquals('http://ipedis.dropbox.com/pdf-uploads/resume_pdf.jpg', $data['formats']['jpg']); // possible test bug fix for convertFile() method 
    }
  
    public function testReturns400ForRequestWithoutFile()
    {
        $app = $this->provideApplication();

        $request = Request::create('/', 'POST', [
            'upload' => 'avengers',
            'formats' => ['webp', 'png']
        ], [], []);

        $response = $app->handleRequest($request);

        $this->assertInstanceOf(Response::class, $response, 'Application should always return Response object');

        $this->assertEquals(400, $response->getStatusCode(), 'Requests without uploaded file should return HTTP Code 400 in the response.');
    }

    public function testReturns400ForRequestWithoutParameters()
    {
        $app = $this->provideApplication();

        $request = Request::create('/', 'POST', [], [], [
            'file' => $this->provideUploadedFile('resume.pdf')
        ]);

        $response = $app->handleRequest($request);

        $this->assertInstanceOf(Response::class, $response, 'Application should always return Response object');

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode(), 'Requests without parameters should return HTTP Code 400 in the response.');
    }

    public function testReturns400ForRequestToUnknownService()
    {
        $app = $this->provideApplication();

        $request = Request::create('/', 'POST', [
            'upload' => 'unknown',
            'formats' => ['webp', 'jpg']
        ], [], [
            'file' => $this->provideUploadedFile('resume.pdf')
        ]);

        $response = $app->handleRequest($request);

        $this->assertInstanceOf(Response::class, $response, 'Application should always return Response object');

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode(), 'GET requests should return HTTP Code 405 in the response.');
    }

    public function testReturns400ForRequestWithUnsupportedFormat()
    {
        $app = $this->provideApplication();

        $request = Request::create('/', 'POST', [
            'upload' => 'dropbox',
            'formats' => ['gif']
        ], [], [
            'file' => $this->provideUploadedFile('resume.pdf')
        ]);

        $response = $app->handleRequest($request);

        $this->assertInstanceOf(Response::class, $response, 'Application should always return Response object');

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode(), 'Requests with unsupported should return HTTP Code 400 in the response.');
    }

    public function testReturns405ForGETRequest()
    {
        $app = $this->provideApplication();

        $request = Request::create('/', 'GET', [
            'upload' => 'ftp',
            'formats' => ['webp', 'png']
        ], [], [
            'file' => $this->provideUploadedFile('resume.pdf')
        ]);

        $response = $app->handleRequest($request);

        $this->assertInstanceOf(Response::class, $response, 'Application should always return Response object');

        $this->assertEquals(Response::HTTP_METHOD_NOT_ALLOWED, $response->getStatusCode(), 'GET requests should return HTTP Code 405 in the response.');
    }

    /**
     * @param Response $response
     * @param bool $hasFormats
     *
     * @return mixed
     */
    protected function validateResponse($response, bool $hasFormats = false): array
    {
        $this->assertInstanceOf(Response::class, $response, 'Application should always return Response object');

        $this->assertEquals(200, $response->getStatusCode(), 'Valid responses should return HTTP Code 200');
        $this->assertEquals('UTF-8', $response->getCharset(), 'The response should always have UTF-8 charset set');
        $this->assertStringStartsWith('application/json', $response->headers->get('Content-Type'), 'The response should have a Content-Type header set to "application/json"');

        $data = json_decode($response->getContent(), true);

        $this->assertIsArray($data, 'The response should contain a valid JSON string');
        $this->assertIsArray($data, 'The response should contain a valid JSON string');

        $this->assertArrayHasKey('url', $data, 'The response should contain "url" key with a URL string to where the original file was uploaded');
        $this->assertIsString($data['url'], 'The response should contain "url" key with a URL string to where the original file was uploaded');

        if ($hasFormats) {
            $this->assertArrayHasKey('formats', $data, 'If there were encoding formats requested then the response should contain a "formats" key.');
            $this->assertIsArray($data['formats'], 'If there were converting formats requested then the response should contain an array under "formats" key.');
            $this->assertNotEmpty($data['formats'], 'If there were converting formats requested then the response should contain a non-empty array under "formats" key.');
        }

        return $data;
    }

    /**
     * @param string $originalName
     * @return MockObject
     */
    protected function provideUploadedFile(string $originalName = 'resume.pdf'): MockObject
    {
        return $this->getMockBuilder(UploadedFile::class)
            ->setConstructorArgs([__DIR__ . '/fixtures/resume.pdf', $originalName, null, null, true])
            ->setConstructorArgs([__DIR__ . '/fixtures/'.$originalName, $originalName, null, null, true])
            ->addMethods([])
            ->getMock()
        ;
    }
}
