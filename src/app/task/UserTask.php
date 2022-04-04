<?php

namespace app\task;

use Firebase\JWT\JWT;

use Phalcon\Cli\Task;
use Phalcon\Logger;
// use Phalcon\Logger\Adapter\File as FileAdapter;

use Settings;
use Products;
use Orders;

class UserTask extends Task
{
    public function mainAction()
    {
        echo 'This is the default task and the default action' . PHP_EOL;
    }

    public function getTokenAction($role = 'admin')
    {

        // echo $role.PHP_EOL; 

        //  echo $role;
        //  die();



        $key = "example_key";
        $payload = array(
            "iss" => "/",
            "aud" => "/",
            "iat" => 1356999524,
            "nbf" => 1357000000,

            "role" => $role
        );
        $jwt = JWT::encode($payload, $key, 'HS256');

        echo $jwt . PHP_EOL;
    }


    public function setdefaultAction($price = null, $zip = null, $stock = null)
    {

        // $obj=new Settings();
        $objsetting = Settings::findFirst(1);
        // print_r($objsetting->Default_Price);
        $price =  $price != null ? $price : $objsetting->Default_Price;
        $zip =  $zip != null ? $zip : $objsetting->default_zip;
        $stock =  $stock != null ? $stock : $objsetting->default_stock;
        // $price=$objsetting['Default_Price'] ;
        // echo $price . PHP_EOL;
        $objsetting->Default_Price = $price;
        $objsetting->default_zip = $zip;
        $objsetting->default_stock = $stock;
        $success = $objsetting->save();
        echo $success . PHP_EOL;
        if ($success) {
            echo "set success" . PHP_EOL;
        } else {
            echo "not set";
        }
    }
    public function getproductAction()
    {

        // $obj=new Settings();
        $products = Products::find(
            [
                'conditions'  => 'stock > 10',


            ]
        );
        echo count($products) . PHP_EOL;
    }

    // public function removelogAction()
    // {


    //     $logger = new FileAdapter("app/log/db.log");

    //     $logger->log(
    //         "Th"
    //     );
    // }

    public function neworderAction()
    {


        $products = Orders::findFirst(
            [
                'order' => 'order_id DESC',
            ]
        );
        echo "product name => " . $products->product . PHP_EOL;
    }

    public function deleteaclAction()
    {

        $aclFile = APP_PATH . '/security/acl.cache';
        // $aclFile->delete();
        // echo $aclFile;
        unlink($aclFile);
        echo "acl file deleted";
    }
}
