<?php

namespace LibreNMS\Plugins;

class DependenciesMap
{
    public static function menu()
    {
        echo '<li><a href="plugin/p=DependenciesMap">DependenciesMap</a></li>';
    }//end menu()

    public function device_overview_container($device) {
        $pid = $device['device_id'];
        echo ('<div class="container-fluid"><div class="row"> <div class="col-md-12"> <div class="panel panel-default panel-condensed"> <div class="panel-heading"><strong> Dependecies  (One parent and All childs)</strong> </div>');
        $tab='&nbsp;&nbsp;&nbsp;&nbsp;';
        $arrow='&#9493;&#9473;&#9473;&#9473;';
        $root=dbFetchRows("select hostname,parent_device_id  from  device_relationships r inner join devices d on d.device_id=r.parent_device_id where child_device_id=$pid");
        if (!empty($root))
            echo ("<p><a href='/device/device={$root[0]['parent_device_id']}/'>{$root[0]['hostname']}</a></p>");
        else
            echo ("No Device");
        echo ("<p>$tab$arrow<a href='/device/device={$device['device_id']}/'>{$device['hostname']}</a></p>");
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
        $childs=dbFetchRows("select hostname,child_device_id from device_relationships r inner join devices d on d.device_id=r.child_device_id where parent_device_id=$id");
        foreach ($tree as $leaf) {
            echo ("<p>".str_repeat($tab,1+$leaf['level']).$arrow."<a href='/device/device={$leaf['child_device_id']}/'>{$leaf['hostname']}</a></p>");
            }
        echo('</div></div></div></div>');
    }

    public function port_container($device, $port) {
    ;
    }
}

