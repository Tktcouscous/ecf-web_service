<?php

namespace App\Dao;

use PDO;
use Core\AbstractDao;
use App\Model\User;

class UserDao extends AbstractDao
{

    /**
     * Récupères de la base de données tous les users
     *
     * @return User[] Tableau d'objet User
     */
    public function getAll(): array
    {
        $sth = $this->dbh->prepare("SELECT id_user, pseudo, email, created_at FROM `user`");
        $sth->execute();
        $result = $sth->fetchAll(PDO::FETCH_ASSOC);

        for ($i = 0; $i < count($result); $i++) {
            $a = new User();
            $result[$i] = $a->setIdUser($result[$i]['id_user'])
                ->setPseudo($result[$i]['pseudo'])
                ->setEmail($result[$i]['email'])
                ->setCreatedAt($result[$i]['created_at']);
        }

        return $result;
    }
    
    /**
     * Récupère un utilisateur par son id si l'email existe dans la base de données,
     * sinon on récupèrera NULL
     *
     * @param int $id_user L'id de l'utilisateur
     * @return User|null Renvoi un utilisateur ou null
     */
    public function getById(int $id_user): ?User
    {
        $sth = $this->dbh->prepare('SELECT id_user, pseudo, email, created_at FROM user WHERE id_user = :id_user');
        $sth->execute([':id_user' => $id_user]);
        $result = $sth->fetch(PDO::FETCH_ASSOC);

        if (empty($result)) {
            return null;
        }

        $u = new User();
        return $u->setIdUser($result['id_user'])
            ->setPseudo($result['pseudo'])
            ->setEmail($result['email'])
            ->setCreatedAt($result['created_at']);
    }

    /**
     * Récupère un utilisateur par son id_user si l'id existe dans la base de données,
     * sinon on récupèrera NULL
     *
     * @param string $email L'email de l'utilisateur
     * @return User|null Renvoi un utilisateur ou null
     */
    public function getByEmail(string $email): ?User
    {
        $sth = $this->dbh->prepare('SELECT * FROM user WHERE email = :email');
        $sth->execute([':email' => $email]);
        $result = $sth->fetch(PDO::FETCH_ASSOC);

        if (empty($result)) {
            return null;
        }

        $u = new User();
        return $u->setIdUser($result['id_user'])
            ->setPseudo($result['pseudo'])
            ->setPwd($result['pwd'])
            ->setEmail($result['email'])
            ->setCreatedAt($result['created_at']);
    }

    /**
     * Ajoutes un user à la base de données et assigne l'id du user créé
     *
     * @param User $user Objet du user à ajouter à la bdd
     */
    public function new(User $user): void
    {
        $sth = $this->dbh->prepare(
            "INSERT INTO `user` (pseudo, email, pwd)
            VALUES (:pseudo, :email, :pwd)"
        );
        $sth->execute([
            ':pseudo' => $user->getPseudo(),
            ':email' => $user->getEmail(),
            ':pwd' => $user->getPwd()
        ]);
        $user->setIdUser($this->dbh->lastInsertId());
    }

    /**
     * Edites un user de la base de données
     *
     * @param User $user Objet du user à éditer
     */
    public function edit(User $user): void
    {
        $sth = $this->dbh->prepare(
            "UPDATE `user` SET pseudo = :pseudo, email = :email, pwd = :pwd WHERE id_user = :id_user"
        );
        $sth->execute([
            ':pseudo' => $user->getPseudo(),
            ':email' => $user->getEmail(),
            ':pwd' => $user ->getPwd(),
            ':id_user' => $user->getIdUser()
        ]);
    }
    
    public function delete(int $id_user): void
    {
        $sth = $this->dbh->prepare("DELETE FROM `user` WHERE id_user = :id_user");
        $sth->execute([":id_user" => $id_user]);
    }
}
