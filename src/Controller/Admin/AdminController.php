<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Admin root controller
 *
 * @category Controller
 * @package  SrcControllerAdmin
 * @author   Andrej <akicay@gmail.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/andrejrs
 * 
 * Require ROLE_ADMIN for *every* controller method in this class.
 * @IsGranted("ROLE_ADMIN")
 */
class AdminController extends AbstractController
{

    /**
     * Dashboard action method
     *
     * @return void
     */
    public function dashboard()
    {
        // If the user is authenticated
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // Get User object, or null if the user is not authenticated
        $user = $this->getUser();

        return $this->render(
            'admin/dashboard.html.twig',
            [
                'username' => $user->getUsername()
            ]
        );
    }
}
