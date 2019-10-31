<?php
$roots=array(1162,1165,1166);

$tab='&nbsp;&nbsp;&nbsp;&nbsp;';
$arrow='&#9493;&#9473;';

echo "<h1>Dependencies Map</h1>";
echo ("<table border=1><tr>");
foreach ($roots as $pid) {
    echo ("<td valign='top'>");
    $root=dbFetchRows("select hostname, device_id from devices where device_id=$pid");
    echo ("<p><a href='/device/device={$root[0]['device_id']}/'>{$root[0]['hostname']}</a></p>");
    $tree=dbFetchRows("with recursive children_cte (child_device_id, parent_device_id, path, level) as (
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
    foreach ($tree as $leaf) {
        if ($leaf['level']==1 && $n>400) {
            echo ("</td><td valign='top'>");
            echo ("<p><a href='/device/device={$root[0]['device_id']}/'>{$root[0]['hostname']}</a></p>");
            $n=0;
            }
        echo ("<p>".str_repeat($tab,$leaf['level']).$arrow."<a href='/device/device={$leaf['child_device_id']}/'>{$leaf['hostname']}</a></p>");
        $n++;
        }
    echo ("</td>");
    }

echo ("</tr></table>");
