<?php

namespace App\Controller;

use App\Dao\UserDao;
use App\Model\User;

class SignupController
{
    public function index()
    {

        $user_post = json_decode(file_get_contents('php://input'), true);

        if (isset($user_post['pseudo']) && isset($user_post['email']) && isset($user_post['pwd']) && isset($user_post['conf_pwd'])) {
            if (empty(trim($user_post['pseudo']))) {
                $error_messages[] = "Pseudo inexistant";
            }
            if (empty(trim($user_post['email']))) {
                $error_messages[] = "Email inexistant";
            }
            if (empty(trim($user_post['pwd']))) {
                $error_messages[] = "Mot de passe inexistant";
            }
            if (empty(trim($user_post['conf_pwd']))) {
                $error_messages[] = "Confirmation de mot de passe inexistante";
            }

            if (!isset($error_messages)) {
                $userDao = new UserDao();
                $result= $userDao->getByEmail($user_post['email']);

                if (empty($result)){
                    $user= new User();
                    $user->setPseudo($user_post['pseudo'])
                    ->setEmail($user_post['email'])
                    ->setHashPwd($user_post['pwd']);
                    $userDao->new($user);

                    header("Content-Type: application/json");
                    echo json_encode([
                    'id_user' => $user->getIdUser()
                    ]);
                }
                else{
                    $error_messages[]= 'Email déjà utilisé';
                }

                header('Location: /user');//redirection non fonctionnelle
            }
        }
    }
}