<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use App\Services\Utils;
use App\Services\Messaging;

class ContactApiController extends AbstractController {

    //! API qui récupère les données du formulaire contact et les envoie par mail à l'admin
    #[Route('/api/contact', name: 'app_contact_api', methods: 'GET')]
    public function sendContactMail(Request $request, SerializerInterface $serializerInterface, Messaging $messaging):Response {

        try {

            //? Récupérer le contenu de la requête en provenance du front
            $json = $request->getContent();

            //? Vérifier que le json n'est pas vide
            if (!$json) {
                return $this->json(
                    ['Error' => 'The json is empty or does not exist.'],
                    400,
                    ['Content-Type'=>'application/json','Access-Control-Allow-Origin' =>'*', 'Access-Control-Allow-Method' => 'GET'], 
                    []
                );
            }

            //? Serializer le json 
            $data = $serializerInterface->decode($json, 'json');

            //? Nettoyer les données du json et les stocker dans des variables
            $firstName      = Utils::cleanInput($data['firstName']);
            $lastName       = Utils::cleanInput($data['lastName']);
            $email          = Utils::cleanInput($data['email']);
            $subject        = Utils::cleanInput($data['subject']);
            $content        = Utils::cleanInput($data['content']);
            
            //? Vérifier si le format de l'adresse mail est valide
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            
                return $this->json(
                    ['Error' => 'The email adress '.$data['email'].' is not a valid email adress.'],
                    422,
                    ['Content-Type'=>'application/json','Access-Control-Allow-Origin' =>'*', 'Access-Control-Allow-Method' => 'GET'], 
                    []
                );
            }

            //? Récupérer les variables d'authentification du webmail pour utiliser la méthode sendEmail() du service Messaging
            $mailLogin      = $this->getParameter('mailaccount');
            $mailPassword   = $this->getParameter('mailpassword');
            $mailContact    = $this->getParameter('mailcontact');

            //? Définition des variables pour utiliser la méthode sendEmail() de la classe Messenging
            $date   = date('d-m-y', time());
            $hour   = date('H:i:s', time());
            $mailObject     = mb_convert_encoding('BRUT MESSENGER : nouveau message de '.$firstName.' '.$lastName, 'ISO-8859-1', 'UTF-8');
            $mailContent    = mb_convert_encoding("<img src='https://i.postimg.cc/yNYjCGST/logo-long.jpg'/>".
                                                    "<p>Bonjour !</p>".
                                                    '<p>Vous avez reçu un nouveau message de <strong>'.$firstName.' '.$lastName.'</strong>, envoyé le <strong>'.$date.' à '.$hour.'</strong></br>'.
                                                    '<hr><p><strong><u>Sujet du message :</u></strong> '.$subject.' </p><hr>'.
                                                    '<p><strong><u>Contenu du message :</u></strong> </p>'.
                                                    $content.'<hr><br>'.
                                                    '<a href="mailto:'.$email.'?subject=BRUT MESSENGER : réponse à votre message du '.$date.' à '.$hour.'">Répondre à '.$firstName.' '.$lastName.'</a>'
                                                    , 'ISO-8859-1', 'UTF-8');

            //? Executer la méthode sendMail() de la classe Messenging
            $mailStatus = $messaging->sendEmail($mailLogin, $mailPassword, $mailContact, $mailObject, $mailContent, 'Administrateur', 'BRUT MESSENGER');

            //? Vérifier si l'envoi du mail à échoué
            if ($mailStatus != 'The mail has been sent') {
                if (!$json) {
                    return $this->json(
                        ['Error' => 'Unable to send mail'],
                        500,
                        ['Content-Type'=>'application/json','Access-Control-Allow-Origin' =>'*', 'Access-Control-Allow-Method' => 'GET'], 
                        []
                    );
                }
            }

            //? Retourner un json pour avertir que l'envoi du mail a fonctionné
            return $this->json(
                ['Success'=> 'The message has been sent '], 
                200, 
                ['Content-Type'=>'application/json','Access-Control-Allow-Origin' =>'*', 'Access-Control-Allow-Method' => 'GET'],
                ['groups' => 'user:getUserById']
            );

        //? En cas d'erreur inattendue, capter l'erreur rencontrée
        } catch (\Exception $error) {

            //? Retourner un json pour détailler l'erreur inattendu
            return $this->json(
                ['Error' => $error->getMessage()],
                400,
                ['Content-Type'=>'application/json','Access-Control-Allow-Origin' =>'*', 'Access-Control-Allow-Method' => 'GET'], 
                []
            );
        }
    }
}
