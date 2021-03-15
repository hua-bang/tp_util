<?php


namespace app\login\controller;
use QL\QueryList;

class Water
{
    public function getBuilding(){
        $area=input("post.area");
        $campus=input("post.campus");
        $url="http://ehall.dgut.edu.cn/hq/home/Common/getBuildingList.hq";
        $ql=QueryList::getInstance()->post($url,[
            "area_id"=>$area,
            "campus_type"=>$campus
        ]);
        return json_decode($ql->getHtml(),true);
    }

    public function getRoomList(){
        $area=input("post.area");
        $campus=input("post.campus");
        $building=input("post.building");
        $url="http://ehall.dgut.edu.cn/hq/home/Common/getRoomList.hq";
        $ql=QueryList::getInstance()->post($url,[
            "area_id"=>$area,
            "campus_id"=>$campus,
            "building_id"=>$building,
            "type"=>1
        ]);
        return json_decode($ql->getHtml(),true);
    }

    public function getDistributionPre(){
        $area=input("post.area");
        $campus=input("post.campus");
        $building=input("post.building");
        $room=input("post.room");
        $token=input("post.token");
        $url="http://ehall.dgut.edu.cn/hq/home/Distribution/getDistributionPre.hq";
        $ql=QueryList::getInstance()->post($url,[
            "area_id"=>$area,
            "campus_id"=>$campus,
            "building_id"=>$building,
            "room_number"=>$room
        ],[
            'headers' => [
                'authorization' => $token
            ]
        ]);
        return json_decode($ql->getHtml(),true);
    }

    public function checkPay(){
        $price=input("post.price");
        $token=input("post.token");
        $url="http://ehall.dgut.edu.cn/hq/home/Pay/checkPay.hq";
        $ql=QueryList::getInstance()->post($url,[
            "total_price"=>$price
        ],[
            'headers' => [
                'authorization' => $token
            ]
        ]);
        return json_decode($ql->getHtml(),true);
    }

    public function addDistribution(){
        $area=input("post.area");
        $campus=input("post.campus");
        $building=input("post.building");
        $room=input("post.room");
        $barrel=input("post.barrel");
        $phone=input("post.phone");
        $send_num=input("send_num");
        $token=input("post.token");
        $url="http://ehall.dgut.edu.cn/hq/home/Distribution/addDistribution.hq";
        $ql=QueryList::getInstance()->post($url,[
            "area_id"=>$area,
            "campus_id"=>$campus,
            "building_id"=>$building,
            "room_number"=>$room,
            "barrel_id"=>$barrel,
            "phone"=>$phone,
            "send_num"=>$send_num
        ],[
            'headers' => [
                'authorization' => $token
            ]
        ]);
        return json_decode($ql->getHtml(),true);
    }
}