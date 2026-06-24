<?php

$courses = json_decode(file_get_contents(__DIR__ . '/unique_courses.json'), true);

function getTuitionFeeIdFromCourse(string $course): ?int
{
    $c = str_replace(' ', '', strtolower(trim($course)));
    
    if (strpos($c, 'combopre_toeic+toeiclv(1+2)+toeic(s+w)') !== false) return 50;
    if (strpos($c, 'combopre_toeic+toeiclv(1+2)+ônthict(l&r)+toeicw') !== false) return 53;
    if (strpos($c, 'combopre_toeic+toeiclv(1+2)+ônthict(l&r)') !== false) return 44;
    if (strpos($c, 'combopre_toeic+toeiclv(1+2)+toeics') !== false) return 46;
    if (strpos($c, 'combopre_toeic+toeiclv(1+2)+toeicw') !== false) return 53;
    if (strpos($c, 'combopre_toeic+toeiclv(1+2)') !== false) return 44;
    
    if (strpos($c, 'combotoeiclv(1+2)+toeic(s+w)') !== false) return 49;
    if (strpos($c, 'toeiclv(1+2)+toeic(s+w)') !== false) return 49;
    if (strpos($c, 'combotoeiclv(1+2)+toeics') !== false) return 54;
    if (strpos($c, 'toeiclv(1+2)+toeics') !== false) return 54;
    if (strpos($c, 'combotoeiclv1+2') !== false) return 45;
    
    if (strpos($c, 'combopre_toeic+toeiclv1+toeic(s+w)') !== false) return 51;
    if (strpos($c, 'combopre_toeic+toeiclv1') !== false) return 43;

    if (strpos($c, 'combotoeiclv1+toeic(s+w)') !== false) return 52;
    
    if (strpos($c, 'combotoeiclv2+toeic(s+w)') !== false) return 48;
    if (strpos($c, 'combotoeiclv2+toeics') !== false) return 55;
    if (strpos($c, 'combotoeic(s+w)') !== false) return 47;
    if (strpos($c, 'toeic(s+w)') !== false) return 47;
    
    if (strpos($c, 'pre_toeic') !== false) return 38;
    if (strpos($c, 'toeiclv1') !== false) return 39;
    if (strpos($c, 'toeiclv2') !== false) return 40;
    if (strpos($c, 'toeicspeaking') !== false) return 41;
    if (strpos($c, 'toeicwriting') !== false) return 42;
    
    return null;
}

$results = [];
foreach ($courses as $c) {
    if (empty($c)) continue;
    $id = getTuitionFeeIdFromCourse($c);
    $results[] = [
        'course' => $c,
        'mapped_id' => $id === null ? 'NULL (Chưa map được)' : $id
    ];
}

echo json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
