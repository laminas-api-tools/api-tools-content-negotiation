<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-content-negotiation for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-content-negotiation/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-content-negotiation/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\ApiTools\ContentNegotiation\Filter;

use Laminas\Filter\Exception\RuntimeException as FilterRuntimeException;
use Laminas\Filter\File\RenameUpload as BaseFilter;
use Laminas\Stdlib\ErrorHandler;
use Laminas\Stdlib\RequestInterface;

class RenameUpload extends BaseFilter
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @param RequestInterface $request
     */
    public function setRequest(RequestInterface $request)
    {
        $this->request = $request;
    }

    /**
     * Override moveUploadedFile
     *
     * If the request is not HTTP, or not a PUT or PATCH request, delegates to
     * the parent functionality.
     *
     * Otherwise, does a `rename()` operation, and returns the status of the
     * operation.
     *
     * @param string $sourceFile
     * @param string $targetFile
     * @return bool
     * @throws FilterRuntimeException in the event of a warning
     */
    protected function moveUploadedFile($sourceFile, $targetFile)
    {
        if (null === $this->request
            || ! method_exists($this->request, 'isPut')
            || (! $this->request->isPut() && ! $this->request->isPatch())
        ) {
            return parent::moveUploadedFile($sourceFile, $targetFile);
        }

        ErrorHandler::start();
        $result = rename($sourceFile, $targetFile);
        $warningException = ErrorHandler::stop();

        if (false === $result || null !== $warningException) {
            throw new FilterRuntimeException(
                sprintf('File "%s" could not be renamed. An error occurred while processing the file.', $sourceFile),
                0,
                $warningException
            );
        }
        return $result;
    }
}
