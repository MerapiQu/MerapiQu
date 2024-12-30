<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'users')]
class User
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    private $id;

    #[ORM\Column(type: 'string', length: 255, nullable: false)] // Ensure nullable is false
    private $username;

    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    private $email;



    public function getId()
    {
        return $this->id;
    }

    function setId($id)
    {
        $this->id = $id;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    // public function getPassword()
    // {
    //     return $this->password;
    // }

    // public function setPassword($password)
    // {
    //     $this->password = $password;
    // }
}
