<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserType;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * User controller
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
 * @Route("/admin/user")
 */
class UserController extends AbstractController
{
    /**
     * Represents the User entity service
     *
     * @var UserService
     */
    private $_userService;

    /**
     * Set manager for User entity
     *
     * @param UserService $userService Manager for User entity
     */
    public function __construct(UserService $userService)
    {
        $this->_userService = $userService;
    }

    /**
     * The method for displaying all users with pagination
     *
     * @param  PaginatorInterface $paginator Knp paginator interface
     * @param  Request            $request Represents an HTTP request.
     * @return Response           Represents an HTTP response.
     * 
     * @Route("/", name="user_index", methods={"GET"})
     */
    public function index(PaginatorInterface $paginator, Request $request): Response
    {
        // Get page number
        $page = (int) $request->query->getInt('page', 1);
        $emailFilter = $request->query->get('email', "");

        // Get users by page number
        $users = $this->_userService->getUsersByPage($page, $emailFilter);

        // Render the twig view
        return $this->render(
            'admin/user/index.html.twig', [
                'users' => $users,
                'emailFilter' => $emailFilter
            ]
        );
    }

    /**
     * The method for creating an user
     *
     * @param  Request            $request    Represents an HTTP request.
     * @param  ValidatorInterface $validator  Validates PHP values against constraints.
     * @return Response           Represents an HTTP response.
     * 
     * @Route("/new", name="user_new", methods={"GET","POST"})
     */
    public function new(Request $request, ValidatorInterface $validator): Response
    {
        $errors = [];
        $user = new User();

        // Create the form
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        // Check is form submited and is it valid
        if ($form->isSubmitted()) {
            
            if ($form->isValid()) {

                $this->_userService->updateUserByObject($user);

                return $this->redirectToRoute('user_index');
            }
            $errors = $validator->validate($user);
        }

        // Render the twig view
        return $this->render(
            'admin/user/new.html.twig', [
                'user' => $user,
                'form' => $form->createView(),
                'errors' => $errors
            ]
        );
    }

    /**
     * The method for editing an user
     *
     * @param  Request            $request   Represents an HTTP request.  
     * @param  User               $user      Represents user entity object 
     * @param  ValidatorInterface $validator Validates PHP values against constraints.
     * @return Response           Represents an HTTP response.
     * 
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user, ValidatorInterface $validator): Response
    {
        $errors = [];

        // Create the form
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        // Check is form submited and is it valid
        if ($form->isSubmitted()) {
            
            if ($form->isValid()) {

                $this->getDoctrine()->getManager()->flush();

                return $this->redirectToRoute(
                    'user_index', [
                        'id' => $user->getId(),
                    ]
                );
            }
            $errors = $validator->validate($user);
        }

        // Render the twig view
        return $this->render(
            'admin/user/edit.html.twig', [
                'user' => $user,
                'form' => $form->createView(),
                'errors' => $errors
            ]
        );
    }

    /**
     * The method for deleting an user
     *
     * @param  Request $request Represents an HTTP request.  
     * @param  User $user       Represents user entity object 
     * @return Response         Represents an HTTP response.
     * 
     * @Route("/{id}", name="user_delete", methods={"DELETE"})
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $this->_userService->deleteUser($user->getId());
        }

        return $this->redirectToRoute('user_index');
    }
}
