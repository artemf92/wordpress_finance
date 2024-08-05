SELECT 
    ufd.uid,
    ufd.mail,
    ufd.name,
    ufd.created,
    ufc.field_contributed_value,
    ufm.field_money_value,
    ufo.field_overdep_value,
    ufp.field_profit_value,
    ufr.field_refund_value,
    ufro.field_refund_over_value,
    ur.roles_target_id
FROM 
    users_field_data ufd
LEFT JOIN 
    user__field_contributed ufc ON ufd.uid = ufc.entity_id
LEFT JOIN 
    user__field_money ufm ON ufd.uid = ufm.entity_id
LEFT JOIN 
    user__field_overdep ufo ON ufd.uid = ufo.entity_id
LEFT JOIN 
    user__field_profit ufp ON ufd.uid = ufp.entity_id
LEFT JOIN 
    user__field_refund ufr ON ufd.uid = ufr.entity_id
LEFT JOIN 
    user__field_refund_over ufro ON ufd.uid = ufro.entity_id
LEFT JOIN 
    user__roles ur ON ufd.uid = ur.entity_id
WHERE 
    ufd.uid > 0;