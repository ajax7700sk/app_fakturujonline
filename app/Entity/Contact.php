<?php

namespace App\Entity;

use App\Repository\ContactRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ContactRepository::class)
 */
class Contact
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="contacts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="boolean")
     */
    private $billingSameAsShipping;

    /**
     * @ORM\ManyToOne(targetEntity=Address::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $billingAddress;

    /**
     * @ORM\ManyToOne(targetEntity=Address::class)
     */
    private $shippingAddress;

    /**
     * @ORM\ManyToOne(targetEntity=BankAccount::class, inversedBy="contacts")
     */
    private $bankAccount;

    /**
     * @ORM\OneToMany(targetEntity=TaxDocument::class, mappedBy="contact")
     */
    private $taxDocuments;

    public function __construct()
    {
        $this->taxDocuments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getBillingSameAsShipping(): ?bool
    {
        return $this->billingSameAsShipping;
    }

    public function setBillingSameAsShipping(bool $billingSameAsShipping): self
    {
        $this->billingSameAsShipping = $billingSameAsShipping;

        return $this;
    }

    public function getBillingAddress(): ?Address
    {
        return $this->billingAddress;
    }

    public function setBillingAddress(?Address $billingAddress): self
    {
        $this->billingAddress = $billingAddress;

        return $this;
    }

    public function getShippingAddress(): ?Address
    {
        return $this->shippingAddress;
    }

    public function setShippingAddress(?Address $shippingAddress): self
    {
        $this->shippingAddress = $shippingAddress;

        return $this;
    }

    public function getBankAccount(): ?BankAccount
    {
        return $this->bankAccount;
    }

    public function setBankAccount(?BankAccount $bankAccount): self
    {
        $this->bankAccount = $bankAccount;

        return $this;
    }

    /**
     * @return Collection<int, TaxDocument>
     */
    public function getTaxDocuments(): Collection
    {
        return $this->taxDocuments;
    }

    public function addTaxDocument(TaxDocument $taxDocument): self
    {
        if (!$this->taxDocuments->contains($taxDocument)) {
            $this->taxDocuments[] = $taxDocument;
            $taxDocument->setContact($this);
        }

        return $this;
    }

    public function removeTaxDocument(TaxDocument $taxDocument): self
    {
        if ($this->taxDocuments->removeElement($taxDocument)) {
            // set the owning side to null (unless already changed)
            if ($taxDocument->getContact() === $this) {
                $taxDocument->setContact(null);
            }
        }

        return $this;
    }
}
