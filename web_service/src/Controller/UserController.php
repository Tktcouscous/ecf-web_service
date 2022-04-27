<?php

namespace App\Controller;

use PDOException;
use App\Model\User;
use App\Dao\UserDao;

class UserController
{
    public function index()
    {
        try {
            $userDao = new UserDao();
            $users = $userDao->getAll();

            for ($i = 0; $i < count($users); $i++) {
                $users[$i] = $users[$i]->toArray();
            }

            header("Content-Type: application/json");
            echo json_encode($users);
        } catch (PDOException $e) {
        }
    }

    public function show()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $userDao = new UserDao();
        $user = $userDao->getById($data['id_user']);

        if (!is_null($user)) {
            $user = $user->toArray();
        }

        header("Content-Type: application/json");
        echo json_encode($user);
    }


    public function edit()
    {

        $user_post = json_decode(file_get_contents('php://input'), true);



        if (
            isset($user_post['id_user'])
            && isset($user_post['pseudo'])
            && isset($user_post['email'])
            && isset($user_post['pwd'])
            && isset($user_post['new_pwd'])
            && isset($user_post['conf_new_pwd'])
        ) {

            if (empty(trim($user_post['pseudo']))) {
                $error_messages[] = "Pseudo inexistant";
            }

            if (empty(trim($user_post['email']))) {
                $error_messages[] = "Email inexistant";
            }

            if (empty(trim($user_post['pwd']))) {
                $error_messages[] = "Mot de passe inexistant";
            }

            if (empty(trim($user_post['new_pwd']))) {
                $error_messages[] = "Nouveau mot de passe inexistant";
            }

            if (empty(trim($user_post['conf_new_pwd']))) {
                $error_messages[] = "Confirmation de nouveau mot de passe inexistante";
            }

            $a = new UserDao;
            $b = $a->getById($user_post['id_user']);

            if (!isset($error_messages)) {
                if ($user_post['new_pwd'] === $user_post['conf_new_pwd']) {
                    $user = User::fromArray($user_post);
                    $user->setIdUser($user_post['id_user']);
                    $user->setPwd($user_post['new_pwd']);
                    
                    if ($b->verifyPwd($user_post['pwd'])) {
                        $userDao = new UserDao();
                        $userDao->edit($user);
                    }
                }
            }
        }
    }

    public function delete()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $userDao = new UserDao();
        $userDao->delete($data['id_user']);
    }
}
