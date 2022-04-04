<?php

namespace App\Listeners;

use Phalcon\Di\Injectable;
use Phalcon\Events\Event;
use Phalcon\Security\JWT\Token\Parser;
use Phalcon\Security\JWT\Validator;
use Phalcon\Security\JWT\Signer\Hmac;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;


class NotificationListeners extends Injectable
{

    public function afterSend(Event $event, $as)
    {
        //     $logger = $this->di->get('logger');
        //     $logger->info('After notification');
        die('logger');
    }
    public function afterAdd(Event $event, $product, $setting)
    {
        // echo $product->price;



        if ($product->price == '') {
            // echo "56";
            // die();
            $logger = $this->di->get('logger');
            $logger->info('default price set');
            $product->price = $setting->Default_Price;
        }
        if ($product->stock == '') {
            $logger = $this->di->get('logger');
            $logger->info('default stock set');
            $product->stock = $setting->default_stock;
            // echo "4564";
            // die();
        }
        if ($setting->title_optimization == 'N') {
        }

        if ($setting->title_optimization == 'Y') {

            $product->name = $product->name + $product->tag;
        }

        return $product;
    }
    public function afterorderAdd(Event $event, $order, $setting)
    {
        // echo $product->price;



        if ($order->Zipcode == '') {
            // echo "56";
            // die();
            $logger = $this->di->get('logger');
            $logger->info('default price set');
            $order->Zipcode = $setting->default_zip;
        }


        return $order;
    }

    public function beforeHandleRequest(Event $event, \Phalcon\Mvc\Application $application)
    {

        // echo "fire";
        // die();

        $aclFile = APP_PATH . '/security/acl.cache';
        if (true === is_readable($aclFile)) {

            $acl = unserialize(
                file_get_contents($aclFile)
            );

            $role = $this->request->getQuery('role');

            // echo $role;
            // die();
            if ($role == '') {
                echo $this->locale->_("access denied");
                die;
            }

            $key = "example_key";
            try {
                $decoded = JWT::decode($role, new Key($key, 'HS256'));
            } catch (\Exception $e) {

                echo $this->locale->_("access denied");
                die;
            }

            // $decoded = JWT::decode($role, new Key($key, 'HS256'));


            $decoded_array = (array) $decoded;
            // print_r($decoded_array);
            // die();
            $role = $decoded_array['role'];



            // if ($role != 'admin') {
            //     echo $this->locale->_("access denied");
            //     die;
            // }


            if (!$role || true !== $acl->isAllowed($role, $this->router->getControllerName() ?? "acl", $this->router->getActionName() ?? "index")) {

                echo $this->locale->_("access denied");
                die;
            }
        } else {
            // echo "donr find acl";
            // die();
            // $response = new response();

            $this->response->redirect('secure/buildacl?redirect=1');
        }
    }
}
