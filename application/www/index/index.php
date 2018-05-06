<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 2018/4/20
 * Time: 上午12:16
 */

echo "index";

$res=[];
$db  = new PDO("mysql:dbname=test;host=127.0.0.1", "root", "root");
foreach (range(1, 5) as $k => $v) {
    $res[] = $db->query("select * from user where id =$v")->fetchAll(\PDO::FETCH_ASSOC);
}
print_r($res);
echo 11;


