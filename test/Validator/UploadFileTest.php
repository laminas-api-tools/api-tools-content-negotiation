<?php

declare(strict_types=1);

namespace LaminasTest\ApiTools\ContentNegotiation\Validator;

use Laminas\ApiTools\ContentNegotiation\Validator\UploadFile;
use Laminas\Http\Request as HttpRequest;
use PHPUnit\Framework\TestCase;

use function basename;
use function filesize;
use function realpath;
use function var_export;

use const UPLOAD_ERR_OK;

class UploadFileTest extends TestCase
{
    /** @var UploadFile */
    protected $validator;

    protected function setUp(): void
    {
        $this->validator = new UploadFile();
    }

    /** @psalm-return array<string, array{0: string}> */
    public function uploadMethods(): array
    {
        return [
            'put'   => ['PUT'],
            'patch' => ['PATCH'],
        ];
    }

    /**
     * @dataProvider uploadMethods
     */
    public function testDoesNotMarkUploadFileAsInvalidForPutAndPatchHttpRequests(string $method): void
    {
        $request = new HttpRequest();
        $request->setMethod($method);
        $this->validator->setRequest($request);

        $file = [
            'name'     => basename(__FILE__),
            'tmp_name' => realpath(__FILE__),
            'size'     => filesize(__FILE__),
            'type'     => 'application/x-php',
            'error'    => UPLOAD_ERR_OK,
        ];

        $this->assertTrue($this->validator->isValid($file), var_export($this->validator->getMessages(), true));
    }
}
