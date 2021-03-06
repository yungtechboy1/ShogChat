<?php
namespace shogchat\database;

use shogchat\socket\Logger;

class Users{
    public static function createUser($name, $email, $password, $token, $repos = []){
        MongoConnector::getUserCollection()->insert([
            "_id" => $name,
            "email" => $email,
            "password" => md5($password), //TODO stronger hash
            "token" => $token,
            "registration" => time(),
            "lastactive" => time(),
            "repos" => $repos,
            "reposupdated" => @date("F j, Y, g:i a"),
            "sessions" => []
        ]);
    }
    public static function checkLogin($name, $password){
        $user = MongoConnector::getUserCollection()->findOne(["_id" => $name]);
        if($user != null){
            if($user["password"] == md5($password)){
                return true;
            }
            else{
                return false;
            }
        }
        else{
            return false;
        }
    }
    public static function userExists($name){
        return MongoConnector::getUserCollection()->findOne(["_id" => $name]) !== null;
    }
    public static function getUser($name){
        $user = MongoConnector::getUserCollection()->findOne(["_id" => $name]);
        return ($user == null ? false : $user);
    }
    public static function addSession($name, $ip){
        $user = Users::getUser($name);
        if($user !== false){
            $id = md5(time() . $_SERVER['REMOTE_ADDR']);
            $user["sessions"][$id] = [
                "ip" => $_SERVER['REMOTE_ADDR']
            ];
            MongoConnector::getUserCollection()->update(["_id" => $name], $user);
            return $id;
        }
        else{
            return false;
        }
    }
    public static function deleteSession($name, $id){
        $user = Users::getUser($name);
        if($user !== false){
            unset($user["sessions"][$id]);
            MongoConnector::getUserCollection()->update(["_id" => $name], $user);
        }
        else{
            return false;
        }
    }
    public static function changePassword($name, $newpass){
        return MongoConnector::getUserCollection()->update(["_id" => $name], [
            '$set' => [
                "password" => md5($newpass)
            ]
        ]);
    }
    public static function updateRepos($name, $repos){
        return MongoConnector::getUserCollection()->update(["_id" => $name], [
            '$set' => [
                "repos" => $repos,
                "reposupdated" => @date("F j, Y, g:i a")
            ]
        ]);
    }
    public static function isRepoOwner($user, $repo){
        foreach(Users::getUser($user)["repos"] as $testRepo){
            if($testRepo["name"] === $repo){
                return  true;
            }
        }
        return false;
    }
}