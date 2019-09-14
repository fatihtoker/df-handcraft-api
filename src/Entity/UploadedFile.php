<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * UploadedFile
 * @ORM\Table(name="uploaded_files")
 * @ORM\Entity(repositoryClass="App\Repository\UploadedFileRepository")
 * @Vich\Uploadable
 * @Serializer\ExclusionPolicy("all")
 *
 */
class UploadedFile
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Serializer\Expose()
     * @Serializer\Groups({"Default", "products_list"})
     */
    private $id;

    /**
     * @Vich\UploadableField(mapping="file", fileNameProperty="uniqueName")
     * @var File
     *
     */
    private $documentFile;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     * @Serializer\Expose()
     */
    private $originalName;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     *
     * @Serializer\Expose()
     */
    private $size;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=120)
     *
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, unique=true)
     *
     * @Serializer\Expose()
     * @Serializer\Groups({"Default", "products_list"})
     */
    private $uniqueName;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     * @Serializer\Expose()
     * @Serializer\Groups({"Default", "products_list"})
     */
    private $folder;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     */
    private $user;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Product", inversedBy="images")
     */
    private $product;

    use TimestampableEntity;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set orginalName
     *
     * @param string $originalName
     *
     * @return UploadedFile
     */
    public function setOriginalName($originalName)
    {
        $this->originalName = $originalName;

        return $this;
    }

    /**
     * Get orginalName
     *
     * @return string
     */
    public function getOriginalName()
    {
        return $this->originalName;
    }

    /**
     * Set size
     *
     * @param integer $size
     *
     * @return UploadedFile
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size
     *
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return UploadedFile
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set uniqueName
     *
     * @param string $uniqueName
     *
     * @return UploadedFile
     */
    public function setUniqueName($uniqueName)
    {
        $this->uniqueName = $uniqueName;

        return $this;
    }

    /**
     * Get uniqueName
     *
     * @return string
     */
    public function getUniqueName()
    {
        return $this->uniqueName;
    }

    /**
     * @return string
     */
    public function getFolder()
    {
        return $this->folder;
    }

    /**
     * @param string $folder
     */
    public function setFolder($folder)
    {
        $this->folder = $folder;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param boolean $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }

    /**
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $file
     *
     * @return $this
     */
    public function setDocumentFile(File $file = null)
    {
        $this->documentFile = $file;

        if ($file) {
            $this->updatedAt = new \DateTime('now');
        }

        return $this;
    }

    /**
     * @return File|null
     */
    public function getDocumentFile()
    {
        return $this->documentFile;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }
}

