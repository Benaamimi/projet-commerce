<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;



#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank( message: "Le nom est obligatoire")]
    #[Assert\Length(  
                min: 3,
                max: 50,
                minMessage: "Le nom doit contenir plus que {{ limit }} caractères",
                maxMessage: "Le nom doit contenir moin que {{ limit }} caractères"
                )]
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[Assert\NotBlank( message: "Le prix est obligatoire")]
    #[ORM\Column]
    private ?int $price = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    private ?Category $category = null;

    #[Assert\Url(message: "La photo doit être une url valide")]
    #[Assert\NotBlank(message: "Ce champ est obligatoire")]
    #[ORM\Column(length: 255)]
    private ?string $mainPicture = null;
    
    #[Assert\NotBlank(message: "La pdescription courte est obligatoire")]
    #[Assert\Length(
        min: 20,
        minMessage: "La description doit contenir plus que {{ limit }} caractères" 
    )]
    #[ORM\Column(type: Types::TEXT)]
    private ?string $shortDescription = null;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: PurchaseItem::class)]
    private Collection $purchaseItems;


    public function __construct()
    {
        $this->purchaseItems = new ArrayCollection();
    }



    public function getUppercaseName(): string
    {
        return strtoupper($this->name);
    }


    //! FONCTION POUR LES CONTRAITES ET VALIDATION :
    //? public static function loadValidatorMetadata(ClassMetadata $metaData)
    // {
    //     $metaData->addPropertyConstraints('name', [
    //         new Assert\NotBlank(['message' => "Le nom est obligatoire"]),
    //         new Assert\Length([
    //             'min' => 3,
    //             'max' => 50,
    //             'minMessage' => "Le nom doit contenir plus que {{ limit }} caractères",
    //             'maxMessage' => "Le nom doit contenir moin que {{ limit }} caractères"
    //         ])
    //     ]);
    //     $metaData->addPropertyConstraint('price', new Assert\NotBlank(['message' => "Le prix est obligatoire"]));
    // }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(?int $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getMainPicture(): ?string
    {
        return $this->mainPicture;
    }

    public function setMainPicture(?string $mainPicture): static
    {
        $this->mainPicture = $mainPicture;

        return $this;
    }

    public function getShortDescription(): ?string
    {
        return $this->shortDescription;
    }

    public function setShortDescription(?string $shortDescription): static
    {
        $this->shortDescription = $shortDescription;

        return $this;
    }

    /**
     * @return Collection<int, PurchaseItem>
     */
    public function getPurchaseItems(): Collection
    {
        return $this->purchaseItems;
    }

    public function addPurchaseItem(PurchaseItem $purchaseItem): static
    {
        if (!$this->purchaseItems->contains($purchaseItem)) {
            $this->purchaseItems->add($purchaseItem);
            $purchaseItem->setProduct($this);
        }

        return $this;
    }

    public function removePurchaseItem(PurchaseItem $purchaseItem): static
    {
        if ($this->purchaseItems->removeElement($purchaseItem)) {
            // set the owning side to null (unless already changed)
            if ($purchaseItem->getProduct() === $this) {
                $purchaseItem->setProduct(null);
            }
        }

        return $this;
    }

}
