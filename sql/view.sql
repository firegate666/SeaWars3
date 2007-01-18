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