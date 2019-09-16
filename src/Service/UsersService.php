<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Role;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Response\ApiResponse;
use App\Service\Shared\RequestValidationService;
use Symfony\Component\Validator\Constraints\NotBlank;

class UsersService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    /**
     * @var RequestValidationService
     */
    private $validator;

    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $encoder, RequestValidationService $validator)
    {
        $this->em = $em;
        $this->encoder = $encoder;
        $this->validator = $validator;
    }

    public function create(Request $request)
    {
        $userRepo = $this->em->getRepository(User::class);
        $roleRepo = $this->em->getRepository(Role::class);

        $hasError = $this->validator->validate($request, [
            'email'          => [new NotBlank([
                'message' => 'Bu değer boş bırakılamaz.'
            ])],
            'password'   => [new NotBlank([
                'message' => 'Bu değer boş bırakılamaz.'
            ])],
            'roles'   => [new NotBlank([
                'message' => 'Bu değer boş bırakılamaz.'
            ])]
        ]);

        if ($hasError) {
            return $hasError;
        }

        $id = $request->get('id');
        $email = $request->get('email');
        $plainPassword = $request->get('password');
        $roles = $request->get('roles');

        $roleCollection = [];

        

        if ($id) {
             $user = $userRepo->find($id);
        } else {
            $user = new User();
        }
        $encodedPassword = $this->encoder->encodePassword($user, $plainPassword);

        $user->setEmail($email);
        $user->setPassword($encodedPassword);

        foreach ($roles as $role) {
            $roleCollection[] = $roleRepo->find($role['id']);
        }

        $user->setRoles($roleCollection);

        $this->em->persist($user);
        $this->em->flush();
        
        $response = $id ? 'Kullanıcı başarı ile güncellendi.' : 'Kullanıcı başarı ile oluşturuldu.';


        return ApiResponse::createSuccessResponse([], $response);
    }

    public function delete($id)
    {
        $userRepo = $this->em->getRepository(User::class);

        $user = $userRepo->find($id);

        if (!$id) {
            return ApiResponse::createErrorResponse(422, 'Böyle bir kullanıcı bulunamadı', []);
        }

        $this->em->remove($user);
        $this->em->flush();
        
        return ApiResponse::createSuccessResponse([], 'Kullanıcı başarı ile silindi.');
    }

    public function getAll(Request $request)
    {
        $userRepo = $this->em->getRepository(User::class);

        $data = $userRepo->findAll();
        
        return $data;
    }
}