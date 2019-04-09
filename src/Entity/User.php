<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Config\Definition\Exception\DuplicateKeyException;
use Doctrine\ORM\EntityNotFoundException;

/**
 * User Entity
 * 
 * @category Entity
 * @package  SrcEntity
 * @author   Andrej <akicay@gmail.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/andrejrs
 * 
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(
 *     fields={"email"},
 *     message="The {{ value }} is already used."
 * )
 */
class User implements UserInterface
{
    /**
     * User roles
     * It is used for checking when saving 
     * as well as for creating a select field in form.
     */
    const ROLES =  [
        'Admin' => "ROLE_ADMIN",
        'User' => 'ROLE_USER',
        'Customer' => 'ROLE_CUSTOMER'
    ];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * 
     * @var Integer
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank
     * @Assert\NotNull
     * 
     * @var String
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     * @Assert\NotBlank
     * @Assert\NotNull
     * 
     * @var String(json)
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Assert\NotBlank
     * @Assert\NotNull
     * 
     * @var String
     */
    private $password;

    /**
     * Many Users have Many Groups.
     * @ORM\ManyToMany(targetEntity="UserGroup", inversedBy="users")
     * @ORM\JoinTable(name="users_groups")
     * @MaxDepth(1)
     * 
     * @var ArrayCollection
     */
    private $groups;
    
    /**
     * Set ArrayCollection for the user groups
     */
    public function __construct()
    {
        $this->groups = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Undocumented function
     *
     * @return ArrayCollection Array collection of user groups
     */
    public static function getAvaibleRoles(): ArrayCollection
    {
        return User::ROLES;
    }

    /**
     * Method for validating user roles
     *
     * @param  Array $roles User roles
     * @return void
     */
    public static function validateRoles(Array $roles)
    {
        foreach ($roles as $role) {
            if (!in_array($role, User::ROLES)) {
                throw new \InvalidArgumentException(
                    "The role $role is not valid! Valid roles are: ".\implode(User::ROLES)
                );
            }
        }
    }

    /**
     * Method for getting groups for certain user
     * 
     * @return Collection|UserGroup[]
     */
    public function getGroups(): Collection
    {
        return $this->groups;
    }

    /**
     * Method for adding a group to user
     *
     * @param  UserGroup $group User group entity
     * @return void
     */
    public function addGroup(UserGroup $group)
    {
        
        if ($this->groups->contains($group)) {
            throw new DuplicateKeyException("The user is already in ".$group->getName()." group.");
        }
        $this->groups->add($group);
    }

    /**
     * Method for removing a group from user
     *
     * @param  UserGroup $group User group entity
     * @return void
     */
    public function removeGroup(UserGroup $group)
    {
        if ($this->groups->contains($group) === false) {
            throw new EntityNotFoundException("The user is not in ".$group->getName()." group.");
        }
        $this->groups->removeElement($group);
    }

    /**
     * Getting User identification
     *
     * @return integer|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Getting User email
     *
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Setting User email
     *
     * @param string $email
     * @return self
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;

        return array_unique($roles);
    }

    /**
     * Setting User roles
     *
     * @param array|null $roles Array of User roles
     * @return self
     */
    public function setRoles(?array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    /**
     * Setting User password
     *
     * @param string $password User password
     * @return self
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * Method that returns the string representation of the object. 
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->email;
    }
}
