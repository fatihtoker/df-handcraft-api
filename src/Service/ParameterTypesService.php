<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\ParameterType;
use App\Response\ApiResponse;
use App\Service\Shared\RequestValidationService;
use Symfony\Component\Validator\Constraints\NotBlank;

class ParameterTypesService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var RequestValidationService
     */
    private $validator;

    public function __construct(EntityManagerInterface $em, RequestValidationService $validator)
    {
        $this->em = $em;
        $this->validator = $validator;
    }

    public function getAll(Request $request)
    {
        $parameterTypeRepo = $this->em->getRepository(ParameterType::class);

        $data = $parameterTypeRepo->findAll();
        
        return $data;
    }

    public function create(Request $request)
    {
        $parameterTypeRepo = $this->em->getRepository(ParameterType::class);

        $id = $request->get('id');
        $name = $request->get('name');
        $displayName = $request->get('displayName');

        $hasError = $this->validator->validate($request, [
            'name'          => [new NotBlank([
                'message' => 'Bu değer boş bırakılamaz.'
            ])],
            'displayName'   => [new NotBlank([
                'message' => 'Bu değer boş bırakılamaz.'
            ])]
        ]);

        if ($hasError) {
            return $hasError;
        }

        if ($id) {
            $parameterType = $parameterTypeRepo->find($id);
        } else {
            $parameterType = new ParameterType();
        }

        $parameterType->setName($name);
        $parameterType->setDisplayName($displayName);


        $this->em->persist($parameterType);
        $this->em->flush();
        
        $response = $id ? 'Parametre Türü başarı ile güncellendi.' : 'Parametre Türü başarı ile oluşturuldu.';


        return ApiResponse::createSuccessResponse([], $response);
    }

    public function delete($id)
    {
        $parameterTypeRepo = $this->em->getRepository(ParameterType::class);

        $parameterType = $parameterTypeRepo->find($id);

        if (!$id) {
            return ApiResponse::createErrorResponse(422, 'Böyle bir parametre türü bulunamadı', []);
        }

        $this->em->remove($parameterType);
        $this->em->flush();
        
        return ApiResponse::createSuccessResponse([], 'Parametre Türü başarı ile silindi.');
    }
}