<?php

namespace App\Controller\Admin;

use App\Controller\Api\ApiController;
use App\Response\ApiResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Service\ParametersService;

/**
 * @Route("/parameters")
 * @IsGranted("ROLE_ADMIN")
 */
class ParameterController extends ApiController
{
    /**
     * @Route("", name="parameters-index", methods={"GET"})
     */
    public function getParameterTypesAction(Request $request, ParametersService $service)
    {
        $data = $service->getAll($request);
        return $this->createJsonResponse(ApiResponse::createSuccessResponse($data));
    }

    /**
     * @Route("/create", name="add-parameter", methods={"POST"})
     */
    public function createParameterAction(Request $request, ParametersService $service)
    {
        $response = $service->create($request);
        return $this->createJsonResponse($response);
    }

    /**
     * @Route("/delete/{id}", name="delete-parameter", methods={"POST"})
     */
    public function deleteParameterAction(int $id, ParametersService $service)
    {
        $response = $service->delete($id);

        return $this->createJsonResponse($response);
    }
}
