<?php

namespace MerapiPanel\Entity;

use Doctrine\ORM\Mapping as ORM;


enum PageStatus: int
{
    case PUBLIC = 1;
    case PRIVATE = 0;
}

#[ORM\Entity]
#[ORM\Table(name: 'pages')]
class Page
{

    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    private $id;

    #[ORM\Column(type: 'integer', enumType: PageStatus::class)]
    private PageStatus $status;

    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    private $slug;

    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    private $title;

    #[ORM\Column(type: 'json', nullable: false)]
    private $head;

    #[ORM\Column(type: 'json', nullable: false)]
    private $body;


    public function getId()
    {
        return $this->id;
    }

    function setId($id)
    {
        $this->id = $id;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getHead()
    {
        return $this->head;
    }

    public function setHead($head)
    {
        $this->head = $head;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function setBody($body)
    {
        $this->body = $body;
    }
}
