<?php

namespace App\Form\Type;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * RolesType
 *
 * @category Type
 * @package  SrcFormType
 * @author   Andrej <akicay@gmail.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/andrejrs
 */
class RolesType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
            'choices' => User::ROLES,
            'expanded' => true,
            'multiple' => true,
            ]
        );
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}
