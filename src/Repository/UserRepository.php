<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;

/**
 * User methods to read entities from the DB
 *
 * @category Controller
 * @package  SrcRepository
 * @author   Andrej <akicay@gmail.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/andrejrs
 */
class UserRepository extends ServiceEntityRepository
{
    /**
     * Represents EntityManager interface
     *
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * Set Registry and EntityManager for User entity
     *
     * @param RegistryInterface      $registry  References Doctrine connections and entity managers
     * @param EntityManagerInterface $em        EntityManager interface
     */
    public function __construct(
        RegistryInterface $registry, 
        EntityManagerInterface $em
    ) {
        parent::__construct($registry, User::class);
        $this->em = $em;
    }
 
    /**
     * Method to return User entity object by indentificator
     *
     * @param  Integer   $userId User indentificator
     * @return User      User entity object
     */
    public function findById(Int $userId) : User
    {
        $user = $this->find($userId);
        if (!$user) {
            throw new EntityNotFoundException('User with id '.$userId.' does not exist!');
        }
        return $user;
    }

    /**
     * Method to return all records from the User table
     *
     * @return Query
     */
    public function getAllUsersQuery(String $emailFilter = "") : Query
    {
        $builder = $this->createQueryBuilder('u');
        if ($emailFilter !== "") {
            $builder->where('u.email LIKE :email')
                ->setParameter('email', "%".$emailFilter."%");
        }
        return $builder->orderBy('u.id', 'ASC')
            ->getQuery();
    }

    /**
     * Method to save record of the User entity
     *
     * @param User $user User Group entity
     * @return User Updated User Group entity
     */
    public function save(User $user) : User
    {
        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    /**
     * UMethod to delete the User entity
     *
     * @param User  $user User entity
     * @return void
     */
    public function delete(User $user) : void
    {
        $this->em->remove($user);
        $this->em->flush();
    }
}
