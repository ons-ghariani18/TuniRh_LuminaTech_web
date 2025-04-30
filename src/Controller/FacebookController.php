<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
//use League\OAuth2\Client\Provider\Github;
use League\OAuth2\Client\Provider\Facebook;
use App\Entity\Users;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class FacebookController extends AbstractController
{
    private $provider;

    //private $github_provider;
 
 
    public function __construct()
    {
       $this->provider=new Facebook([
         'clientId'          => $_ENV['FCB_ID'],
         'clientSecret'      => $_ENV['FCB_SECRET'],
         'redirectUri'       => $_ENV['FCB_CALLBACK'],
         'graphApiVersion'   => 'v15.0',
     ]);
 
 
    /* $this->github_provider=new Github([
         'clientId'          => $_ENV['GITHUB_ID'],
         'clientSecret'      => $_ENV['GITHUB_SECRET'],
         'redirectUri'       => $_ENV['GITHUB_CALLBACK'],
     ]);*/
 
 
    }
 
     #[Route('/', name: 'app_home')]
     public function index(): Response
     {
         return $this->render('baseFront.html.twig');
     }
 
     #[Route('/fcb-login', name: 'fcb_login')]
     public function fcbLogin(): Response
     {
          
         $helper_url=$this->provider->getAuthorizationUrl();
 
         return $this->redirect($helper_url);
     }
 
 
     #[Route('/fcb-callback', name: 'fcb_callback')]
     public function fcbCallBack(UsersRepository $userDb, EntityManagerInterface $manager,SessionInterface $session): Response
     {
        //Récupérer le token
        $token = $this->provider->getAccessToken('authorization_code', [
         'code' => $_GET['code']
         ]);
 
        try {
            //Récupérer les informations de l'utilisateur
 
            $user=$this->provider->getResourceOwner($token);
 
            $user=$user->toArray();
 
            $firstName = $user['first_name'] ?? '';
            $lastName = $user['last_name'] ?? '';
            $email = $user['email'] ?? '';
 
           // $picture=array($user['picture_url']);
 
            //Vérifier si l'utilisateur existe dans la base des données
 
            $user_exist=$userDb->findOneByEmail($email);
 
            if($user_exist)
            {
                
                
                 $user_exist->setFirstname($firstName)
                            ->setFirstname($firstName);

                          //->setPictureUrl($picture);
 
                 $manager->flush();
                 $session->set('facebook_authenticated', true);

 
                 return $this->redirectToRoute('app_template_Connected');


 
            }
 
            else
            {
                 $new_user=new Users();
 
                 $new_user->setNom($firstName)
                       ->setPrenom($lastName)
                       ->setEmail($email)
                       ->setRole("candidat")
                       ->setPassword(sha1(str_shuffle('abscdop123390hHHH;:::OOOI')));
               
                 $manager->persist($new_user);
 
                 $manager->flush();
                 $session->set('facebook_authenticated', true);

 
                 return $this->redirectToRoute('app_template_Connected');

 
 
            }
 
 
        } catch (\Throwable $th) {
         //throw $th;
 
         return new Response(
            'error'
         );
    }
        
 
 
     }
 
 
     
 
 
      
}