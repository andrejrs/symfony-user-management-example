<?php

namespace App\Rest\Controller;

use App\Service\UserService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Entity\User;
use App\Entity\UserGroup;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\FileParam;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;

/**
 * User REST API controller
 *
 * @category ApiController
 * @package  SrcRestController
 * @author   Andrej <akicay@gmail.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/andrejrs
 */
class UserController extends FOSRestController
{
    /**
     * Represents the User service
     *
     * @var UserService
     */
    private $userService;

    /**
     * Set manager for User service
     *
     * @param UserService $userService Manager for User entity
     */    
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Retrieves an User resource
     * 
     * @param  integer $userId User identity
     * @return View
     * 
     * @Rest\Get("/users/{userId}")
     */
    public function getUserEntity(int $userId): View
    {
        $user = $this->userService->getUser($userId);

        // If success return a 200 HTTP OK response with the request object
        return View::create(
            $this->userService->normalize($user),
            Response::HTTP_OK
        );
    }

    /**
     * Retrieves a collection of User resource
     * 
     * @param  Request $request Represents an HTTP request.
     * @return View
     *  
     * @Rest\Get("/users")
     */
    public function getUsers(Request $request): View
    {
        // Get users by page number
        $users = $this->userService->getUsersByPage(
            (int) $request->query->getInt('page', 1),
            $request->query->get('email', "")
        );

        return View::create(
            $this->userService->normalize($users),
            Response::HTTP_OK
        );
    }

    /**
     * Creates an User resource
     * 
     * @param  Request $request Represents an HTTP request.
     * @return View
     * 
     * @Rest\Post("/users")
     * @RequestParam(name="email", requirements="^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$", description="E-mail.")
     * @RequestParam(name="password", requirements="[a-zA-Z0-9]+", description="Password.")
     * @RequestParam(name="roles", requirements="[a-zA-Z0-9_,]+", description="Roles.")
     */
    public function addUser(Request $request): View
    {
        $user = $this->userService->addUser(
            $request->get('email'), 
            $request->get('password'),
            $request->get('roles')
        );

        // In case our POST was a success we need to return a 201 HTTP CREATED response with the created object
        return View::create($user, Response::HTTP_CREATED);
    }

    /**
     * Replaces User resource
     * 
     * @param  Integer $userId  Represents an User identificator.
     * @param  Request $request Represents an HTTP request.
     * @return View
     * 
     * @Rest\Put("/users/{userId}")
     * @RequestParam(name="email", requirements="^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$", default="", description="E-mail.")
     * @RequestParam(name="password", requirements="[a-zA-Z0-9]+", default="", description="Password.")
     * @RequestParam(name="roles", requirements="[a-zA-Z0-9_,]+", default="", description="Roles.")
     */
    public function putUser(int $userId, Request $request): View
    {
        $user = $this->userService->updateUser(
            $userId,
            $request->get('email'),
            $request->get('password'),
            $request->get('roles')
        );

        // In case our PUT was a success we need to return a 200 HTTP OK response with the object as a result of PUT
        return View::create($this->userService->normalize($user), Response::HTTP_OK);
    }

    /**
     * Removes the User resource
     * 
     * @param  Integer $userId  Represents an User identificator.
     * @return View
     * 
     * @Rest\Delete("/users/{userId}")
     */
    public function deleteUser(int $userId): View
    {
        $this->userService->deleteUser($userId);

        // In case our DELETE was a success we need to return a 204 HTTP NO CONTENT response. The object is deleted.
        return View::create([], Response::HTTP_NO_CONTENT);
    }

    /**
     * Adds an User to User Group resource
     * 
     * @param  User      $user      Represents an User resource.
     * @param  UserGroup $UserGroup Represents an User Group resource.
     * @return View
     * 
     * @Rest\Put("/users/{id}/add-group/{id_user_group}")
     * @Entity("UserGroup", expr="repository.find(id_user_group)")
     */
    public function addGroupUser(User $user, UserGroup $UserGroup): View
    {
        $user->addGroup($UserGroup);
        $user = $this->userService->updateUserByObject(
            $user
        );

        return View::create($this->userService->normalize($user), Response::HTTP_OK);
    }

    /**
     * Removes an User from User Group resource
     * 
     * @param  User      $user      Represents an User resource.
     * @param  UserGroup $UserGroup Represents an User Group resource.
     * @return View
     * 
     * @Rest\Put("/users/{id}/remove-group/{id_user_group}")
     * @Entity("UserGroup", expr="repository.find(id_user_group)")
     */
    public function removeGroupUser(User $user, UserGroup $UserGroup): View
    {
        $user->removeGroup($UserGroup);
        $user = $this->userService->updateUserByObject(
            $user
        );

        return View::create($this->userService->normalize($user), Response::HTTP_OK);
    }
}
