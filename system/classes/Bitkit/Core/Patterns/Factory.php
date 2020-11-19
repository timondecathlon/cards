<?php
namespace Bitkit\Core\Patterns;
class Factory
{
    public static function createPost(int $id) : \Post
    {
        return new \Post($id);
    }

    public static function createBuilding(int $id) : \Building
    {
        return new \Building($id);
    }

    public static function createOffer(int $id) : \Offer
    {
        return new \Offer($id);
    }

    public static function createCompany(int $id) : \Company
    {
        return new \Company($id);
    }

    public static function createRequest(int $id) : \Request
    {
        return new \Request($id);
    }
}