SELECT
    nd.nid,
    nd.vid,
    nd.title,
    nd.created,
    t.field_transaction_type_value,
    p.field_project_target_id,
    v.field_value_value,
    c.field_contributor_target_id,
    fe.field_for_entity_target_id
FROM
    node_field_data AS nd
LEFT JOIN
    node__field_transaction_type AS t ON nd.nid = t.entity_id
LEFT JOIN
    node__field_project AS p ON nd.nid = p.entity_id
LEFT JOIN
    node__field_value AS v ON nd.nid = v.entity_id
LEFT JOIN
    node__field_contributor AS c ON nd.nid = c.entity_id
LEFT JOIN
    node__field_for_entity AS fe ON nd.nid = fe.entity_id
WHERE
    nd.type = 'transaction'
AND
    v.bundle = 'transaction';



_______



SELECT
    nd.nid,
    nd.vid,
    nd.title,
    nd.created,
    t.field_transaction_type_value,
    p.field_project_target_id,
    v.field_value_value,
    c.field_contributor_target_id,
    fe.field_for_entity_target_id
FROM
    node_field_data AS nd
LEFT JOIN
    node__field_transaction_type AS t ON nd.nid = t.entity_id
LEFT JOIN
    node__field_project AS p ON nd.nid = p.entity_id
LEFT JOIN
    node__field_value AS v ON nd.nid = v.entity_id
LEFT JOIN
    node__field_contributor AS c ON nd.nid = c.entity_id
LEFT JOIN
    node__field_for_entity AS fe ON nd.nid = fe.entity_id
WHERE
    nd.type = 'transaction'
AND
    v.bundle = 'transaction'
AND
    nd.nid >= 87788;