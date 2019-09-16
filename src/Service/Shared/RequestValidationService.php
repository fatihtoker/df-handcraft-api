<?php

namespace App\Service\Shared;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Response\ApiResponse;

class RequestValidationService
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * RequestValidationService constructor.
     *
     * @param ValidatorInterface $validator
     */
    public function __construct(
        ValidatorInterface $validator
    ) {
        $this->validator = $validator;
    }

    /**
     * @param Request $request
     * @param array $params
     */
    public function validate(Request $request, $params)
    {
        $invalid = false;
        $errors = [];
        foreach ($params as $param => $constraint) {

            if ($request->files->get($param)) {
                $value = $request->files->get($param);
            } else {
                $value = $request->get($param);
            }

            $violations = $this->validator->validate($value, $constraint);
            $errors[ $param ] = $violations;

            $invalid = $invalid || $violations->count() > 0;

        }

        if ($invalid) {
            return ApiResponse::createErrorResponse(422, 'Kayıt başarısız.', $errors);
        }
    }
}