<?php

namespace App\Rest\Controller;

use App\Service\UserGroupService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\FileParam;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

/**
 * Require ROLE_ADMIN for *every* controller method in this class.
 *
 */
class UserGroupController extends FOSRestController
{
    private $userGroupService;

    public function __construct(UserGroupService $userGroupService)
    {
        $this->userGroupService = $userGroupService;
    }

    /**
     * Retrieves an User Group resource
     * @Rest\Get("/user-groups/{userGroupId}")
     */
    public function getUserGroup(int $userGroupId): View
    {
        $userGroup = $this->userGroupService->getUserGroup($userGroupId);

        // If success return a 200 HTTP OK response with the request object
        return View::create($userGroup, Response::HTTP_OK);
    }

    /**
     * Retrieves a collection of User Group resource
     * @Rest\Get("/user-groups")
     */
    public function getUserGroups(Request $request): View
    {
        // Get users by page number
        $users = $this->userGroupService->getUserGroupsByPage(
            (int) $request->query->getInt('page', 1)
        );
        
        return View::create($users, Response::HTTP_OK);
    }

    /**
     * Creates an User Group resource
     *
     * @Rest\Post("/user-groups")
     * @RequestParam(name="name", requirements="[a-zA-Z0-9]+", description="Password.")
     */
    public function addUserGroup(Request $request): View
    {
        $user = $this->userGroupService->addUserGroup(
            $request->get('name')
        );

        // Todo: 400 response - Invalid Input
        // Todo: 404 response - Resource not found
        // In case our POST was a success we need to return a 201 HTTP CREATED response with the created object
        return View::create($user, Response::HTTP_CREATED);
    }

    /**
     * Replaces User Group resource
     * @Rest\Put("/user-groups/{userGroupId}")
     * @RequestParam(name="name", requirements="[a-zA-Z0-9]+", description="E-mail.")
     */
    public function putUserGroup(int $userGroupId, Request $request): View
    {
        $userGroup = $this->userGroupService->updateUserGroup(
            $userGroupId,
            $request->get('name')
        );
        // Todo: 400 response - Invalid Input
        // Todo: 404 response - Resource not found
        // In case our PUT was a success we need to return a 200 HTTP OK response with the object as a result of PUT
        return View::create($userGroup, Response::HTTP_OK);
    }

    /**
     * Removes the User Group resource
     * @Rest\Delete("/user-groups/{userGroupId}")
     */
    public function deleteUserGroup(int $userGroupId): View
    {
        $this->userGroupService->deleteUserGroup($userGroupId);
        // Todo: 404 response - Resource not found
        // In case our DELETE was a success we need to return a 204 HTTP NO CONTENT response. The object is deleted.
        return View::create([], Response::HTTP_NO_CONTENT);
    }
}
