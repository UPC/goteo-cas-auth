<?php

namespace Goteo\Controller\AuthController;

use Symfony\Component\HttpFoundation\Request;

use Goteo\Application\App;
use Goteo\Application\Session;
use Goteo\Application\Config;
use Goteo\Application\AppEvents;
use Goteo\Application\Event\FilterAuthEvent;
use Goteo\Application\Message;

use Goteo\Library\Text;

use Goteo\Model\User;

use phpCAS;

class CAS extends \Goteo\Controller\AuthController {
    private $cas_auth;

    private function casSignup($cas_user, Request $request) {
        $mail_domain = $this->cas_auth['mail_domain'];

        $user = new User();
        $user->userid = $cas_user;
        $user->email = "$cas_user@$mail_domain";
        $user->active = true;

        //si no existe nombre, nos lo inventamos a partir del userid
        if (trim($user->name) == '') {
            $user->name = ucwords(preg_replace("/[\.-]/", " ", $user->userid));
        }

        //no hará falta comprovar la contraseña ni el estado del usuario
        $skip_validations = array('password', 'active');

        //si el email proviene del CAS, podemos confiar en el y lo confirmamos por defecto
        $user->confirmed = 1;

        if ($u = User::getByEmail($user->email, null, true)) {
            if ($u->userid == $user->userid) {
                //login!
                Session::setUser($u, true);

                //Everything ok, redirecting
                return $this->dispatch(AppEvents::LOGIN_SUCCEEDED, new FilterAuthEvent($u))->getUserRedirect($request);
            } else {
                Message::error("E-mail $user->email for user $cas_user already registered to user $u->userid");
                $this->dispatch(AppEvents::SIGNUP_FAILED, new FilterAuthEvent($u));
                return $this->redirect('/login');
            }
        } elseif ($user->save($errors, $skip_validations)) {
            //si el usuario se ha creado correctamente, login en goteo e importacion de datos
            //y fuerza que pueda logear en caso de que no tenga contraseña o email sin confirmar

            //login!
            Session::setUser($user, true);

            //Everything ok, redirecting
            return $this->dispatch(AppEvents::SIGNUP_SUCCEEDED, new FilterAuthEvent($user))->getUserRedirect($request);
        }
        else {
            //si no: registrar errores
            Message::error("Saving of new user $cas_user failed!");
            $this->dispatch(AppEvents::SIGNUP_FAILED, new FilterAuthEvent($user));
            return $this->redirect('/login');
        }
    }

    public function __construct() {
        parent::__construct();

        $this->cas_auth = Config::get('plugins.cas-auth');

        phpCAS::setVerbose($this->cas_auth['verbose']);
        phpCAS::client(
            $this->cas_auth['version'],
            $this->cas_auth['hostname'],
            $this->cas_auth['port'],
            $this->cas_auth['uri']
        );

        if (empty($this->cas_auth['ca_cert'])) {
            phpCAS::setNoCasServerValidation();
        }
        else {
            phpCAS::setCasServerCACert($this->cas_auth['ca_cert']);
        }
    }

    public function casLoginAction(Request $request) {
        // Already logged?
        if (Session::isLogged()) {
            return App::dispatch(AppEvents::ALREADY_LOGGED, new FilterAuthEvent(Session::getUser()))->getUserRedirect($request);
        }

        if($request->query->has('return')) {
            Session::store('jumpto', $request->query->get('return'));
        }

        // check username/password
        phpCAS::forceAuthentication();
        $cas_user = phpCAS::getUser();

        if (!empty($cas_user)) {
            Message::info("CAS user is \"$cas_user\"");

            if ($user = User::get($cas_user)) {
                if (Session::setUser($user, true)) {
                    //Everything ok, redirecting
                    return App::dispatch(AppEvents::LOGIN_SUCCEEDED, new FilterAuthEvent($user))->getUserRedirect($request);
                }
            }

            return $this->casSignup($cas_user, $request);
        }

        // A subscriber will register a message
        App::dispatch(AppEvents::LOGIN_FAILED, new FilterAuthEvent(new User()));
        return $this->redirect('/');
    }

    public function casLogoutAction(Request $request) {
        if (phpCAS::checkAuthentication()) {
            phpCAS::logout();
        }
    }

    public function casSignupAction(Request $request) {
        Message::error("Trying to signup with CAS authentication enabled!");
        $this->dispatch(AppEvents::SIGNUP_FAILED, new FilterAuthEvent(new User()));
        return $this->redirect('/');
    }
}
