<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Product;
use Liior\Faker\Prices;
use App\Entity\Category;
use App\Entity\Purchase;
use App\Entity\PurchaseItem;
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


        $users = []; //? Stocker les utilisateurs dans un tableau

        for($u = 0; $u < 5; $u++){
            $user = new User();

        $hash = $this->hasher->hashPassword($admin, "password");

            $user->setEmail("user$u@gmail.com")  //? Le $u c'est pour user1 , user2, user3 ... 
            ->setFullName($faker->name())
            ->setPassword($hash, "password");

            $users[] = $user; //? il y a 5 utilisateur stocker dans ce tableau

            $manager->persist($user);
        }

        $products = []; //? Stocker les produit dans un tableau

        for ($c = 0; $c < 3; $c++) {
            $category = new Category();
            $category->setName(ucwords($faker->sentence(1, 2)))
                // ->setSlug(strtolower($this->slugger->slug($category->getName())))
                ;

            $manager->persist($category);


            for ($p = 0; $p < mt_rand(15, 20); $p++) {
                $product = new Product();
                $product->setName(ucwords($faker->sentence(2, 3)))
                    ->setPrice($faker->price(4000, 20000))
                    // ->setSlug($faker->slug(2, 3))
                    // ->setSlug(strtolower($this->slugger->slug($product->getName())))
                    ->setCategory($category)
                    ->setShortDescription($faker->paragraph())
                    ->setMainPicture($faker->imageUrl(400, 400, true));

                $products[] = $product; //? dans le tableau des produits on ajoute un nouveau produit 
                    
                $manager->persist($product);
            }
        }

        for($p = 0; $p < mt_rand(20, 40); $p++){
            $purchase = new Purchase;

            $purchase->setFullName($faker->name)
                    ->setAddress($faker->streetAddress)
                    ->setPostalCode($faker->postcode)
                    ->setCity($faker->city)
                    ->setUser($faker->randomElement($users))
                    ->setTotal(mt_rand(2000, 30000))
                    ->setPurchasedAt($faker->dateTimeBetween('-6 months'));
                    
            $selectedProducts = $faker->randomElements($products, mt_rand(3, 5));

            //? pour chaque commande je veux 3 à 5 produit($selectedProducts)
            $totalSelectedProductsAmount = 0;
            foreach($selectedProducts as $product){
                $purchaseItem = new PurchaseItem;
                $purchaseItem->setProduct($product)
                    ->setQuantity(mt_rand(1, 3))
                    ->setProductName($product->getName())
                    ->setProductPrice($product->getPrice())
                    ->setTotal(
                        $purchaseItem->getProductPrice() * $purchaseItem->getQuantity()
                    )
                    ->setPurchase($purchase);

                $manager->persist($purchaseItem);
                
                $totalSelectedProductsAmount += $purchaseItem->getTotal();             
            }
            
            $purchase->setTotal($totalSelectedProductsAmount);

            
            if($faker->boolean(90)){
                $purchase->setStatus(Purchase::STATUS_PAID);
            }

            $manager->persist($purchase);
        }

        $manager->flush();
    }
}
