<?php

namespace App\DataFixtures;

use Faker;
use Faker\Factory;
use App\Entity\User;
use App\Entity\Image;
use App\Entity\Annonce;
use App\Entity\Category;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;

class AppFixtures extends Fixture
{

    private $encoder;
    private $faker;
    private $params;

    public function __construct(UserPasswordEncoderInterface $encoder, ParameterBagInterface $params)
    {
        $this->encoder = $encoder;
        $this->params = $params;
    }

    public function load(ObjectManager $manager)
    {
            $this->faker = Faker\Factory::create('fr_FR');
            $this->addUsers($manager);
            $this->addCategories($manager);
            $this->addAnnonces($manager);
    }

    public function addUsers(EntityManager $manager)
    {
        // On ajoute un admin
        $admin = new User();
        $admin->setUsername('admin')
                ->setEmail('admin@exemple.com')
                ->setRoles(['ROLE_ADMIN'])
                ->setPassword($this->encoder->encodePassword($admin, 'admin'));
        $manager->persist($admin);

        // On ajoute un utilisateur
        $utilisateur = new User();
        $utilisateur->setUsername('user')
            ->setEmail('user@exemple.com')
            ->setRoles(['ROLE_USER'])
            ->setPassword($this->encoder->encodePassword($utilisateur, 'user'));
        $manager->persist($utilisateur);

        // On génère un nombre aléatoire d'utilisateurs
        for ($i = 1; $i < random_int(10,17); $i++) {
            $user = new User();
            $user->setUsername($this->faker->firstName())
                ->setEmail($this->faker->email())
                ->setRoles(['ROLE_USER'])
                ->setPassword($this->encoder->encodePassword($user, $this->faker->password($minLength = 6, $maxLength = 14)));
            $manager->persist($user);
        }
        $manager->flush();
    }

    public function addCategories(EntityManager $manager)
    {
        for ($i = 1; $i < random_int(7, 11); $i++) {
            $category = new Category();
            $category->setName($this->faker->word());
            $manager->persist($category);
        }
        $manager->flush();
    }

    public function addAnnonces(EntityManager $manager)
    {

        $categories = $manager->getRepository(Category::class)->findAll();
        $users = $manager->getRepository(User::class)->findAll();

        for ($i = 1; $i < random_int(60, 80); $i++) {

            $category = $categories[array_rand($categories, 1)];
            $user = $users[array_rand($users, 1)];

            $annonce = new Annonce();
            $annonce->setUser($user)
                ->setCategory($category)
                ->setTitle($this->faker->sentence())
                ->setContent($this->faker->paragraph(1));
                
                // // On génère entre 1 et 5 images par annonces
                // for ($j=0; $j < random_int(1,5); $j++) { 
                // // On récupère les images transmises
                //     $images[] = $this->faker->image(null, 360, 360, 'animals', true, true, 'cats', true)->getData();
                // }
                // dd($images);
                //     // On boucle sur les images
                //     foreach ($images as $image) {
                //         // On génère un nouveau nom de fichier
                //         $fichier = md5(uniqid()) . '.' . $image->guessExtension();

                //         // On copie le fichier dans le dossier uploads
                //         $image->move(
                //             $this->params->get('images_directory'),
                //             $fichier
                //         );
                //         // On crée l'image dans la base de données
                //         $img = new Image();
                //         $img->setName($fichier);
                //         $annonce->addImage($img);
                //     }
                //     $manager->persist($image);

            if ($this->faker->boolean($chanceOfGettingTrue = 50)) {
                // Si c'est vrais on insère un prix immédiat de vente ->
                $annonce->setPrice($this->faker->randomFloat(2, 10, 1000))
                        ->setCreatedAt(new \DateTime('now'));
                if ($this->faker->boolean($chanceOfGettingTrue = 50)) {
                    // Si c'est vrais on ajoute un prix de départ aux enchères et un prix de réserve -> Enchère mixte
                    // Si c'est faux -> Vente immédiate
                    $annonce->setPriceStartAuction($this->faker->randomFloat(2, 10, 1000))
                            ->setPriceAuctionPreserve($this->faker->randomFloat(2, 10, 1000))
                            ->setCreatedAt(new \DateTime('now'))
                            ->setEndAt($this->faker->dateTimeInInterval('now', '+7 days'));
                }
            } else {
                // Enchère mixte avec option d'achat immédiat
                $annonce->setPrice($this->faker->randomFloat(2,10, 10000))
                        ->setPriceStartAuction($this->faker->randomFloat(2, 10, 1000))
                        ->setPriceAuctionPreserve($this->faker->randomFloat(2, 10, 1000))
                        ->setCreatedAt(new \DateTime('now'))
                        ->setEndAt($this->faker->dateTimeInInterval('now', '+7 days'));
            }
            $manager->persist($annonce);
        }
        $manager->flush();
    }

}
