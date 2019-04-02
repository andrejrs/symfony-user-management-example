<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Index controller
 *
 * @category Controller
 * @package  SrcController
 * @author   Andrej <akicay@gmail.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/andrejrs
 */
class IndexController extends AbstractController
{
    /**
     * The default page.
     *
     * @Route("/")
     */
    public function indexAction()
    {
        return $this->render('index/homepage.html.twig');
    }
}
