<?php

namespace App\Service;

use App\Entity\UserGroup;
use App\Repository\UserGroupRepository;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * The User Group business logic 
 *
 * @category Controller
 * @package  SrcService
 * @author   Andrej <akicay@gmail.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/andrejrs
 */
final class UserGroupService
{
    /**
     * Represents the User Group repository
     *
     * @var UserGroupRepository
     */
    protected $userGroupRepository;

    /**
     *  Set manager for User entity
     *
     * @param UserGroupRepository $userGroupRepository User Group methods to read entities from the DB
     * @param PaginatorInterface  $paginator           Knp paginator interface
     * @param ValidatorInterface  $validator           Validates PHP values against constraints.
     */
    public function __construct(
        UserGroupRepository $userGroupRepository,
        PaginatorInterface $paginator,
        ValidatorInterface $validator
    ) {
        $this->userGroupRepository = $userGroupRepository;
        $this->paginator = $paginator;
        $this->validator = $validator;
    }

    /**
     * The method for getting User Group by identificator
     *
     * @param integer $userGroupId User Group by identificator
     * @return UserGroup           User Group entity object
     */
    public function getUserGroup(int $userGroupId): UserGroup
    {
        return $this->userGroupRepository->findById($userGroupId);
    }

    /**
     * The method for getting All User Groups with pagination
     *
     * @param integer $page      Page number
     * @return SlidingPagination Sliding Pagination object
     */
    public function getUserGroupsByPage($page = 1): SlidingPagination
    {
        return $this->paginator->paginate(
            // Doctrine Query, not results
            $this->userGroupRepository->getAlluserGroupsQuery(),
            // Define the page parameter
            $page,
            // Items per page
            5
        );
    }

    /**
     * The method for adding a new User Group
     *
     * @param string $name User group name
     * @return UserGroup
     */
    public function addUserGroup(string $name): UserGroup
    {
        // Create a new user group
        $userGroup = new UserGroup();
        $userGroup->setName($name);

        $errors = $this->validator->validate($userGroup);
        if (count($errors) > 0) {
            
            $errorsString = (string) $errors;
            $errorsString = "";
            foreach ($errors as $violation) {
                throw new \InvalidArgumentException($error->getMessage()." (".$error->getPropertyPath().")");
            }
        }

        $this->userGroupRepository->save($userGroup);
        return $userGroup;
    }

    /**
     * The method for update the User Group by entity object
     *
     * @param UserGroup $userGroup User Group entity object
     * @return UserGroup
     */
    public function updateUserGroupByObject(UserGroup $userGroup): UserGroup
    {
        $this->userGroupRepository->save($userGroup);
        return $userGroup;
    }

    /**
     * The method for update the User Group by indentificator
     *
     * @param integer $userGroupId User Group indentificator
     * @param string|null $Name User Group name
     * @return UserGroup
     */
    public function updateUserGroup(int $userGroupId, ?string $Name): UserGroup
    {
        $userGroup = $this->userGroupRepository->findById($userGroupId);
        if (!$userGroup) {
            throw new EntityNotFoundException('User with id '.$userGroupId.' does not exist!');
        }
        $result = compact("Name");
        
        foreach ($result as $name => $value) {
            if ($value == "") {
                continue;
            }
            $action = "set".$name;
            if (is_callable(array($userGroup, $action))) {
                $userGroup->$action($value);
            } else {
                die("Ne postoji action $action !");
            }
        }

        $this->userGroupRepository->save($userGroup);
        return $userGroup;
    }

    /**
     * The method for delete an User Group by indentificator
     *
     * @param integer $userGroupId User Group indentificator
     * @return void
     */
    public function deleteUserGroup(int $userGroupId): void
    {
        $userGroup = $this->userGroupRepository->findById($userGroupId);
        $this->userGroupRepository->delete($userGroup);
    }
}
