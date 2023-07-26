<?php

namespace App\Entity;

use App\Entity\Traits\ChangesLoggableTrait;
use App\Repository\PaymentDataRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PaymentDataRepository::class)
 */
class PaymentData
{
    use ChangesLoggableTrait;

    const TYPE_BANK_PAYMENT = 'bank_payment';
    const TYPE_CASH_ON_DELIVERY = 'cash_on_delivery';
    const TYPE_CASH = 'cash';
    const TYPE_PAYPAL = 'paypal';
    const TYPE_PAYMENT_CARD = 'payment_card';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $paypalMail;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $bankAccountNumber;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $bankAccountIban;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $bankAccountSwift;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getPaypalMail(): ?string
    {
        return $this->paypalMail;
    }

    public function setPaypalMail(?string $paypalMail): self
    {
        $this->paypalMail = $paypalMail;

        return $this;
    }

    public function getBankAccountNumber(): ?string
    {
        return $this->bankAccountNumber;
    }

    public function setBankAccountNumber(?string $bankAccountNumber): self
    {
        $this->bankAccountNumber = $bankAccountNumber;

        return $this;
    }

    public function getBankAccountIban(): ?string
    {
        return $this->bankAccountIban;
    }

    public function setBankAccountIban(?string $bankAccountIban): self
    {
        $this->bankAccountIban = $bankAccountIban;

        return $this;
    }

    public function getBankAccountSwift(): ?string
    {
        return $this->bankAccountSwift;
    }

    public function setBankAccountSwift(?string $bankAccountSwift): self
    {
        $this->bankAccountSwift = $bankAccountSwift;

        return $this;
    }
}
