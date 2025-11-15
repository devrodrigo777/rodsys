<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Services\ModuleService;
use Psr\Log\LoggerInterface; // Para compatibilidade do initController

use CodeIgniter\Config\Services; 
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class ThemedController extends Controller
{
    protected ModuleService $moduleService;

    // Apenas a interface PSR-3 é usada aqui
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        // $this->moduleService = service('moduleService'); 
        

    }
}