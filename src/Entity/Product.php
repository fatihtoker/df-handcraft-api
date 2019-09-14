<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 *
 * @Serializer\ExclusionPolicy("all")
 */
class Product
{
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Serializer\Expose()
     * @Serializer\Groups({"Default", "products_list", "product_category"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Serializer\Expose()
     * @Serializer\Groups({"Default", "products_list", "product_category"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=520, nullable=true)
     * @Serializer\Expose()
     * @Serializer\Groups({"Default", "products_list"})
     */
    private $description;

    /**
     * @ORM\Column(type="boolean")
     * @Serializer\Expose()
     * @Serializer\Groups({"Default", "products_list"})
     */
    private $onSale;

    /**
     * @ORM\Column(type="integer")
     * @Serializer\Expose()
     * @Serializer\Groups({"Default", "products_list", "product_category"})
     */
    private $price;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Parameter")
     * @ORM\JoinColumn(nullable=false)
     * @Serializer\Expose()
     * @Serializer\Groups({"Default", "products_list"})
     */
    private $category;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Parameter")
     * @ORM\JoinColumn(nullable=true)
     * @Serializer\Expose()
     * @Serializer\Groups({"Default"})
     */
    private $type;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UploadedFile", mappedBy="product")
     * @Serializer\Expose()
     * @Serializer\Groups({"Default", "products_list"})
     */
    private $images;

    public function __construct()
    {
        $this->images = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getOnSale(): ?bool
    {
        return $this->onSale;
    }

    public function setOnSale(bool $onSale): self
    {
        $this->onSale = $onSale;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getCategory(): ?Parameter
    {
        return $this->category;
    }

    public function setCategory(?Parameter $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getType(): ?Parameter
    {
        return $this->type;
    }

    public function setType(?Parameter $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection|UploadedFile[]
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(UploadedFile $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setProduct($this);
            $image->setFolder($this->id);
        }

        return $this;
    }

    public function removeImage(UploadedFile $image): self
    {
        if ($this->images->contains($image)) {
            $this->images->removeElement($image);
            // set the owning side to null (unless already changed)
            if ($image->getProduct() === $this) {
                $image->setProduct(null);
            }
        }

        return $this;
    }

    public function emptyImages(): self
    {
        foreach ($this->images as $image) {
            if ($this->images->contains($image)) {
                $this->images->removeElement($image);
                // set the owning side to null (unless already changed)
                if ($image->getProduct() === $this) {
                    $image->setProduct(null);
                }
            }
        }
        return $this;
    }
}
