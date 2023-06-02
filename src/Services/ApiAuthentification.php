<?php
    namespace App\Services;
    use App\Repository\UserRepository;
    use App\Services\Utils;
    use Firebase\JWT\JWT;
    use Firebase\JWT\Key;
    use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

    class ApiAuthentification {

        public function authentification (UserPasswordHasherInterface $userPasswordHasherInterface, UserRepository $userRepository, string $email, string $password):bool {

            //? Nettoyer les données issues de l'api
            $email = Utils::cleanInput($email);
            $password = Utils::cleanInput($password);

            //? Récupérer le user avec la méthode findOneBy() de la classe UserRepository
            $user = $userRepository->findOneBy(['email' => $email]);

            //? Teste si le user à été trouvé dans la BDD
            if ($user) {

                //? Tester si le password est correct
                if ($userPasswordHasherInterface->isPasswordValid($user, $password)) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }

        }

        public function genNewToken(string $email, string $secretKey, UserRepository $userRepository ):string {

            //? autolaod composer
            require_once('../vendor/autoload.php');

            //? Définition des variable nécessaires à la génération du token
            $issueDate  = new \DateTimeImmutable(); //Date de génération du token
            $expireDate = $issueDate->modify('+1 minutes')->getTimestamp(); //Date d'expiration du toke
            $serverName = "localhost";
            $userName   = $userRepository->findOneBy(['email' => $email])->getFirstNameUser().' '.$userRepository->findOneBy(['email' => $email])->getLastNameUser();

            //? Renseigner le contenu du token 
            $data = [
                'iat'       => $issueDate->getTimestamp(),         // Timestamp génération du token
                'iss'       => $serverName,                       // Serveur
                'nbf'       => $issueDate->getTimestamp(),         // Timestamp empécher date  (sécurité si quelqu'un récupère la clé de chiffrement)
                'exp'       => $expireDate,                           // Timestamp expiration du token
                'userName'  => $userName,
            ];

            //? Utilisation de la méthode statique encode() de la classe JWT pour encoder notre token
            $token = JWT::encode($data, $secretKey, 'HS512');

            //? Retourner le token
            return $token;

        }

        public function verifyToken (string $token, string $secretKey) {
            
            //? autolaod composer
            require_once('../vendor/autoload.php');

            try {
                //? On décode le token
                $decodeToken = JWT::decode($token, new Key($secretKey, 'HS512'));
                
                //? On return 'true' si aucune erreur n'a été retournée par la méthode decode()
                return true;

            } catch (\Throwable $error) {
                return $error->getMessage();
            }
        }
    }
?>