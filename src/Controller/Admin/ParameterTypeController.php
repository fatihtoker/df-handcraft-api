<?php

namespace App\Controller\Admin;

use App\Controller\Api\ApiController;
use App\Response\ApiResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Service\ParameterTypesService;

/**
 * @Route("/parameter-types")
 * @IsGranted("ROLE_ADMIN")
 */
class ParameterTypeController extends ApiController
{
    /**
     * @Route("", name="parameter-types-index", methods={"GET"})
     */
    public function getParameterTypesAction(Request $request, ParameterTypesService $service)
    {
        $data = $service->getAll($request);
        return $this->createJsonResponse(ApiResponse::createSuccessResponse($data));
    }

    /**
     * @Route("/create", name="add-parameter-type", methods={"POST"})
     */
    public function createParameterTypeAction(Request $request, ParameterTypesService $service)
    {
        $response = $service->create($request);
        return $this->createJsonResponse($response);
    }

    /**
     * @Route("/delete/{id}", name="delete-parameter-type", methods={"POST"})
     */
    public function deleteParameterTypeAction(int $id, ParameterTypesService $service)
    {
        $response = $service->delete($id);

        return $this->createJsonResponse($response);
    }
}
