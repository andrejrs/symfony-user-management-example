<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * UserGroup Entity
 * 
 * @category Entity
 * @package  SrcEntity
 * @author   Andrej <akicay@gmail.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/andrejrs
 * 
 * @ORM\Entity(repositoryClass="App\Repository\UserGroupRepository")
 * @UniqueEntity(
 *     fields={"name" },
 *     message="The name {{ value }} is already used."
 * )
 */
class UserGroup
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * 
     * @var Integer
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=155)
     * @Assert\NotBlank
     * @Assert\NotNull
     * 
     * @var String
     */
    private $name;

    /**
     * Many Groups have Many Users.
     * @ORM\ManyToMany(targetEntity="User", mappedBy="groups")
     * @MaxDepth(1)
     * 
     * @var ArrayCollection
     */
    private $users;

    /**
     * Set ArrayCollection for the users
     */
    public function __construct()
    {
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Method returns users who are in a particular group
     * 
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    /**
     * Getting User Group identification
     *
     * @return integer|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Getting User Group name
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Setting User Group name
     *
     * @param string $name
     * @return self
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Method that returns the string representation of the object. 
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->name;
    }
}
