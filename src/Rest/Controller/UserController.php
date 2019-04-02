<?php

namespace App\Rest\Controller;

use App\Service\UserService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Entity\User;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\FileParam;

/**
 * Require ROLE_ADMIN for *every* controller method in this class.
 *
 */
class UserController extends FOSRestController
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Retrieves an Article resource
     * @Rest\Get("/users/{articleId}")
     */
    public function getUserEntity(int $articleId): View
    {
        $user = $this->userService->getUser($articleId);

        // If success return a 200 HTTP OK response with the request object
        return View::create($user, Response::HTTP_OK);
    }

    /**
     * Retrieves a collection of Article resource
     * @Rest\Get("/users")
     */
    public function getUsers(Request $request): View
    {
        // Get users by page number
        $users = $this->userService->getUsersByPage(
            (int) $request->query->getInt('page', 1),
            $request->query->get('email', "")
        );

        return View::create($users, Response::HTTP_OK);
    }

    /**
     * Creates an Article resource
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

        // Todo: 400 response - Invalid Input
        // Todo: 404 response - Resource not found
        // In case our POST was a success we need to return a 201 HTTP CREATED response with the created object
        return View::create($user, Response::HTTP_CREATED);
    }

    /**
     * Replaces Article resource
     * @Rest\Put("/users/{articleId}")
     * @RequestParam(name="email", requirements="^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$", default="", description="E-mail.")
     * @RequestParam(name="password", requirements="[a-zA-Z0-9]+", default="", description="Password.")
     * @RequestParam(name="roles", requirements="[a-zA-Z0-9_,]+", default="", description="Roles.")
     */
    public function putUser(int $articleId, Request $request): View
    {
        $article = $this->userService->updateUser(
            $articleId,
            $request->get('email'),
            $request->get('password'),
            $request->get('roles')
        );
        // Todo: 400 response - Invalid Input
        // Todo: 404 response - Resource not found
        // In case our PUT was a success we need to return a 200 HTTP OK response with the object as a result of PUT
        return View::create($article, Response::HTTP_OK);
    }

    /**
     * Removes the Article resource
     * @Rest\Delete("/users/{articleId}")
     */
    public function deleteUser(int $articleId): View
    {
        $this->userService->deleteUser($articleId);
        // Todo: 404 response - Resource not found
        // In case our DELETE was a success we need to return a 204 HTTP NO CONTENT response. The object is deleted.
        return View::create([], Response::HTTP_NO_CONTENT);
    }
}
