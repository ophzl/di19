<?php

namespace src\Controller;

class UserController extends AbstractController
{

    public function loginForm()
    {
        $token = bin2hex(random_bytes(32));
        $_SESSION['token'] = $token;
        return $this->twig->render('User/login.html.twig', [
            'token' => $token
        ]);
    }

    public function loginCheck()
    {

        if (!filter_var(
            $_POST['Pass'],
            FILTER_VALIDATE_REGEXP,
            array(
                "options" => array("regexp" => "/[a-zA-Z]{3,}/")
            )
        )) {
            $_SESSION['errorlogin'] = "Mot de passe trop court (3 caractères minimum)";
            header('Location:/Login');
            return;
        }

        if (!filter_var($_POST['Mail'], FILTER_VALIDATE_EMAIL)) {
            $_SESSION['errorlogin'] = "Mail invalide";
            header('Location:/Login');
            return;
        }

        if ($_POST["Mail"] == "admin@admin.com"
            AND $_POST["Pass"] == "password" AND $_POST['crsf'] == $_SESSION['token']
        ) {

            $_SESSION['USER'] = array(
                'Name' => 'Administrateur', 'roles' => array('admin', 'redacteur')
            );
            header('Location:/');
        } else {
            $_SESSION['errorlogin'] = "Erreur Authent.";
            header('Location:/Login');
        }


    }

    public static function roleNeed($roleATester)
    {
        if (isset($_SESSION['login'])) {
            if (!in_array($roleATester, $_SESSION['login']['roles'])) {
                $_SESSION['errorlogin'] = "Manque le role : " . $roleATester;
                header('Location:/Contact');
            }
        } else {
            $_SESSION['errorlogin'] = "Veuillez vous identifier";
            header('Location:/Login');
        }
    }

    public function logout()
    {
        unset($_SESSION['USER']);
        unset($_SESSION['errorlogin']);
        header('Location:/');
    }

}