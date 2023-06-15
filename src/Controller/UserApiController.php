<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use App\Entity\User;
use App\Services\Utils;
use App\Services\Messaging;
use App\Services\ApiAuthentification;
use App\Services\Encryption;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


//! Vérifier le projet SYmfony pour le use et la méthode à ajouter pour le hash du MDP

class UserApiController extends AbstractController {

    //! API pour renvoyer les données utilisateur (user profile)
    #[ROUTE('api/user/id', name:"app_api_user_get", methods: 'GET')]
    public function getUserById(Request $request, SerializerInterface $serializerInterface, UserRepository $userRepository, ApiAuthentification $apiAuthentification):Response {
        try {
            //? Récupérer le contenu de la requête en provenance du front
            $json = $request->getContent();

            //? On vérifie que le json n'est pas vide
            if (!$json) {
                return $this->json(
                    ['Error' => 'The json is empty or does not exist.'],
                    400,
                    ['Content-Type'=>'application/json','Access-Control-Allow-Origin' =>'localhost', 'Access-Control-Allow-Method' => 'GET'], 
                    []
                );
            }

            //? Serializer le json
            $data = $serializerInterface->decode($json,'json');

            //? Stocker les données du json dans des variables
            $id = $data['id'];
        
            //? Récupérer les variables à passer en paramètre de la méthode verifyToken() de ApiAuthentification
            $secretkey = $this->getParameter('token');
            $token = $request->server->get('HTTP_AUTHORIZATION');
            $token = str_replace('Bearer ', '', $token);

            //? Appeller la méthode verifyToken() de ApiAuthentification
            $checkToken = $apiAuthentification->verifyToken($token, $secretkey);
            
            //? Si la méthode verifyToken() retourne autre chose que true (une erreur)
            if ($checkToken !== true) {
                return $this->json(
                    ['Error' => $checkToken],
                    400, 
                    ['Content-Type'=>'application/json','Access-Control-Allow-Origin' =>'localhost', 'Access-Control-Allow-Method' => 'GET'], 
                    []
                );  
            }

            //? Rechercher l'utilisateur par son id dans la BDD
            $user = $userRepository->find($id);

            //? Vérifier si $user est vide (s'il n'existe pas dans la BDD)
            if (empty($user)) {
                return $this->json(
                    ['Error' => 'This user does not exist in the database.'],
                    206, 
                    ['Content-Type'=>'application/json','Access-Control-Allow-Origin' =>'localhost', 'Access-Control-Allow-Method' => 'GET'], 
                    []
                );
            }

            return $this->json(
                $user, 
                200, 
                ['Content-Type'=>'application/json','Access-Control-Allow-Origin' =>'localhost', 'Access-Control-Allow-Method' => 'GET'], //renvoie du json, uniquement depuis local host, et uniquelent sous forme de GET
                ['groups' => 'user:getUserById']
            );

        } catch (\Exception $error) {

            //? En cas d'erreur, on lève l'exception et on retourne le message d'erreur lié
            return $this->json(
                ['Error' => $error->getMessage()],
                400, 
                ['Content-Type'=>'application/json','Access-Control-Allow-Origin' =>'localhost', 'Access-Control-Allow-Method' => 'GET'], 
                []
            );
        }
    }

    //! API pour ajouter un utilisateur (inscription)
    #[ROUTE('api/user/add', name:"app_api_user_add", methods: 'POST')]
    public function addUser(UserRepository $userRepository, Request $request, SerializerInterface $serializerInterface, EntityManagerInterface $entityManagerInterface, Messaging $messaging, ApiAuthentification $apiAuthentification, Encryption $encryption, UserPasswordHasherInterface $userPasswordHasherInterface):Response {
        
        require_once('../vendor/autoload.php');
        
        try {
            //? Récupérer le contenu de la requête en provenance du front
            $json = $request->getContent();

            //? Vérifier que le body de la requête en provenance du front n'est pas vide
            if (!$json) {
                return $this->json(
                    ['Error' => 'The json is empty or does not exist.'],
                    400,
                    ['Content-Type'=>'application/json','Access-Control-Allow-Origin' =>'*', 'Access-Control-Allow-Method' => 'GET'], 
                    []
                );
            }

            //? Serializer le json pour le transformer en taleau
            $data = $serializerInterface->decode($json, 'json');

            //? Vérifier si la date est valide
            if (!Utils::isValidDate($data['birthday'])) {
                return $this->json(
                    ['Error' => 'The date '.$data['birthday'].' is not a valid date.'],
                    400,
                    ['Content-Type'=>'application/json','Access-Control-Allow-Origin' =>'*', 'Access-Control-Allow-Method' => 'GET'], 
                    [] );
            }

            //? Vérifier si le format de l'adresse mail est valide
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            
                return $this->json(
                    ['Error' => 'The email adress '.$data['email'].' is not a valid email adress.'],
                    422,
                    ['Content-Type'=>'application/json','Access-Control-Allow-Origin' =>'*', 'Access-Control-Allow-Method' => 'GET'], 
                    []
                );
            }

            //? Vérifier si le user faisant l'objet de la demande existe déjà en BDD
            $recup = $userRepository->findOneBy(['email'=>$data['email']]);

            if ($recup) {
                //? Renvoyer une erreur
                return $this->json(
                    ['Error' => 'The email adress '.$data['email'].' is already used by an other user account.'],
                    206,
                    ['Content-Type'=>'application/json','Access-Control-Allow-Origin' =>'*', 'Access-Control-Allow-Method' => 'GET'], 
                    [] 
                );
            }

            //? Nettoyer les données envoyées par l'API
            $firstName      = Utils::cleanInput($data['firstName']);
            $lastName       = Utils::cleanInput($data['lastName']);
            $birthday       = Utils::cleanInput($data['birthday']);
            $email          = Utils::cleanInput($data['email']);
            $password       = Utils::cleanInput($data['password']);
            $nickname       = Utils::cleanInput($data['firstName']).' '.Utils::cleanInput($data['lastName']);
            $publicKey      = Utils::cleanInput($data['publicKey']);
            $privateKey     = Utils::cleanInput($data['privateKey']);

            //? Récupérer la clé de chiffrement
            $encryptionKey = $this->getParameter('encryptionKey');
            
            //? Chiffrer le couple de clés avec la méthode encrypt() du service Encryption
            $encryptedPublicKey = $encryption->encrypt($publicKey, $encryptionKey);
            $encryptedPrivateKey = $encryption->encrypt($privateKey, $encryptionKey);

            //? Instancier un objet User et on 'setter' toutes ses propriétés
            $user = new User;
            $user->setFirstNameUser($firstName);
            $user->setLastNameUser($lastName);
            $user->setBirthdayUser(new \DateTimeImmutable($birthday));
            $user->setEmail($email);
            $user->setPassword($userPasswordHasherInterface->hashPassword($user, $password));
            $user->setNicknameUser($nickname);
            $user->setRoles(['ROLE_USER']);
            $user->setAvatarUrlUser('./public/asset/images/default-avatar.svg');
            $user->setStatusUser(false);
            $user->setFontSizeUser('medium');
            $user->setPublicKeyUser($encryptedPublicKey);
            $user->setPrivateKeyUser($encryptedPrivateKey);

            //? Persister et flush les données de l'instance $user pour l'insérer en BDD
            $entityManagerInterface->persist($user);
            $entityManagerInterface->flush();

            //? Récupérer les variables d'authentification du webmail
            $mailLogin      = $this->getParameter('mailaccount');
            $mailPassword   = $this->getParameter('mailpassword');

            //? Récupérer la clé secrète pour générer un token avec la méthode genNewToken() du service ApiAuthentification
            $secretkey      = $this->getParameter('token');
            $token          = $apiAuthentification->genNewToken($user->getEmail(), $secretkey, $userRepository, 10);

            //? Définition des variables pour utiliser la méthode sendEmail() de la classe Messenging
            $mailObject     = 'Activation de votre compte BRUT MESSENGER';
            $mailContent    = mb_convert_encoding("<img src='https://i.postimg.cc/yNYjCGST/logo-long.jpg'/>".
                                                  "<p>Bienvenue dans la communauté BRUT MESSENGER ".$user->getFirstNameUser()." ! </p>".
                                                  "<p>Pour activer ton compte et commencer à utiliser l'application BRUT MESSENGER sur ton mobile, cliques sur le lien ci-dessous:</p>".
                                                  '<a href = "https://127.0.0.1:8000/api/user/activate/'.$user->getId().'/'.$token.'">Lien d\'activation</a>', 'ISO-8859-1', 'UTF-8');
            

            //? Executer la méthode sendMail() de la classe Messenging
            $mailStatus = $messaging->sendEmail($mailLogin, $mailPassword, $user->getEmail(), $mailObject, $mailContent);
            
            //? Vérifier si l'envoi du mail s'est bien passé
            if ($mailStatus != 'The mail has been sent') {
                return $this->json(
                    ['Error' => 'Unable to send mail'],
                    500,
                    ['Content-Type'=>'application/json','Access-Control-Allow-Origin' =>'*', 'Access-Control-Allow-Method' => 'GET'], 
                    []
                );
            }

            //? Retourner un json pour avertir que l'enregistrement a réussit
            return $this->json(
                ['Success'=> 'The account '.$user->getEmail().' has been added to the database.'],
                200, 
                ['Content-Type'=>'application/json','Access-Control-Allow-Origin' =>'*', 'Access-Control-Allow-Method' => 'GET'],
                []
            );

        //? En cas d'erreur inattendue, capter l'erreur rencontrée
        } catch (\Exception $error) {

            //? Retourner un json pour détailler l'erreur inattendue
            return $this->json(
                ['Error'=> 'Json state : '.$error->getMessage()],
                400, 
                ['Content-Type'=>'application/json','Access-Control-Allow-Origin' =>'*', 'Access-Control-Allow-Method' => 'GET'],
                []

            );
        }
    }

    //! API pour activer le compte utilisateur quand il clique sur le lien dans le mail d'activation
    #[ROUTE('api/user/activate/{id}/{token}', name:"app_api_user_activate", methods: 'GET')]
    public function activateUser(int $id, string $token, UserRepository $userRepository, EntityManagerInterface $entityManagerInterface, ApiAuthentification $apiAuthentification, Messaging $messaging):Response {

        try {

            //? Nettoyer les données
            $id = Utils::cleanInput($id);
            $token = Utils::cleanInput($token);

            //? Vérifier si l'utilisateur existe
            $user = $userRepository->find($id);

            if (!$user) {
                return $this->json(
                    ['Error' => 'This user does not exist in the database.'],
                    400,
                    ['Content-Type'=>'application/json','Access-Control-Allow-Origin' =>'*', 'Access-Control-Allow-Method' => 'GET'], 
                    []
                );
            }

            //? Vérifier si l'utilisateur n'est pas déjà activé
            if ($user->isStatusUser()) {
                //? Rediriger vers le front en passant le code '206' en paramètre (Partial Content)
                return $this->redirect('http://localhost:8080/account-activation/206');
            }

            //? Récupérer la secret key pour vérifier la validité du token avec la méthode verifyToken() du service ApiAuthentification
            $secretkey = $this->getParameter('token');

            //? Appeller la méthode verifyToken() de ApiAuthentification
            $checkToken = $apiAuthentification->verifyToken($token, $secretkey);
       
            //? On gère le cas du token valide mais expiré pour renvoyer un mail
            if ($checkToken === "Expired token") {

                //? Récupérer les variables d'authentification du webmail
                $mailLogin      = $this->getParameter('mailaccount');
                $mailPassword   = $this->getParameter('mailpassword');

                //? Récupérer la clé secrète pour générer un token avec la méthode $genNewToken du service ApiAuthentification
                $secretkey      = $this->getParameter('token');
                $newToken       = $apiAuthentification->genNewToken($user->getEmail(), $secretkey, $userRepository, 10);

                //? Définition des variables pour utiliser la méthode sendEmail() de la classe Messenging
                $mailObject     = 'Activation de votre compte BRUT MESSENGER';
                $mailContent    = mb_convert_encoding("<img src='https://i.postimg.cc/yNYjCGST/logo-long.jpg'/>".
                                                      "<p>Bienvenue dans la communauté BRUT MESSENGER ".$user->getFirstNameUser()." ! </p>".
                                                      "<p>Pour activer ton compte et commencer à utiliser l'application BRUT MESSENGER sur ton mobile, cliques sur le lien ci-dessous:</p>".
                                                      '<a href = "https://127.0.0.1:8000/api/user/activate/'.$user->getId().'/'.$newToken.'">Lien d\'activation</a>', 'ISO-8859-1', 'UTF-8');

                //? Executer la méthode sendMail() de la classe Messenging
                $mailStatus = $messaging->sendEmail($mailLogin, $mailPassword, $user->getEmail(), $mailObject, $mailContent);
                
                //? Vérifier si l'envoi du mail s'est bien passé
                if ($mailStatus != 'The mail has been sent') {
                    return $this->redirect('http://localhost:8080/account-activation/500');
                }

                //? Rediriger vers le front en passant le code '419' en paramètre (Expired Token)
                return $this->redirect('http://localhost:8080/account-activation/419');
            }

            //? Si la méthode verifyToken() retourne autre chose que true (une erreur)
            if ($checkToken !== true) {

                //? Rediriger vers le front en passant le code '498' en paramètre (Invalid Token)
                return $this->redirect('http://localhost:8080/account-activation/498');  
            }

            //? Setter le statut de l'utilisateur à true
            $user->setStatusUser(true);

            //? Persister et flush les données
            $entityManagerInterface->persist($user);
            $entityManagerInterface->flush();

            //? Rediriger vers le front en passant le code '200' en paramètre (OK)
            return $this->redirect('http://localhost:8080/account-activation/200');
        
        //? En cas d'erreur inattendue, capterl'erreur rencontrée
        } catch (\Exception $error) {

            //? Rediriger vers le front en passant le code '400' en paramètre (bad request)
            return $this->redirect('http://localhost:8080/account-activation/400');
        }
    }

    //! API pour récupérer les données utilisateurs à la demande de connexion
    #[ROUTE('api/user/logIn', name:"app_api_user_login", methods: 'GET')]
    public function logInUser(ApiAuthentification $apiAuthentification, UserPasswordHasherInterface $userPasswordHasherInterface,  Request $request, SerializerInterface $serializerInterface, UserRepository $userRepository, Messaging $messaging ):Response {
        
        try {

            //? Récupérer le contenu de la requête en provenance du front
            $json = $request->getContent();

            //? On vérifie que le json n'est pas vide
            if (!$json) {
                return $this->json(
                    ['Error' => 'The json is empty or does not exist.'],
                    400,
                    ['Content-Type'=>'application/json','Access-Control-Allow-Origin' =>'*', 'Access-Control-Allow-Method' => 'GET'], 
                    []
                );
            }

            //? Serializer le json
            $data = $serializerInterface->decode($json,'json');

            //? Stocker les données du json dans des variables
            $email      = $data['email'];
            $password   = $data['password'];

            //? Récupérer la clé de chiffrement
            $secretkey = $this->getParameter('token');

            //? Vérifier si les données du json ne sont pas vides
            if (empty($email) OR empty($password)) {
                return $this->json(
                    ['Error' => 'At least one data is empty'],
                    400,
                    ['Content-Type'=>'application/json','Access-Control-Allow-Origin' =>'*', 'Access-Control-Allow-Method' => 'GET'], 
                    []
                );
            }

            //? Vérifier si le format de l'adresse mail est valide
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return $this->json(
                    ['Error' => 'Invalid e-mail address format'],
                    400,
                    ['Content-Type'=>'application/json','Access-Control-Allow-Origin' =>'*', 'Access-Control-Allow-Method' => 'GET'], 
                    []
                );
            }
            
            //? Appeller la méthode d'authentification du service ApiAuthentification pour vérifier si on peut connecter l'utilisateur
            if ($apiAuthentification->authentification($userPasswordHasherInterface ,$userRepository, $email, $password )) {

                //? Récupérer les données de l'utilisateur dans une instance $user
                $user = $userRepository->findOneBy(['email'=>$email]);

                //? Vérifier si le compte est activé
                if (!$user->isStatusUser()) {
                    return $this->json(
                        ['Error' => 'The account is not activated'],
                        400,
                        ['Content-Type'=>'application/json','Access-Control-Allow-Origin' =>'*', 'Access-Control-Allow-Method' => 'GET'], 
                        []
                    );
                }

                //? Récupérer la clé secrète pour générer un token avec la méthode genNewToken() du service ApiAuthentification
                $secretkey      = $this->getParameter('token');
                $token          = $apiAuthentification->genNewToken($email, $secretkey, $userRepository, 3);
                
                //? Récupérer les variables d'authentification du webmail
                $mailLogin      = $this->getParameter('mailaccount');
                $mailPassword   = $this->getParameter('mailpassword');

                $hour           = date( "H:i:s", time());
            
                //? Définition des variables pour utiliser la méthode sendEmail() de la classe Messenging
                $mailObject     = mb_convert_encoding('BRUT MESSENGER : authentification à double facteur', 'ISO-8859-1', 'UTF-8');
                $mailContent    = mb_convert_encoding("<img src='https://i.postimg.cc/yNYjCGST/logo-long.jpg'/>".
                                                      "<p>Bonjour ".$user->getFirstNameUser()." ! </p>".
                                                      "<p>Tu as essayé de te connecter à BRUT MESSENGER à ".$hour.". Pour confirmer ton identité et accéder à ton application, cliques sur le lien suivant : </br>".
                                                      '<a href = "https://127.0.0.1:8000/api/user/activate/'.$user->getId().'/'.$token.'">Lien d\'activation</a>', 'ISO-8859-1', 'UTF-8');
                
                //? Executer la méthode sendMail() de la classe Messenging
                $mailStatus = $messaging->sendEmail($mailLogin, $mailPassword, $user->getEmail(), $mailObject, $mailContent);
                
                // dd($mailStatus, $user->getEmail());
                //? Vérifier si l'envoi du mail s'est bien passé
                if ($mailStatus != 'The mail has been sent') {
                    return $this->json(
                        ['Error' => 'Unable to send mail'],
                        500,
                        ['Content-Type'=>'application/json','Access-Control-Allow-Origin' =>'*', 'Access-Control-Allow-Method' => 'GET'], 
                        []
                    );
                }
                
                //? Retourner un json pour avertir que la première étape de la connexion a réussie
                return $this->json(
                    ['Success'=> 'A connexion confirmation email was send to '.$user->getEmail()],
                    200, 
                    ['Content-Type'=>'application/json','Access-Control-Allow-Origin' =>'*', 'Access-Control-Allow-Method' => 'GET'],
                    []
                );
                
            } else {
                return $this->json(
                    ['Error' => 'Connexion denied : wrong email or password'],
                    401,
                    ['Content-Type'=>'application/json','Access-Control-Allow-Origin' =>'*', 'Access-Control-Allow-Method' => 'GET'], 
                    []
                );
            }

        //? En cas d'erreur inattendue, capter l'erreur rencontrée
        } catch (\Exception $error){

            //? Retourner un json pour détailler l'erreur inattendu
            return $this->json(
                ['Error' =>$error->getMessage()],
                400,
                ['Content-Type'=>'application/json','Access-Control-Allow-Origin' =>'*', 'Access-Control-Allow-Method' => 'GET'], 
                []
            );
        }
    }
}
