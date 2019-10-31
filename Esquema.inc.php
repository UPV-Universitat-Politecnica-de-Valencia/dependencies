<?php
$padres=array(1162,1165,1166);

$tabula='&nbsp;&nbsp;&nbsp;&nbsp;';
$flecha='&#9493;&#9473;';

echo "<h1>Esquema de dependencias</h1>";
//echo ("<table border=1><tr><td><h3>Vera</h3></td><td><h3>Alcoi</h3></td><td><h3>Gand&iacute;a</h3></td></tr><tr>");
echo ("<table border=1><tr>");
foreach ($padres as $pid) {
    echo ("<td valign='top'>");
    $padre=dbFetchRows("select hostname, device_id from devices where device_id=$pid");
    echo ("<p><a href='https://seti.upv.es/device/device={$padre[0]['device_id']}/'>{$padre[0]['hostname']}</a></p>");
    $arbol=dbFetchRows("with recursive children_cte (child_device_id, parent_device_id, path, level) as (
      select child_device_id, parent_device_id, CAST(child_device_id AS CHAR(500)) AS path, 1 as level
      from device_relationships
      where parent_device_id = $pid
      union all
      select  r.child_device_id,  r.parent_device_id, CONCAT(cc.path, ',', r.child_device_id), cc.level +1
      from device_relationships r
      inner join children_cte cc on r.parent_device_id = cc.child_device_id)
      select hostname, child_device_id, path, level  from children_cte cc
      inner join devices d on d.device_id = cc.child_device_id order by path");
    $n=0;
    foreach ($arbol as $hoja) {
        if ($hoja['level']==1 && $n>400) {
            echo ("</td><td valign='top'>");
            echo ("<p><a href='https://seti.upv.es/device/device={$padre[0]['device_id']}/'>{$padre[0]['hostname']}</a></p>");
            $n=0;
            }
        echo ("<p>".str_repeat($tabula,$hoja['level']).$flecha."<a href='https://seti.upv.es/device/device={$hoja['child_device_id']}/'>{$hoja['hostname']}</a></p>");
        $n++;
        }
    echo ("</td>");
    }

echo ("</tr></table>");
//echo "<hr><pre>";
//print_r($arbol);
//echo "</pre>";
