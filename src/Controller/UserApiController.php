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

class UserApiController extends AbstractController
{
    #[ROUTE('api/user/add', name:"app_api_user_add", methods: 'PUT')]
    public function addUser(UserRepository $articleRepository, Request $request, SerializerInterface $serializerInterface, EntityManagerInterface $entityManagerInterface):Response {
        try {
            //? Récupérer le contenu de la requête en provenance du front
            $json = $request->getContent();

            //? On vérifie que le body de la requête en provenance du front n'est pas vide
            if (!$json) {
                return $this->json(
                    ['Erreur' => 'Le json est vide ou n\'esiste pas.'],
                    400,
                    ['Content-Type'=>'application/json','Access-Control-Allow-Origin' =>'localhost', 'Access-Control-Allow-Method' => 'GET'], 
                    []
                );
            }

            //? On serialize le json pour le transformer en taleau
            $data = $serializerInterface->decode($json, 'json');

            //? On vérifie si le user faisant l'objet de la demande existe déjà en BDD
            $recup = $articleRepository->findOneBy(['email'=>$data['email']]);

            if ($recup) {
                //? On renvoie une erreur
                return $this->json(
                    ['Erreur' => 'L\'adresse mail '.$data['email'].' est déjà utilisée par un compte utilisateur.'],
                    206,
                    ['Content-Type'=>'application/json','Access-Control-Allow-Origin' =>'localhost', 'Access-Control-Allow-Method' => 'GET'], 
                    [] 
                );
            }

            //? On instancie un objet User et on 'set' toutes ses propriétés
            $user = new User;
            $user->setFirstNameUser($data['firstName']);
            $user->setLastNameUser($data['lastName']);
            $user->setBirthdayUser(new \DateTimeImmutable($data['birthday']));
            $user->setEmail($data['email']);
            $user->setPassword($data['password']);
            $user->setNicknameUser($data['firstName'].' '.$data['lastName']);
            $user->setRoles(['USER']);
            $user->setAvatarUrlUser('./public/asset/images/default-avatar.svg');
            $user->setStatusUser('true');
            $user->setFontSizeUser('medium');
            $user->setPublicKeyUser('1111111111');
            $user->setPrivateKeyUser('2222222222');

            //? On persiste et flush les données de l'instance $user
            $entityManagerInterface->persist($user);
            $entityManagerInterface->flush();

            //? On retourne un json pour avertir que l'enregistrement a réussit
            return $this->json(
                ['erreur'=> 'Le compte '.$user->getEmail().' à bien été ajouté à la BDD.'],
                200, 
                ['Content-Type'=>'application/json','Access-Control-Allow-Origin' =>'localhost', 'Access-Control-Allow-Method' => 'GET'],
                []
            );

        } catch (\Exception $error) {

            //? On retourne un json pour détailler l'erreur rencontrée
            return $this->json(
                ['erreur'=> 'Etat du json : '.$error->getMessage()],
                400, 
                ['Content-Type'=>'application/json','Access-Control-Allow-Origin' =>'localhost', 'Access-Control-Allow-Method' => 'GET'],
                []
            );
        }
    }
}
