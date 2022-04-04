<?php

use Phalcon\Mvc\Controller;
use Phalcon\Http\Request;
use Phalcon\Acl\Role;
use Phalcon\Acl\Adapter\Memory;
use Phalcon\Http\Response;
use Phalcon\Security\JWT\Builder;
use Phalcon\Security\JWT\Signer\Hmac;
use Phalcon\Security\JWT\Token\Parser;
use Phalcon\Security\JWT\Validator;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;




class AclController extends Controller
{
    public function indexAction()
    {

        // $eventsManager = $this->di->get('EventsManager');
        // $eventsManager->fire('notification:beforeHandleRequest');
        $dir    = APP_PATH . '/controllers';

        $files = scandir($dir, 1);
        $controllers = array();
        foreach ($files as $key => $value) {
            $explode  = explode('Controller', $value);
            // $controllers = array_diff($controllers, array('.', '..'));

            array_push($controllers, strtolower($explode[0]));
        }

        ///////////////////////////select controllers/////////////////////////
        $controller = $this->request->getpost();
        if ($controller['conroller'] == 'select') {

            // print_r($controller);
            // die();
            $controller = $controller['controller'];
        }


        $dir    = APP_PATH . '/views/' . $controller;
        echo $dir;
        // if ($controller) {
        //     die();
        // }
        $files = scandir($dir, 1);
        $actions = array();
        foreach ($files as $key => $value) {
            $explode  = explode('.phtml', $value);
            array_push($actions, $explode[0]);
        }
        $actions = array_diff($actions, array('.', '..'));

        $roles = Roles::find();


        // $acl = new Memory();

        // // $acl->addRole('manager');
        // // $acl->addRole('guest');
        // print_r($controllers);
        // print_r($actions);
        // print_r($roles[0]->role);
        // // die();
        // for ($i = 0; $i < count($controllers); $i++) {

        //     if ($controllers[$i] != '..' || $controllers[$i] != '.') {

        //         $acl->addComponent(
        //             $admin,
        //             [
        //                 $controllers[$i],
        //                 'users',
        //             ]
        //         );


        //         $acl->addComponent($controllers[$i]);
        //     }
        // }
        // for ($i = 0; $i < count($actions); $i++) {

        //     $acl->addR($actions[$i]);
        // }
        // for ($i = 0; $i < count($roles); $i++) {

        //     $acl->addRole($roles[$i]->role);
        // }

        $dir    = APP_PATH . '/message';
        echo $dir;
        // if ($controller) {
        //     die();
        // }
        $files = scandir($dir, 1);
        $language = array();
        foreach ($files as $key => $value) {
            $explode  = explode('.php', $value);
            array_push($language, $explode[0]);
        }
        $language = array_diff($language, array('.', '..'));

    

        $this->view->controllers = array("controller" => array_diff($controllers, array('.', '..')), "action" => $actions, "roles" => $roles, "selectedcontroller" => $controller,"language"=>$language);
    }
    public function addroleAction()
    {
        $request = new Request();

        if (true === $request->isPost()) {
            // $newrole = new Roles();
            // $rollarr = array(
            //     'role' => $request->getPost('roles'),
            // );
            // $newrole->assign(
            //     $rollarr,
            //     [
            //         'role'
            //     ]
            // );
            $roleobj = new Roles();
            $role = $request->getPost('roles');

            echo $role;

            // echo $newrole;
            $roleobj->role = $role;

            // die();
            $success = $roleobj->save();
            $this->view->success = $success;
            if ($success) {
                $this->view->msg = "<h6 class='alert alert-success w-75 container text-center'>Added Successfully</h6>";
            } else {
                $this->view->msg = "<h6 class='alert alert-danger w-75 container text-center'>Something went wrong</h6>";
            }
        }
    }



    public function addcomponentAction()
    {
        $request = new Request();
        $dir    = APP_PATH . '/controllers';
        $files = scandir($dir, 1);
        $controllers = array();
        foreach ($files as $key => $value) {
            $explode  = explode('Controller', $value);
            array_push($controllers, strtolower($explode[0]));
        }
        $this->view->controllers = array_diff($controllers, array('.', '..'));


        if (true === $request->isPost()) {
            $this->view->post = $request->getPost();
            $component = new Components();
            $component->assign(
                $request->getPost(),
                [
                    'component'
                ]
            );
            $success = $component->save();
            $this->view->success = $success;
            if ($success) {
                $this->view->msg = "<h6 class='alert alert-success w-75 container text-center'>Added Successfully</h6>";
            } else {
                $this->view->msg = "<h6 class='alert alert-danger w-75 container text-center'>Something went wrong</h6>";
            }
        }
    }

    public function allowAction()
    {
      

        $data = $this->request->getPost();

        if ($data['submit'] == 'save') {
            // echo "gfhgfhf";
            // die();
            // print_r($data);
            // die();



            $aclFile = APP_PATH . '/security/acl.cache';


            $secure = new SecureController();
            $acl = $secure->BuildaclAction();

            $acl->addComponent(
                $data['conroller'],
                [

                    $data['action'],

                ]
            );
            $acl->addRole($data['role']);
            $acl->allow($data['role'], $data['conroller'], $data['action']);

            file_put_contents(
                $aclFile,
                serialize($acl)
            );

            // print_r($acl->getComponents());
            // die();
        }

        ////////////////////////////////////////////deny permission////////////////////////////
        if ($data['submit'] == 'deny') {
            // echo "gfhgfhf";
            // die();
            // print_r($data);
            // die();



            $aclFile = APP_PATH . '/security/acl.cache';


            $secure = new SecureController();
            $acl = $secure->BuildaclAction();

            // $acl->addComponent(
            //     $data['conroller'],
            //     [

            //         $data['action'],

            //     ]
            // );
            // $acl->addRole($data['role']);
            $acl->deny($data['role'], $data['conroller'], $data['action']);

            file_put_contents(
                $aclFile,
                serialize($acl)
            );

            // print_r($acl->getComponents());
            // die();
        }

        // $response = new Response();
        $this->response->redirect('/acl/index?role='.$this->request->getQuery('role'));
    }
    public function adduserAction()
    {

        // echo "Action";
        // die();
        $roles = Roles::find();
        // print_r($role);
        // die();
        $this->view->message = $roles;

        $data = $this->request->getPost();

        if ($data['submit'] == 'add') {
            // echo "ryrtyrt";
            // die();

            $role = $data['role'];
            //  echo $role;
            //  die();



            $key = "example_key";
            $payload = array(
                "iss" => "/",
                "aud" => "/",
                "iat" => 1356999524,
                "nbf" => 1357000000,
                "name" => $data['name'],
                "password" => $data['password'],
                "role" => $role
            );
            $jwt = JWT::encode($payload, $key, 'HS256');

            // echo $jwt;
            // die();



            // Defaults to 'sha512'
            // $signer  = new Hmac();

            // // Builder object
            // $builder = new Builder($signer);

            // $now        = new DateTimeImmutable();
            // $issued     = $now->getTimestamp();
            // $notBefore  = $now->modify('-1 minute')->getTimestamp();
            // $expires    = $now->modify('+1 day')->getTimestamp();
            // $passphrase = 'QcMpZ&b&mo3TPsPk668J6QH8JA$&U&m2';

            // // Setup
            // $builder
            //     ->setAudience('/')  // aud
            //     ->setContentType('application/json')        // cty - header
            //     ->setExpirationTime($expires)               // exp 
            //     ->setId('abcd123456789')                    // JTI id 
            //     ->setIssuedAt($issued)                      // iat 
            //     ->setIssuer('/')           // iss 
            //     ->setNotBefore($notBefore)                  // nbf
            //     ->setSubject($role)   // sub
            //     ->setPassphrase($passphrase)                // password 
            // ;


            // // Phalcon\Security\JWT\Token\Token object
            // $tokenObject = $builder->getToken();

            $role = new Roles();
            $role->name = $data['name'];
            $role->password = $data['password'];
            $role->role = $data['role'];
            $role->token = $jwt;
            $role->save();

            // The token
            // echo $tokenObject->getToken();
            // die();
        }
    }

    public function loginAction()
    {
        // echo "Access";
        // die;
        $this->view->message ="hello";
    }
}
