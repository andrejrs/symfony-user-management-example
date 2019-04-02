<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\UserGroup;

/**
 * User Group Fixtures is used to load a "fake" set of data into a database that can then be used for testing purposes
 *
 * @category Fixture
 * @package  SrcDataFixtures
 * @author   Andrej <akicay@gmail.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/andrejrs
 */
class UserGroupFixtures extends Fixture
{
    /**
     * Create "fake" set of groups into a database that can then be used for testing purposes
     *
     * @param ObjectManager $manager Contract for a Doctrine persistence layer
     * @return void
     */
    public function load(ObjectManager $manager)
    {
        $groups = [
            "Chaos Computer Club",
            "Computer Measurement Group (CMG)",
            "ComputerTown UK",
            "Homebrew Computer Club",
            "Port7Alliance",
            "Java User Group",
            "Perl Mongers",
            "Z User Group",
            "Linux Australia",
            "Linux Users of Victoria",
            "LinuxChix",
            "Loco team",
            "NYLUG",
            "Portland Linux/Unix",
            "RLUG",
            "SEUL",
        ];

        foreach ($groups as $group) {
            $userGroup = new UserGroup();
            $userGroup->setName($group);

            $manager->persist($userGroup);
        }

        $manager->flush();
    }
}
