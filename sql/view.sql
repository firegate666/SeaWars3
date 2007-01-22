CREATE OR REPLACE VIEW object_view AS
SELECT
	o.*,
	a.id as subid,
	a.name,
	a.value
FROM
	object o
LEFT JOIN attribute a ON a.objectid = o.id
ORDER BY
	o.id;
	
CREATE OR REPLACE VIEW relation_view AS
SELECT
	r.*,
	o.type as type
FROM
	relation r,
	object o
WHERE
	r.object2 = o.id
ORDER BY
	r.object1;