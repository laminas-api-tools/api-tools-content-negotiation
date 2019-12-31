<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-content-negotiation for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-content-negotiation/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-content-negotiation/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\ApiTools\ContentNegotiation\Validator;

use Laminas\Stdlib\RequestInterface;
use Laminas\Validator\File\UploadFile as BaseValidator;

class UploadFile extends BaseValidator
{
    /**
     * @var null|RequestInterface
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
     * Overrides isValid()
     *
     * If the reason for failure is self::ATTACK, we can assume that
     * is_uploaded_file() has failed -- which is
     *
     * @param mixed $value
     * @return void
     */
    public function isValid($value)
    {
        if (null === $this->request
            || ! method_exists($this->request, 'isPut')
            || (! $this->request->isPut()
                && ! $this->request->isPatch())
        ) {
            // In absence of a request object, an HTTP request, or a PATCH/PUT
            // operation, just use the parent logic.
            return parent::isValid($value);
        }

        $result = parent::isValid($value);
        if ($result !== false) {
            return $result;
        }

        if (! isset($this->abstractOptions['messages'][static::ATTACK])) {
            return $result;
        }

        if (count($this->abstractOptions['messages']) > 1) {
            return $result;
        }

        unset($this->abstractOptions['messages'][static::ATTACK]);
        return true;
    }
}
