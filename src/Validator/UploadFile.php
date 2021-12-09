<?php

declare(strict_types=1);

namespace Laminas\ApiTools\ContentNegotiation\Validator;

use Laminas\Stdlib\RequestInterface;
use Laminas\Validator\File\UploadFile as BaseValidator;

use function count;
use function method_exists;

class UploadFile extends BaseValidator
{
    /** @var null|RequestInterface */
    protected $request;

    /** @return void */
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
     * @return bool
     */
    public function isValid($value)
    {
        if (
            null === $this->request
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
