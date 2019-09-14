<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Parameter;
use App\Response\ApiResponse;
use App\Entity\ParameterType;

class ParametersService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getAll(Request $request)
    {
        $parameterRepo = $this->em->getRepository(Parameter::class);

        $data = $parameterRepo->findAll();
        
        return $data;
    }

    public function create(Request $request)
    {
        $parameterRepo = $this->em->getRepository(Parameter::class);
        $parameterTypeRepo = $this->em->getRepository(ParameterType::class);

        $id = $request->get('id');
        $name = $request->get('name');
        $displayName = $request->get('displayName');
        $parameterTypeId = $request->get('parameterType');

        if (!($name && $displayName && $parameterTypeId)) {
            return ApiResponse::createErrorResponse(422, 'Zorunlu alanlar boş bırakılamaz', []);
        }

        if ($id) {
            $parameter = $parameterRepo->find($id);
        } else {
            $parameter = new Parameter();
        }

        $parameterType = $parameterTypeRepo->find($parameterTypeId);

        $parameter->setName($name);
        $parameter->setDisplayName($displayName);
        $parameter->setParameterType($parameterType);


        $this->em->persist($parameter);
        $this->em->flush();
        
        $response = $id ? 'Parametre başarı ile güncellendi.' : 'Parametre başarı ile oluşturuldu.';


        return ApiResponse::createSuccessResponse([], $response);
    }

    public function delete($id)
    {
        $parameterRepo = $this->em->getRepository(Parameter::class);

        $parameter = $parameterRepo->find($id);

        if (!$id) {
            return ApiResponse::createErrorResponse(422, 'Böyle bir parametre bulunamadı', []);
        }

        $this->em->remove($parameter);
        $this->em->flush();
        
        return ApiResponse::createSuccessResponse([], 'Parametre başarı ile silindi.');
    }
}