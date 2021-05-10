<?php

namespace LaminasTest\ApiTools\ContentNegotiation\TestAsset;

use Laminas\Mvc\Controller\AbstractActionController;

class ContentTypeController extends AbstractActionController
{
    public function setRequest($request)
    {
        $this->request = $request;
    }
}
