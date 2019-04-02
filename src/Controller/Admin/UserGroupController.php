<?php

namespace App\Controller\Admin;

use App\Entity\UserGroup;
use App\Form\UserGroupType;
use App\Service\UserGroupService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * User group controller
 *
 * @category Controller
 * @package  SrcControllerAdmin
 * @author   Andrej <akicay@gmail.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/andrejrs
 * 
 * Require ROLE_ADMIN for *every* controller method in this class.
 * @IsGranted("ROLE_ADMIN")
 * 
 * @Route("/admin/user-group")
 */
class UserGroupController extends AbstractController
{
    /**
     * Represents the User Group entity service
     *
     * @var UserGroupService
     */
    private $_userGroupService;

    /**
     * Set manager for User Group entity
     *
     * @param UserGroupService $userService Manager for User entity
     */
    public function __construct(UserGroupService $userGroupService)
    {
        $this->_userGroupService = $userGroupService;
    }

    /**
     * The method for displaying all user groups with pagination
     *
     * @param  PaginatorInterface $paginator Knp paginator interface
     * @param  Request            $request Represents an HTTP request.
     * @return Response           Represents an HTTP response.
     * 
     * @Route("/", name="user_group_index", methods={"GET"})
     */
    public function index(PaginatorInterface $paginator, Request $request): Response
    {
        // Get page number
        $page = (int) $request->query->getInt('page', 1);

        // Get users by page number
        $userrGroups = $this->_userGroupService->getUserGroupsByPage($page);

        // Render the twig view
        return $this->render(
            'admin/user_group/index.html.twig', [
                'user_groups' => $userrGroups
            ]
        );
    }

    /**
     * The method for creating an user group
     *
     * @param  Request            $request    Represents an HTTP request.
     * @param  ValidatorInterface $validator  Validates PHP values against constraints.
     * @return Response           Represents an HTTP response.
     * 
     * @Route("/new", name="user_group_new", methods={"GET","POST"})
     */
    public function new(Request $request, ValidatorInterface $validator): Response
    {
        $errors = [];
        $userGroup = new UserGroup();

        // Create the form
        $form = $this->createForm(UserGroupType::class, $userGroup);
        $form->remove('users');
        $form->handleRequest($request);

        // Check is form submited and is it valid
        if ($form->isSubmitted()) {
            
            if ($form->isValid()) {

                $this->userGroupService->updateUserGroupByObject($userGroup);

                return $this->redirectToRoute('user_group_index');
            }
            $errors = $validator->validate($userGroup);
        }

        // Render the twig view
        return $this->render(
            'admin/user_group/new.html.twig', [
                'user_group' => $userGroup,
                'form' => $form->createView(),
                'errors' => $errors
            ]
        );
    }

    /**
     * The method for editing an user group
     *
     * @param  Request            $request   Represents an HTTP request.  
     * @param  UserGroup          $userGroup Represents user group entity object 
     * @param  ValidatorInterface $validator Validates PHP values against constraints.
     * @return Response           Represents an HTTP response.
     * 
     * @Route("/{id}/edit", name="user_group_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, UserGroup $userGroup, ValidatorInterface $validator): Response
    {
        $errors = [];

        // Create the form
        $form = $this->createForm(UserGroupType::class, $userGroup);
        $form->handleRequest($request);

        // Check is form submited and is it valid
        if ($form->isSubmitted()) {
            
            if ($form->isValid()) {

                $this->getDoctrine()->getManager()->flush();

                return $this->redirectToRoute(
                    'user_group_index', [
                        'id' => $userGroup->getId(),
                    ]
                );
            }
            $errors = $validator->validate($userGroup);
        }

        // Render the twig view
        return $this->render(
            'admin/user_group/edit.html.twig', [
                'user_group' => $userGroup,
                'form' => $form->createView(),
                'errors' => $errors
            ]
        );
    }

    /**
     * The method for deleting an user group
     *
     * @param  Request   $request   Represents an HTTP request.  
     * @param  UserGroup $userGroup Represents user entity object 
     * @return Response  Represents an HTTP response.
     * 
     * @Route("/{id}", name="user_group_delete", methods={"DELETE"})
     */
    public function delete(Request $request, UserGroup $userGroup): Response
    {
        if ($this->isCsrfTokenValid('delete'.$userGroup->getId(), $request->request->get('_token'))) {
            $this->_userGroupService->deleteUserGroup($userGroup->getId());
        }

        return $this->redirectToRoute('user_group_index');
    }
}
