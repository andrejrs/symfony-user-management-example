<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User;
use Faker\Factory;
use Faker\Generator;

/**
 * User Fixtures is used to load a "fake" set of data into a database that can then be used for testing purposes
 *
 * @category Fixture
 * @package  SrcDataFixtures
 * @author   Andrej <akicay@gmail.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/andrejrs
 */
class UserFixtures extends Fixture
{
    /**
     * The PHP library that generates fake data
     *
     * @var Generator
     */
    private $_faker;

    /**
     * The interface for the password encoder service.
     *
     * @var UserPasswordEncoderInterface
     */
    private $_passwordEncoder;

    /**
     * Set password encoderager and faker for User entity
     *
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->_passwordEncoder = $passwordEncoder;
        $this->_faker = Factory::create();
    }

    /**
     * Create "fake" set of data into a database that can then be used for testing purposes
     *
     * @param ObjectManager $manager Contract for a Doctrine persistence layer
     * @return void
     */
    public function load(ObjectManager $manager)
    {
        // Create an test account
        $user = new User();
        $user->setEmail("test@nesto.com");
        $user->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
        $user->setPassword(
            $this->passwordEncoder->encodePassword(
                $user,
                'test'
            )
        );
        $manager->persist($user);

        // Create random 20 fake users
        for ($i = 1; $i <= 20; $i++) {
            $user = new User();
            $firstname = $this->faker->firstName;
            $lastname = $this->faker->lastName;
            $user->setEmail($firstname.'.'.$lastname.'@gmail.com');
            $user->setRoles(['ROLE_USER']);
            $user->setPassword(
                $this->passwordEncoder->encodePassword(
                    $user,
                    'test'
                )
            );
            $manager->persist($user);
        }

        $manager->flush();
    }
}
