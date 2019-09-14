<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ParameterRepository")
 *
 * @Serializer\ExclusionPolicy("all")
 */
class Parameter
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Serializer\Expose()
     * @Serializer\Groups({"Default", "products_list"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Serializer\Expose()
     * @Serializer\Groups({"Default", "products_list"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Serializer\Expose()
     * @Serializer\Groups({"Default", "products_list"})
     */
    private $displayName;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ParameterType")
     * @ORM\JoinColumn(nullable=false)
     * @Serializer\Expose()
     * @Serializer\Groups({"Default"})
     */
    private $parameterType;

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

    public function getDisplayName(): ?string
    {
        return $this->displayName;
    }

    public function setDisplayName(?string $displayName): self
    {
        $this->displayName = $displayName;

        return $this;
    }

    public function getParameterType(): ?ParameterType
    {
        return $this->parameterType;
    }

    public function setParameterType(?ParameterType $parameterType): self
    {
        $this->parameterType = $parameterType;

        return $this;
    }
}
