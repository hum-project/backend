<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ArgumentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *     collectionOperations={
 *         "get"
 *     },
 *     itemOperations={
 *         "get"
 *     }
 * )
 * @ORM\Entity(repositoryClass=ArgumentRepository::class)
 */
class Argument
{
    /**
     * @Groups({"hum"})
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups({"hum"})
     * @ORM\Column(type="string", length=255)
     */
    private $side;

    /**
     * @Groups({"hum"})
     * @ORM\Column(type="text")
     */
    private $text;

    /**
     * @ORM\OneToOne(targetEntity=Argument::class, inversedBy="child", cascade={"persist", "remove"})
     */
    private $parent;

    /**
     * @Groups({"hum"})
     * @ORM\OneToOne(targetEntity=Argument::class, mappedBy="parent", cascade={"persist", "remove"})
     */
    private $child;

    /**
     * @Groups({"hum"})
     * @ORM\ManyToOne(targetEntity=Language::class, inversedBy="arguments")
     */
    private $language;

    /**
     * @ORM\ManyToOne(targetEntity=Argument::class, inversedBy="translations")
     */
    private $translation;

    /**
     * @Groups({"hum"})
     * @ORM\OneToMany(targetEntity=Argument::class, mappedBy="translation")
     */
    private $translations;

    public function __construct()
    {
        $this->translations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSide(): ?string
    {
        return $this->side;
    }

    public function setSide(string $side): self
    {
        $this->side = $side;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    public function getChild(): ?self
    {
        return $this->child;
    }

    public function setChild(?self $child): self
    {
        $this->child = $child;

        return $this;
    }

    public function getDescendants()
    {
        $result = null;
        if (null !== $this->getChild()) {
            $array = array();
            $array[] = $this->getChild();
            $result = Argument::traverseDescendants($array, $this->getChild());
        }
        return $result;
    }

    public static function traverseDescendants($array, Argument $decendant)
    {
        $child = $decendant->getChild();
        if (null === $child) {
            return $array;
        }

        array_push($array, $child);
        return Argument::traverseDescendants($array, $child);

    }

    public function getLanguage(): ?Language
    {
        return $this->language;
    }

    public function setLanguage(?Language $language): self
    {
        $this->language = $language;

        return $this;
    }

    public function __toString()
    {
        return $this->getSide() . ': ' . substr($this->getText(), 0, 40);
    }

    public function getTranslation(): ?self
    {
        return $this->translation;
    }

    public function setTranslation(?self $translation): self
    {
        $this->translation = $translation;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getTranslations(): Collection
    {
        return $this->translations;
    }

    public function addTranslation(self $translation): self
    {
        if (!$this->translations->contains($translation)) {
            $this->translations[] = $translation;
            $translation->setTranslation($this);
        }

        return $this;
    }

    public function removeTranslation(self $translation): self
    {
        if ($this->translations->contains($translation)) {
            $this->translations->removeElement($translation);
            // set the owning side to null (unless already changed)
            if ($translation->getTranslation() === $this) {
                $translation->setTranslation(null);
            }
        }

        return $this;
    }
}
