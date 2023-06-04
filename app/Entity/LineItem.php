<?php

namespace App\Entity;

use App\Repository\LineItemRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LineItemRepository::class)
 */
class LineItem
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $unit;

    /**
     * @ORM\Column(type="integer")
     */
    private $quantity;

    /**
     * @ORM\Column(type="decimal", precision=20, scale=2)
     */
    private $unitPriceTaxExcl;

    /**
     * @ORM\Column(type="decimal", precision=20, scale=2)
     */
    private $unitTaxTotal;

    /**
     * @ORM\Column(type="decimal", precision=20, scale=2)
     */
    private $totalPriceTaxExcl;

    /**
     * @ORM\Column(type="decimal", precision=20, scale=2)
     */
    private $totalTax;

    /**
     * @ORM\Column(type="decimal", precision=20, scale=2)
     */
    private $taxRate;

    /**
     * @ORM\ManyToOne(targetEntity=TaxDocument::class, inversedBy="lineItems")
     * @ORM\JoinColumn(nullable=false)
     */
    private $taxDocument;

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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getUnit(): ?string
    {
        return $this->unit;
    }

    public function setUnit(string $unit): self
    {
        $this->unit = $unit;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getUnitPriceTaxExcl(): ?string
    {
        return $this->unitPriceTaxExcl;
    }

    public function setUnitPriceTaxExcl(string $unitPriceTaxExcl): self
    {
        $this->unitPriceTaxExcl = $unitPriceTaxExcl;

        return $this;
    }

    public function getUnitTaxTotal(): ?string
    {
        return $this->unitTaxTotal;
    }

    public function setUnitTaxTotal(string $unitTaxTotal): self
    {
        $this->unitTaxTotal = $unitTaxTotal;

        return $this;
    }

    public function getTotalPriceTaxExcl(): ?string
    {
        return $this->totalPriceTaxExcl;
    }

    public function setTotalPriceTaxExcl(string $totalPriceTaxExcl): self
    {
        $this->totalPriceTaxExcl = $totalPriceTaxExcl;

        return $this;
    }

    public function getTotalTax(): ?string
    {
        return $this->totalTax;
    }

    public function setTotalTax(string $totalTax): self
    {
        $this->totalTax = $totalTax;

        return $this;
    }

    public function getTaxRate(): ?string
    {
        return $this->taxRate;
    }

    public function setTaxRate(string $taxRate): self
    {
        $this->taxRate = $taxRate;

        return $this;
    }

    public function getTaxDocument(): ?TaxDocument
    {
        return $this->taxDocument;
    }

    public function setTaxDocument(?TaxDocument $taxDocument): self
    {
        $this->taxDocument = $taxDocument;

        return $this;
    }
}
