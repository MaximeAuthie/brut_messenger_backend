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
use App\Service\Utils;

//! Vérifier le projet SYmfony pour le use et la méthode à ajouter pour le hash du MDP

class UserApiController extends AbstractController {

    //! API pour renvoyer les données utilisateur (user profile) TRY CATCH????????
    #[ROUTE('api/user/{id}', name:"app_api_user_id", methods: 'GET')]
    public function getUserById(int $id, UserRepository $userRepository):Response {
        //? On recherche l'utilisateur par son id dans la BDD
        $user = $userRepository->find($id);

        //? On vérifie si $user est vide (s'il n'existe pas dans la BDD)
        if (empty($user)) {
            return $this->json(
                ['Erreur' => 'Cet utilisateur n\'existe pas dans la BDD'],
                206, 
                [], 
                []
            );
        }

        return $this->json(
            $user, 
            200, 
            ['Content-Type'=>'application/json','Access-Control-Allow-Origin' =>'localhost', 'Access-Control-Allow-Method' => 'GET'], //renvoie du json, uniquement depuis local host, et uniquelent sous forme de GET
            ['groups' => 'user:getUserById']
        );
    }

    //! API pour ajouter un utilisateur (inscription)
    #[ROUTE('api/user/add', name:"app_api_user_add", methods: 'POST')] //! Passer la classe Password pour le hash dans les param
    public function addUser(UserRepository $userRepository, Request $request, SerializerInterface $serializerInterface, EntityManagerInterface $entityManagerInterface):Response {
        try {
            //? Récupérer le contenu de la requête en provenance du front
            $json = $request->getContent();

            //? On vérifie que le body de la requête en provenance du front n'est pas vide
            if (!$json) {
                return $this->json(
                    ['Erreur' => 'Le json est vide ou n\'esiste pas.'],
                    400,
                    ['Content-Type'=>'application/json','Access-Control-Allow-Origin' =>'*', 'Access-Control-Allow-Method' => 'GET'], 
                    []
                );
            }

            //? On serialize le json pour le transformer en taleau
            $data = $serializerInterface->decode($json, 'json');

            //? On vérifie si la date est valide
            if (!Utils::isValidDate($data['birthday'])) {
                return $this->json(
                    ['Erreur' => 'L\a date '.$data['birthday'].' n\'est pas valide.'],
                    400,
                    ['Content-Type'=>'application/json','Access-Control-Allow-Origin' =>'localhost', 'Access-Control-Allow-Method' => 'GET'], 
                    [] );
            }

            //? On vérifie si le format de l'adresse mail est valide
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                return $this->json(
                    ['Erreur' => 'L\'adresse mail '.$data['email'].' n\'est pas valide.'],
                    400,
                    ['Content-Type'=>'application/json','Access-Control-Allow-Origin' =>'localhost', 'Access-Control-Allow-Method' => 'GET'], 
                    [] );
            }

            //? On vérifie si le user faisant l'objet de la demande existe déjà en BDD
            $recup = $userRepository->findOneBy(['email'=>$data['email']]);

            if ($recup) {
                //? On renvoie une erreur
                return $this->json(
                    ['Erreur' => 'L\'adresse mail '.$data['email'].' est déjà utilisée par un compte utilisateur.'],
                    206,
                    ['Content-Type'=>'application/json','Access-Control-Allow-Origin' =>'*', 'Access-Control-Allow-Method' => 'GET'], 
                    [] 
                );
            }

            //? On nettoie les données envoyées par l'API
            $firstName = Utils::cleanInput($data['firstName']);
            $lastName = Utils::cleanInput($data['lastName']);
            $birthday = Utils::cleanInput($data['birthday']);
            $email = Utils::cleanInput($data['email']);
            $password = Utils::cleanInput($data['password']);
            $nickname = Utils::cleanInput($data['firstName']).' '.Utils::cleanInput($data['lastName']);

            //? On chiffre le password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT); //! utiliseer hashPassword(objet user, variable MDP)

            //? On instancie un objet User et on 'set' toutes ses propriétés
            $user = new User;
            $user->setFirstNameUser($firstName);
            $user->setLastNameUser($lastName);
            $user->setBirthdayUser(new \DateTimeImmutable($birthday));
            $user->setEmail($email);
            $user->setPassword($hashedPassword);
            $user->setNicknameUser($nickname);
            $user->setRoles(['USER']);
            $user->setAvatarUrlUser('./public/asset/images/default-avatar.svg');
            $user->setStatusUser('true');
            $user->setFontSizeUser('medium');
            $user->setPublicKeyUser('1111111111');
            $user->setPrivateKeyUser('2222222222');

            //? On persiste et flush les données de l'instance $user pour l'insérer en BDD
            $entityManagerInterface->persist($user);
            $entityManagerInterface->flush();

            //? On retourne un json pour avertir que l'enregistrement a réussit
            return $this->json(
                ['erreur'=> 'Le compte '.$user->getEmail().' à bien été ajouté à la BDD.'],
                200, 
                ['Content-Type'=>'application/json','Access-Control-Allow-Origin' =>'*', 'Access-Control-Allow-Method' => 'GET'],
                []
            );

        } catch (\Exception $error) {

            //? On retourne un json pour détailler l'erreur rencontrée
            return $this->json(
                ['erreur'=> 'Etat du json : '.$error->getMessage()],
                400, 
                ['Content-Type'=>'application/json','Access-Control-Allow-Origin' =>'*', 'Access-Control-Allow-Method' => 'GET'],
                []
            );
        }
    }

    //! API pour récupérer les données utilisateurs à la demande de connexion
    #[ROUTE('api/user/logIn', name:"app_api_user_id", methods: 'GET')]
    public function logInUser(UserRepository $userRepository, Request $request, SerializerInterface $serializerInterface ):Response {
        //? On récupère le contenu de la requête en provenance du front
        $json = $request->getContent();

        //? On vérifie que le json n'est pas vide
        if (!$json) {
            return $this->json(
                ['Erreur' => 'Le json est vide ou n\'esiste pas.'],
                400,
                ['Content-Type'=>'application/json','Access-Control-Allow-Origin' =>'localhost', 'Access-Control-Allow-Method' => 'GET'], 
                []
            );
        }

        //? On serialize le json
        $data = $serializerInterface->decode($json,'json');

        //? On recherche le user existe dans la BDD via son mail
        $user = $userRepository->findOneBy(['email'=>$data['email']]);

        //? Si l'utilisateur n'existe pas, on renvoie une erreur
        if (!$user) {
            return $this->json(
                ['erreur'=> 'Adresse email ou mot de passe incorrect'],
                400, 
                ['Content-Type'=>'application/json','Access-Control-Allow-Origin' =>'localhost', 'Access-Control-Allow-Method' => 'GET'], //renvoie du json, uniquement depuis local host, et uniquelent sous forme de GET
                []
            );
        }

        //? Si l'utilisateur existe, on vérifie si sont mot de passe est correct
        $inputPassword = $data['password'];

        $databasePassword = $user->getPassword();

        if (!password_verify($inputPassword, $databasePassword)) {  //! ERREUR ICI
            return $this->json(
                ['erreur'=> 'Adresse email ou mot de passe incorrect'],
                400, 
                ['Content-Type'=>'application/json','Access-Control-Allow-Origin' =>'localhost', 'Access-Control-Allow-Method' => 'GET'], //renvoie du json, uniquement depuis local host, et uniquelent sous forme de GET
                []
            );
        }

        //? Si le mot de passe en correct, on retourne un json avec les données de l'utilisateur

        return $this->json(
            $user, 
            200, 
            ['Content-Type'=>'application/json','Access-Control-Allow-Origin' =>'localhost', 'Access-Control-Allow-Method' => 'GET'], //renvoie du json, uniquement depuis local host, et uniquelent sous forme de GET
            ['groups' => 'user:getUserById']);

    }
}
