<?php

namespace LibreNMS\Plugins;

class Esquema
{
    public static function menu()
    {
        echo '<li><a href="plugin/p=Esquema">Esquema</a></li>';
    }//end menu()

    public function device_overview_container($device) {
        $pid = $device['device_id'];
        echo ('<div class="container-fluid"><div class="row"> <div class="col-md-12"> <div class="panel panel-default panel-condensed"> <div class="panel-heading"><strong> Esquema (Padre y todos los hijos)</strong> </div>');
        $tabula='&nbsp;&nbsp;&nbsp;&nbsp;';
        $flecha='&#9493;&#9473;&#9473;&#9473;';
        $padre=dbFetchRows("select hostname,parent_device_id  from  device_relationships r inner join devices d on d.device_id=r.parent_device_id where child_device_id=$pid");
        if (!empty($padre))
            echo ("<p><a href='https://seti.upv.es/device/device={$padre[0]['parent_device_id']}/'>{$padre[0]['hostname']}</a></p>");
        else
            echo ("Sin padre");
        echo ("<p>$tabula$flecha<a href='https://seti.upv.es/device/device={$device['device_id']}/'>{$device['hostname']}</a></p>");
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
        $hijos=dbFetchRows("select hostname,child_device_id  from  device_relationships r inner join devices d on d.device_id=r.child_device_id where parent_device_id=$id");
        foreach ($arbol as $hoja) {
            echo ("<p>".str_repeat($tabula,1+$hoja['level']).$flecha."<a href='https://seti.upv.es/device/device={$hoja['child_device_id']}/'>{$hoja['hostname']}</a></p>");
            }
        echo('</div></div></div></div>');
    }

    public function port_container($device, $port) {
                echo('<div class="container-fluid"><div class="row"> <div class="col-md-12"> <div class="panel panel-default panel-condensed"> <div class="panel-heading"><strong>'.get_class().' plugin in "Port" tab</strong> </div>');
            echo ('Example display in Port tab</br>');
            echo('</div></div></div></div>');
    }
}
