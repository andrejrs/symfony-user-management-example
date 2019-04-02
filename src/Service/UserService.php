<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * The User business logic 
 *
 * @category Controller
 * @package  SrcService
 * @author   Andrej <akicay@gmail.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/andrejrs
 */
final class UserService
{
    /**
     * Represents the User Group repository
     *
     * @var UserGroupRepository
     */
    protected $userRepository;

    /**
     *  Set manager for User entity
     *
     * @param UserRepository      $userRepository User methods to read entities from the DB
     * @param PaginatorInterface  $paginator      Knp paginator interface
     * @param ValidatorInterface  $validator      Validates PHP values against constraints.
     */
    public function __construct(
        UserRepository $userRepository,
        PaginatorInterface $paginator,
        UserPasswordEncoderInterface $passwordEncoder,
        ValidatorInterface $validator
    ) {
        $this->userRepository = $userRepository;
        $this->paginator = $paginator;
        $this->passwordEncoder = $passwordEncoder;
        $this->validator = $validator;
    }

    /**
     * Method to convert string to array
     * Example: 1,2,3 to [1,2,3]
     *
     * @param  String $string String that need to be converted to array
     * @return Array          Array of the converted string
     */
    private function _convertStringToArray(String $string): Array
    {
        return explode(",", $string);
    }

    /**
     * Get User entity by the user ID
     *
     * @param  integer $userId User identificatior
     * @return User   User entity object
     */
    public function getUser(int $userId): User
    {
        return $this->userRepository->findById($userId);
    }

    /**
     * Getting pagination of all users by page
     *
     * @param  Integer $page Page number
     * @return SlidingPagination Pagination of all users
     */
    public function getUsersByPage(Int $page = 1, String $emailFilter = ""): SlidingPagination
    {
        return $this->paginator->paginate(
            // Doctrine Query, not results
            $this->userRepository->getAllUsersQuery($emailFilter),
            // Define the page parameter
            $page,
            // Items per page
            5
        );
    }

    /**
     * Adding new user to database
     *
     * @param  string $email    Email of the user
     * @param  string $password Unencoded Password of the user
     * @param  string $roles    User roles as string, separated with ",". 
     *                          Example: ROLE_ADMIN,ROLE_USER
     * @return User
     */
    public function addUser(string $email, string $password, string $roles): User
    {
        // Convert string to array
        $roles = $this->_convertStringToArray($roles);

        // Validate roles
        User::validateRoles($roles);

        // Create a new user
        $user = new User();
        $user->setEmail($email);
        $user->setRoles($roles);
        $user->setPassword(
            $this->passwordEncoder->encodePassword(
                $user,
                $password
            )
        );

        // Calidate errors
        $errors = $this->validator->validate($user);
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                throw new \InvalidArgumentException($error->getMessage()." (".$error->getPropertyPath().")");
            }
        }
        // Insert new user to database
        $this->userRepository->save($user);
        return $user;
    }

    /**
     * Updateing user by the existing user object
     *
     * @param  User $user Usert entity object
     * @return User
     */
    public function updateUserByObject(User $user): User
    {
        // Update user to the database
        $this->userRepository->save($user);
        return $user;
    }

    /**
     * Updateing user by the existing user identificator
     *
     * @param  integer     $userId   User identificator
     * @param  string|null $Email    Email of the user
     * @param  string|null $Password Unencoded Password of the user
     * @param  string|null $Roles    User roles as string, separated with ",". 
     *                               Example: ROLE_ADMIN,ROLE_USER
     * @return User
     */
    public function updateUser(int $userId, ?string $Email, ?string $Password, ?string $Roles): User
    {
        // Concert roles to array
        $Roles = ($Roles!="") ? $this->_convertStringToArray($Roles) : "";

        // Validate roles
        User::validateRoles($roles);

        $user = $this->userRepository->findById($userId);
        if (!$user) {
            throw new EntityNotFoundException(
                'User with id '.$userId.' does not exist!'
            );
        }
        // Create array containing variables and their values
        $result = compact("Email", "Password", "Roles");
        
        // Iterate over field array
        foreach ($result as $name => $value) {
            if ($value == "") {
                continue;
            }
            $action = "set".$name;
            if (is_callable(array($user, $action))) {
                $user->$action($value);
            } else {
                throw new \BadMethodCallException("There is no $action method !");
            }
        }
        // Update user to the database
        $this->userRepository->save($user);
        return $user;
    }

    /**
     * Delete use from the database by indentificator
     *
     * @param  integer $userId User identificator
     * @return void
     */
    public function deleteUser(int $userId): void
    {
        // Find user by ID
        $user = $this->userRepository->findById($userId);
        if (!$user) {
            throw new EntityNotFoundException('User with id '.$userId.' does not exist!');
        }
        $this->userRepository->delete($user);
    }
}