<?php 
namespace App\DataFixtures;

use App\Entity\Trick;
use App\Entity\User;
use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class AppFixtures extends Fixture
{

  private $encoder;

  public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }
    public function load(ObjectManager $manager)
    {
        
        $user = new User();
        $user->setNickName('admin');

        $user->setEmail('admin@admin.com');

        $password = $this->encoder->encodePassword($user, 'pass_1234');
        $user->setPassword($password);

        $manager->persist($user);
        $manager->flush();

        for ($i=0; $i < 30 ; $i++) { 
          $trick = new Trick();
          $trick->setName("trick " . $i);
          $trick->setDescription("Le Lorem Ipsum est simplement du faux texte employé dans la composition et la mise en page avant impression. Le Lorem Ipsum est le faux texte standard de l'imprimerie depuis les années 1500, quand un imprimeur anonyme assembla ensemble des morceaux de texte pour réaliser un livre spécimen de polices de texte. Il n'a pas fait que survivre cinq siècles, mais s'est aussi adapté à la bureautique informatique, sans que son contenu n'en soit modifié. Il a été popularisé dans les années 1960 grâce à la vente de feuilles Letraset contenant des passages du Lorem Ipsum, et, plus récemment, par son inclusion dans des applications de mise en page de texte, comme Aldus PageMaker");
          $trick->setNiveau(mt_rand(1, 4));
          $trick->setTrickGroup(mt_rand(1, 3));
          $trick->addAttachement("072dfdbf755aaf6b4c993ddf54c0dbbf.jpeg");
          $manager->persist($trick);
        }
        $manager->flush();
    }
}