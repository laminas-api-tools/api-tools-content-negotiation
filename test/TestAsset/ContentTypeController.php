<?php

namespace LaminasTest\ApiTools\ContentNegotiation\TestAsset;

use Laminas\Http\Request;
use Laminas\Mvc\Controller\AbstractActionController;

class ContentTypeController extends AbstractActionController
{
    public function setRequest(Request $request): void
    {
        $this->request = $request;
    }
}
