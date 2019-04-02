<?php

namespace App\Repository;

use App\Entity\UserGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;

/**
 * User Group methods to read entities from the DB
 *
 * @category Controller
 * @package  SrcRepository
 * @author   Andrej <akicay@gmail.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/andrejrs
 */
class UserGroupRepository extends ServiceEntityRepository
{
    /**
     * Represents EntityManager interface
     *
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * Set Registry and EntityManager for User Group entity
     *
     * @param RegistryInterface      $registry  References Doctrine connections and entity managers
     * @param EntityManagerInterface $em        EntityManager interface
     */
    public function __construct(
        RegistryInterface $registry, 
        EntityManagerInterface $em
    ) {
        parent::__construct($registry, UserGroup::class);
        $this->em = $em;
    }
 
    /**
     * Method to return User Group entity object by indentificator
     *
     * @param  Integer   $userGroupId User Group indentificator
     * @return UserGroup User Group entity object
     */
    public function findById(Int $userGroupId) : UserGroup
    {
        $qb = $this->createQueryBuilder('g')
            ->Where('g.id = :id')
            ->setParameter('id', $userGroupId)
            ->getQuery();

        $userGroup = $qb->execute();

        if (count($userGroup)===0) {
            throw new EntityNotFoundException('User group with id '.$userGroupId.' does not exist!');
        }

        return $userGroup[0];
    }

    /**
     * Method to return all records from the User Group table
     *
     * @return Query
     */
    public function getAlluserGroupsQuery() : Query
    {
        return $this->createQueryBuilder('u')
            ->orderBy('u.id', 'ASC')
            ->getQuery();
    }

    /**
     * Method to save record of the User Group entity
     *
     * @param UserGroup $userGroup User Group entity
     * @return UserGroup Updated User Group entity
     */
    public function save(UserGroup $userGroup) : UserGroup
    {
        $this->em->persist($userGroup);
        $this->em->flush();

        return $userGroup;
    }

    /**
     * UMethod to delete the User Group entity
     *
     * @param UserGroup $userGroup User Group entity
     * @return void
     */
    public function delete(UserGroup $userGroup) : void
    {
        $this->em->remove($userGroup);
        $this->em->flush();
    }
}
