<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-content-negotiation for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-content-negotiation/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-content-negotiation/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\ApiTools\ContentNegotiation\Validator;

use Laminas\ApiTools\ContentNegotiation\Validator\UploadFile;
use Laminas\Http\Request as HttpRequest;
use PHPUnit\Framework\TestCase;

class UploadFileTest extends TestCase
{
    protected function setUp()
    {
        $this->validator = new UploadFile();
    }

    public function uploadMethods()
    {
        return [
            'put'   => ['PUT'],
            'patch' => ['PATCH'],
        ];
    }

    /**
     * @dataProvider uploadMethods
     */
    public function testDoesNotMarkUploadFileAsInvalidForPutAndPatchHttpRequests($method)
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

        $this->assertTrue($this->validator->isValid($file), var_export($this->validator->getMessages(), 1));
    }
}
