<?php

namespace App\Entity;

use App\Entity\Traits\ChangesLoggableTrait;
use App\Repository\TaxDocumentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TaxDocumentRepository::class)
 */
class TaxDocument
{
    use ChangesLoggableTrait;

    const TYPE_INVOICE = 'invoice';
    const TYPE_ADVANCE_PAYMENT = 'advance_payment';
    const TYPE_PROFORMA_INVOCE = 'proforma_invoice';
    const TYPE_CREDIT_NOTE = 'credit_note';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=UserCompany::class, inversedBy="taxDocuments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $userCompany;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\Column(type="boolean")
     */
    private $transferedTaxLiability;

    /**
     * @ORM\Column(type="boolean")
     */
    private $vatPayer;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $number;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $constantSymbol;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $specificSymbol;

    /**
     * @ORM\ManyToOne(targetEntity=Contact::class, inversedBy="taxDocuments")
     */
    private $contact;

    /**
     * @ORM\ManyToOne(targetEntity=BankAccount::class)
     */
    private $bankAccount;

    /**
     * @ORM\ManyToOne(targetEntity=Address::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $supplierBillingAddress;

    /**
     * @ORM\ManyToOne(targetEntity=Address::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $subscriberBillingAddress;

    /**
     * @ORM\ManyToOne(targetEntity=PaymentData::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $paymentData;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $currencyCode;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $localeCode;

    /**
     * @ORM\Column(type="decimal", precision=20, scale=2)
     */
    private $totalPriceTaxExcl;

    /**
     * @ORM\Column(type="decimal", precision=20, scale=2)
     */
    private $totalPriceTaxIncl;

    /**
     * @ORM\OneToMany(targetEntity=LineItem::class, mappedBy="taxDocument", cascade={"persist", "remove"})
     */
    private $lineItems;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $noteAboveItems;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $note;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $sentAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $paidAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $issuedBy;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $issuedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deliveryDateAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dueDateAt;

    public function __construct()
    {
        $this->lineItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserCompany(): ?UserCompany
    {
        return $this->userCompany;
    }

    public function setUserCompany(?UserCompany $userCompany): self
    {
        $this->userCompany = $userCompany;

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

    public function getTransferedTaxLiability(): ?bool
    {
        return $this->transferedTaxLiability;
    }

    public function setTransferedTaxLiability(bool $transferedTaxLiability): self
    {
        $this->transferedTaxLiability = $transferedTaxLiability;

        return $this;
    }

    public function getVatPayer(): ?bool
    {
        return $this->vatPayer;
    }

    public function setVatPayer(bool $vatPayer): self
    {
        $this->vatPayer = $vatPayer;

        return $this;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(string $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getConstantSymbol(): ?string
    {
        return $this->constantSymbol;
    }

    public function setConstantSymbol(?string $constantSymbol): self
    {
        $this->constantSymbol = $constantSymbol;

        return $this;
    }

    public function getSpecificSymbol(): ?string
    {
        return $this->specificSymbol;
    }

    public function setSpecificSymbol(?string $specificSymbol): self
    {
        $this->specificSymbol = $specificSymbol;

        return $this;
    }

    public function getContact(): ?Contact
    {
        return $this->contact;
    }

    public function setContact(?Contact $contact): self
    {
        $this->contact = $contact;

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

    public function getSupplierBillingAddress(): ?Address
    {
        return $this->supplierBillingAddress;
    }

    public function setSupplierBillingAddress(?Address $supplierBillingAddress): self
    {
        $this->supplierBillingAddress = $supplierBillingAddress;

        return $this;
    }

    public function getSubscriberBillingAddress(): ?Address
    {
        return $this->subscriberBillingAddress;
    }

    public function setSubscriberBillingAddress(?Address $subscriberBillingAddress): self
    {
        $this->subscriberBillingAddress = $subscriberBillingAddress;

        return $this;
    }

    public function getPaymentData(): ?PaymentData
    {
        return $this->paymentData;
    }

    public function setPaymentData(?PaymentData $paymentData): self
    {
        $this->paymentData = $paymentData;

        return $this;
    }

    public function getCurrencyCode(): ?string
    {
        return $this->currencyCode;
    }

    public function setCurrencyCode(string $currencyCode): self
    {
        $this->currencyCode = $currencyCode;

        return $this;
    }

    public function getLocaleCode(): ?string
    {
        return $this->localeCode;
    }

    public function setLocaleCode(string $localeCode): self
    {
        $this->localeCode = $localeCode;

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

    public function getTotalPriceTaxIncl(): ?string
    {
        return $this->totalPriceTaxIncl;
    }

    public function setTotalPriceTaxIncl(string $totalPriceTaxIncl): self
    {
        $this->totalPriceTaxIncl = $totalPriceTaxIncl;

        return $this;
    }

    public function clearLineItems(): void
    {
        foreach ($this->getLineItems() as $lineItem) {
            $this->lineItems->removeElement($lineItem);
        }
    }

    /**
     * @return Collection<int, LineItem>
     */
    public function getLineItems(): Collection
    {
        return $this->lineItems;
    }

    public function addLineItem(LineItem $lineItem): self
    {
        if (!$this->lineItems->contains($lineItem)) {
            $this->lineItems[] = $lineItem;
            $lineItem->setTaxDocument($this);
        }

        return $this;
    }

    public function removeLineItem(LineItem $lineItem): self
    {
        if ($this->lineItems->removeElement($lineItem)) {
            // set the owning side to null (unless already changed)
            if ($lineItem->getTaxDocument() === $this) {
                $lineItem->setTaxDocument(null);
            }
        }

        return $this;
    }

    public function getNoteAboveItems(): ?string
    {
        return $this->noteAboveItems;
    }

    public function setNoteAboveItems(?string $noteAboveItems): self
    {
        $this->noteAboveItems = $noteAboveItems;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function getSentAt(): ?\DateTimeImmutable
    {
        return $this->sentAt;
    }

    public function setSentAt(?\DateTimeImmutable $sentAt): self
    {
        $this->sentAt = $sentAt;

        return $this;
    }

    public function getPaidAt(): ?\DateTimeImmutable
    {
        return $this->paidAt;
    }

    public function setPaidAt(?\DateTimeImmutable $paidAt): self
    {
        $this->paidAt = $paidAt;

        return $this;
    }

    public function getIssuedBy(): ?string
    {
        return $this->issuedBy;
    }

    public function setIssuedBy(?string $issuedBy): self
    {
        $this->issuedBy = $issuedBy;

        return $this;
    }

    public function getIssuedAt(): ?\DateTime
    {
        return $this->issuedAt;
    }

    public function setIssuedAt(?\DateTime $issuedAt): self
    {
        $this->issuedAt = $issuedAt;

        return $this;
    }

    public function getDeliveryDateAt(): ?\DateTime
    {
        return $this->deliveryDateAt;
    }

    public function setDeliveryDateAt(?\DateTime $deliveryDateAt): self
    {
        $this->deliveryDateAt = $deliveryDateAt;

        return $this;
    }

    public function getDueDateAt(): ?\DateTime
    {
        return $this->dueDateAt;
    }

    public function setDueDateAt(?\DateTime $dueDateAt): self
    {
        $this->dueDateAt = $dueDateAt;

        return $this;
    }
}
