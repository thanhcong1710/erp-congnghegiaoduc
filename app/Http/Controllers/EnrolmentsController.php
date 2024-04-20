<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Controllers\Controller;
use App\Models\LogStudents;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnrolmentsController extends Controller
{
    public function loadClasses(Request $request)
    {
        $data = [];
            $branches = $branch_id ? $branch_id : $session->branches_ids;
            $where = "id > 0 AND status > 0 AND branch_id IN (0,$branches) AND semester_id = $semester_id";
            $query = "SELECT id, 
                id AS item_id, 
                'program' AS item_type, 
                `name` AS `text`, 
                parent_id, 
                'fa fa-folder' AS icon, 
                0 AS status 
            FROM programs 
            WHERE $where
            UNION ALL
            SELECT CONCAT(999, c.id) AS id, 
                c.id AS item_id, 
                'class' AS item_type, 
                c.cls_name AS `text`, 
                c.program_id AS parent_id, 
                IF(c.cm_id > 0, IF(c.status = 1, 'fa fa-close', IF((SELECT COUNT(u.id) FROM users u LEFT JOIN sessions s ON u.id = s.teacher_id WHERE u.status > 0 AND s.class_id = c.id) > 0, 'fa fa-file-text-o', 'fa fa-frown-o')), 'fa fa-user-times') AS icon, 
                c.status 
            FROM classes AS c INNER JOIN programs AS p ON c.program_id = p.id
            WHERE c.cls_iscancelled = 'no' AND p.status > 0 AND p.branch_id IN (0,$branches) AND p.semester_id = $semester_id AND DATE(c.cls_enddate) >= CURDATE()";
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
            $classes = apax_get_tree_data($class);
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
