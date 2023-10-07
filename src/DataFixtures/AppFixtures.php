<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Product;
use Liior\Faker\Prices;
use App\Entity\Category;
use Bluemmb\Faker\PicsumPhotosProvider;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{
    protected $slugger;
    protected $hasher;

    public function __construct(SluggerInterface $slugger, UserPasswordHasherInterface $hasher)
    {
        $this->slugger = $slugger;
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $faker->addProvider(new Prices($faker));
        $faker->addProvider(new PicsumPhotosProvider($faker));
        
        

        $admin = new User;

        $hash = $this->hasher->hashPassword($admin, "password");
        
        $admin->setEmail("admin@gmail.com")
            ->setPassword($hash, "password")
            ->setFullName("Admin")
            ->setRoles(['ROLE_ADMIN']);

        $manager->persist($admin);

        for($u = 0; $u < 5; $u++){
            $user = new User();

        $hash = $this->hasher->hashPassword($admin, "password");

            $user->setEmail("user$u@gmail.com")  //? Le $u c'est pour user1 , user2, user3 ... 
            ->setFullName($faker->name())
            ->setPassword($hash, "password");

            $manager->persist($user);
        }

        for ($c = 0; $c < 3; $c++) {
            $category = new Category();
            $category->setName(ucwords($faker->sentence(1, 2)))
                ->setSlug(strtolower($this->slugger->slug($category->getName())));

            $manager->persist($category);


            for ($p = 0; $p < mt_rand(15, 20); $p++) {
                $product = new Product();
                $product->setName(ucwords($faker->sentence(2, 3)))
                    ->setPrice($faker->price(4000, 20000))
                    ->setSlug($faker->slug(2, 3))
                    ->setSlug(strtolower($this->slugger->slug($product->getName())))
                    ->setCategory($category)
                    ->setShortDescription($faker->paragraph())
                    ->setMainPicture($faker->imageUrl(400, 400, true));
                    

                $manager->persist($product);
            }
        }

        $manager->flush();
    }
}
