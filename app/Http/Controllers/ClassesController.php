<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Controllers\Controller;
use App\Models\LogStudents;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClassesController extends Controller
{
    public function loadClasses(Request $request)
    {
        $data = [];
        $branch_id = (int)$request->branch_id;
        $product_id = (int)$request->product_id;
        $query = "SELECT id, 
            id AS item_id, 
            'program' AS item_type, 
            `name` AS `text`, 
            parent_id, 
            'fa fa-folder' AS icon, 
            0 AS status 
        FROM programs 
        WHERE id > 0 AND status = 1 AND product_id = $product_id
        UNION ALL
        SELECT CONCAT(999, c.id) AS id, 
            c.id AS item_id, 
            'class' AS item_type, 
            c.cls_name AS `text`, 
            c.program_id AS parent_id, 
            IF(c.cm_id > 0, 
                IF(c.status = 0, 
                    'fa-regular fa-rectangle-xmark fa-fw', 
                    IF((SELECT COUNT(u.id) FROM users u LEFT JOIN sessions s ON u.id = s.teacher_id WHERE u.status > 0 AND s.class_id = c.id) > 0, 'fa-solid fa-file-lines fa-fw', 'fa-solid fa-triangle-exclamation fa-fw')), 'fa-solid fa-user-xmark fa-fw') AS icon, 
            c.status 
        FROM classes AS c INNER JOIN programs AS p ON c.program_id = p.id
        WHERE p.status = 1 AND c.branch_id =$branch_id AND p.product_id = $product_id AND DATE(c.cls_enddate) >= CURDATE()";
        $class = u::query($query);
        if (count($class)) {
            foreach ($class as $item) {
                $item->value = $item->id;
                $item->opened = true;
                $item->selected = false;
                $item->disabled = false;
                $item->loading = false;
                $item->children = [];
            }
            $classes = u::get_tree_data($class);
            if ($classes) {
                foreach ($classes as $cls) {
                    if ($cls) {
                        $data[] = $cls;
                    }
                }
            }
        }
        return response()->json($data);
    }

}
