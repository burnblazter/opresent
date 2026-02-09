<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

use App\Models\FaceDescriptorsRequestModel;

abstract class BaseController extends Controller
{

    protected $request;

    protected $helpers = ['auth']; 

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {

        parent::initController($request, $response, $logger);

        $totalPendingFaces = 0;

        if (function_exists('logged_in') && logged_in()) {
            if (in_groups('admin') || in_groups('head')) {

                $requestModel = new FaceDescriptorsRequestModel();

                $totalPendingFaces = $requestModel->where('status', 'pending')->countAllResults();
            }
        }

        session()->set('totalPendingFaces', $totalPendingFaces); 

        \Config\Services::renderer()->setData(['totalPendingFaces' => $totalPendingFaces]); 

    }
}