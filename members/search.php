<?php

include dirname(__DIR__, 3) . '/runtime.php';

$CSRFForm           = new PerchForm('csrf');
$token              = $CSRFForm->get_token();
// $token              = PerchSession::get('csrf_token');

$submitted_token    = PerchUtil::post('token');
$q                  = PerchUtil::post('q');


if(!$q || $submitted_token != $token) {
    echo json_encode([]);
    exit;
}

$API        = new PerchAPI(1.0, 'perch_members');
$Members    = new PerchMembers_Members($API);

$out = [];


$members = $Members->get_filtered_listing([
    'skip-template' => true,
    'template'      => 'members/member.html',
    'filter'        => 'memberEmail',
    'match'         => 'contains',
    'value'         => $q,
]);

// pipit_r($members);

if(PerchUtil::count($members)) {
    $members = $Members->return_instances($members);

    foreach($members as $Member) {
        $name = $Member->name();
        if(!$name) {
            $name = trim($Member->first_name() . ' ' . $Member->last_name());
        }

        
        $label = $name ? $name . ' - ' : '';
        $label .= $Member->memberEmail();

        $out[] = [
            'value' => $Member->id(),
            'label' => $label,
        ];
    }
}


echo json_encode($out);
// PerchUtil::output_debug();