<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CurrencyRateRepository")
 * @ORM\Table(name="currency_rates")
 */
class CurrencyRate
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=3)
     */
    private $currency;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=4)
     */
    private $rate;

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

}
