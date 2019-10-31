# dependencies
Simple Map of dependencies


Works with dependecies of:
- Child Devices with only one parent
- Parent Devices with one or more children
- Deep first traversal


Notes: 
- Its preliminar version
- Its in Spanish


SQL recursive:
for Parent:
  select hostname,parent_device_id  from  device_relationships r inner join devices d on d.device_id=r.parent_device_id where child_device_id=$pid
for Children:
  with recursive children_cte (child_device_id, parent_device_id, path, level) as (
    select child_device_id, parent_device_id, CAST(child_device_id AS CHAR(500)) AS path, 1 as level
    from device_relationships
    where parent_device_id = $pid
    union all
    select  r.child_device_id,  r.parent_device_id, CONCAT(cc.path, ',', r.child_device_id), cc.level +1
    from device_relationships r
    inner join children_cte cc on r.parent_device_id = cc.child_device_id)
  select hostname, child_device_id, path, level  from children_cte cc
  inner join devices d on d.device_id = cc.child_device_id order by path

