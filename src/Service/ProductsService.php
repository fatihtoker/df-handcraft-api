<?php

namespace App\Service;

use App\Entity\Parameter;
use App\Entity\ParameterType;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Response\ApiResponse;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;
use App\Entity\UploadedFile;
use App\Service\Shared\RequestValidationService;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProductsService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var ProductRepository
     */
    private $repo;

    /**
     * @var UploaderHelper
     */
    private $uploaderHelper;

    /**
     * @var RequestValidationService
     */
    private $validator;

    public function __construct(EntityManagerInterface $em, ProductRepository $repo, UploaderHelper $uploaderHelper, RequestValidationService $validator)
    {
        $this->em = $em;
        $this->repo = $repo;
        $this->uploaderHelper = $uploaderHelper;
        $this->validator = $validator;
    }

    public function getAll(Request $request, $filterOnSale = false)
    {
        $productRepo = $this->em->getRepository(Product::class);
        $paramRepo = $this->em->getRepository(Parameter::class);
        $query = $request->get('query');
        $type = $request->get('type');
        $data = [];
        if ($query) {
            $data = $productRepo->getSearchResults($query, $filterOnSale);
        } else if ($type) {
            $param = $paramRepo->findOneBy(['name' => $type]);
            if ($filterOnSale) {
                $data = $productRepo->findBy(['type' => $param, 'onSale' => true]);
            } else {
                $data = $productRepo->findBy(['type' => $param]);
            }
            
        } else {
            $data = $filterOnSale ? $productRepo->findOnSale() : $productRepo->findAll();
        }
        $result = [];
        // foreach ($data as $item) {
        //     $object = (object) ['obj' => $item, 'imgUrl' => $this->uploaderHelper->asset($item, 'imageFile')];
        //     array_push($result, $object);
        // }
        return $data;
    }

    public function delete($id)
    {
        $productRepo = $this->em->getRepository(Product::class);

        $product = $productRepo->find($id);

        if (!$id) {
            return ApiResponse::createErrorResponse(422, 'Böyle bir ürün bulunamadı', []);
        }

        $this->em->remove($product);
        $this->em->flush();
        
        return ApiResponse::createSuccessResponse([], 'Ürün başarı ile silindi.');
    }

    public function getAllByCategory()
    {
        $productRepo = $this->em->getRepository(Product::class);
        $paramRepo = $this->em->getRepository(Parameter::class);
        $paramTypeRepo = $this->em->getRepository(ParameterType::class);

        $categoryParam = $paramTypeRepo->findOneBy(['name' => 'product_category']);

        $categories = $paramRepo->findBy(['parameterType' => $categoryParam]);

        $data = [];

        foreach ($categories as $category) {
            $productsArr = $productRepo->findBy(['category' => $category]);
            $data[] = ['category' => $category->getDisplayName(), 'products' => $productsArr];
        }

        return $data;
    }

    public function getCategories() {
        $paramRepo = $this->em->getRepository(Parameter::class);
        $paramTypeRepo = $this->em->getRepository(ParameterType::class);

        $categoryParam = $paramTypeRepo->findOneBy(['name' => 'product_category']);

        return $paramRepo->findBy(['parameterType' => $categoryParam]);
    }

    public function getTypes() {
        $paramRepo = $this->em->getRepository(Parameter::class);
        $paramTypeRepo = $this->em->getRepository(ParameterType::class);

        $categoryParam = $paramTypeRepo->findOneBy(['name' => 'product_type']);

        return $paramRepo->findBy(['parameterType' => $categoryParam]);
    }

    public function create(Request $request, $user)
    {
        $productRepo = $this->em->getRepository(Product::class);
        $paramRepo = $this->em->getRepository(Parameter::class);
        $uploadedFileRepo = $this->em->getRepository(UploadedFile::class);



        $hasError = $this->validator->validate($request, [
            'name'          => [new NotBlank([
                'message' => 'Bu değer boş bırakılamaz.'
            ])],
            'category'   => [new NotBlank([
                'message' => 'Bu değer boş bırakılamaz.'
            ])],
            'onSale' => [new notBlank([
                'message' => 'Bu değer boş bırakılamaz.'
            ])],
            'price' => [new notBlank([
                'message' => 'Bu değer boş bırakılamaz.'
            ])],
        ]);

        if ($hasError) {
            return $hasError;
        }

        $id = $request->get('id');
        $name = $request->get('name');
        $_category = $request->get('category');
        $description = $request->get('description');
        $_onSale = $request->get('onSale');
        $price = $request->get('price');
        $_type = $request->get('type');
        $images = $request->files->all();

        $onSale = ($_onSale === 'true');

        $category = $paramRepo->find($_category);
        $type = $paramRepo->find($_type);

        

        if ($id) {
            $product = $productRepo->find($id);
        } else {
            $product = new Product();
        }

        $product->setName($name);
        $product->setCategory($category);
        $product->setDescription($description);
        $product->setOnSale($onSale);
        $product->setPrice($price);
        $product->setType($type);

        

        $this->em->persist($product);
        $this->em->flush();

        if ($id) {
            $oldImages = $uploadedFileRepo->findBy(['product' => $id]);
            foreach ($oldImages as $oldImage) {
                $this->em->remove($oldImage);
            }
            
        }

        foreach ($images as $image) {
            // todo: handle update
            $uploadedFile = new UploadedFile();
            $originalName = $image->getClientOriginalName();
            $uploadedFile->setOriginalName($originalName);
            $uploadedFile->setUser($user);
            $uploadedFile->setFolder($product->getId());
            $uploadedFile->setActive(true);
            $uploadedFile->setType($image->getMimeType());
            $uploadedFile->setSize($image->getClientSize());
            $uploadedFile->setDocumentFile($image);
            $uploadedFile->setProduct($product);
            $this->em->persist($uploadedFile);
            
        }

        $this->em->flush();
        
        
        $response = $id ? 'Ürün başarı ile güncellendi.' : 'Ürün başarı ile oluşturuldu.';


        return ApiResponse::createSuccessResponse([], $response);
    }

    public function getProduct(Request $request, $id)
    {
        $productRepo = $this->em->getRepository(Product::class);

        $product = $productRepo->find($id);

        if (!$product) {
            return ApiResponse::createErrorResponse(422, 'Ürün bulunamadı.', []);
        }

        return ApiResponse::createSuccessResponse($product);
    }
}