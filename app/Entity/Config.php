<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "settings")]
class Config
{

    #[ORM\Id]
    #[ORM\Column(type: "string")]
    private $name;

    #[ORM\Column(type: "string", length: 255)]
    private $value;

    /**
     * @param string $name
     * @param string $value
     */
    public function __construct($name, $value)
    {
        $this->name = $name;
        $this->value = $value;
    }
    public function getName(): string
    {
        return $this->name;
    }
    public function getValue(): string
    {
        return $this->value;
    }
    public function setValue($value): void
    {
        $this->value = $value;
    }
}
