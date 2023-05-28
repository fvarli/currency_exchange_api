<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CurrencyRateRepository")
 * @ORM\Table(name="currency_rates")
 * 
 * This entity class represents a record of a currency exchange rate.
 */
class CurrencyRate
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * 
     * The unique identifier for this record.
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=3)
     * 
     * The ISO code of the currency.
     */
    private $currency;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=4)
     * 
     * The exchange rate of the currency against the base currency.
     */
    private $rate;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    // add getter and setter methods for properties

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;
        return $this;
    }

    public function getRate(): ?string
    {
        return $this->rate;
    }

    public function setRate(string $rate): self
    {
        $this->rate = $rate;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }
}
