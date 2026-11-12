<?php

namespace App;

use App\Models\Permission;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Spatie\Permission\Traits\HasRoles;
use App\Providers\UtilityServiceProvider as u;
use Illuminate\Support\Facades\Auth;
class User extends Authenticatable implements JWTSubject
{
    use Notifiable;
    use SoftDeletes;
    use HasRoles;
    private static $instance = null;
 
    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }    

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'status','hrm_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $dates = [
        'deleted_at'
    ];
    
    protected $guard_name = 'api';

    protected $attributes = [ 
        'menuroles' => 'user',
    ];

    public function checkPermission($permission)
    {
        $user_permission = u::first("SELECT pr.permission_id FROM permission_has_role AS pr 
            LEFT JOIN role_has_user AS r ON r.role_id=pr.role_id 
            LEFT JOIN permissions AS p ON p.id = pr.permission_id
            WHERE r.user_id = ".Auth::user()->id." AND p.name = '$permission'");
        
        return empty($user_permission) ? false : true;
    }

    public function getStaffHasUser($user_id=0)
    {
        $list_users = u::query("SELECT u.id,u.manager_id FROM users AS u WHERE u.status=1");
        if($user_id){
            $staff_has_user = $user_id. ",".implode(",",self::data_tree($list_users, $user_id));
        } else {
            $staff_has_user = Auth::user()->id. ",".implode(",",self::data_tree($list_users, Auth::user()->id));
        }
        return trim($staff_has_user, ',');
    }

    private function data_tree($data, $manager_id = 0){
        $result = [];
        foreach($data as $k=> $item){
            if($item->manager_id == $manager_id){
                $result[] = $item->id;
                unset($data[$k]);
                $child = self::data_tree($data, $item->id);
                $result = array_merge($result, $child);
            }
        }
        return $result;
    }

    public function getBranchesHasUser()
    {
        $list_branches = u::query("SELECT u.branch_id FROM branch_has_user AS u WHERE u.user_id = ".Auth::user()->id);
        $tmp = "";
        foreach($list_branches AS $branch){
            $tmp.= $tmp ? ",".$branch->branch_id : $branch->branch_id;
        }
        return $tmp ? $tmp : 0;
    }

    public function checkRole($role)
    {
        $user_role = u::first("SELECT r.role_id FROM role_has_user AS r
            WHERE r.user_id = ".Auth::user()->id." AND r.role_id = '$role'");
        
        return empty($user_role) ? false : true;
    }

    /**
     * Trả về danh sách user_id được phép truy cập nếu user hiện tại là Sale (68) hoặc Trưởng nhóm Sale (69).
     * Trả về null nếu user có quyền xem toàn bộ (Admin, CEO, Kế toán, CSKH, Giáo viên, hoặc có quyền canViewAllSale).
     */
    public function getSaleAllowedStaffIds()
    {
        if ($this->checkPermission('canViewAllSale') || $this->checkPermission('canViewAllParents')) {
            return null;
        }
        $unrestrictedRoles = ['999999', '686868', '1000002', '55', '56', '36', '37'];
        foreach ($unrestrictedRoles as $rId) {
            if ($this->checkRole($rId) || (string)$this->role_id === $rId) {
                return null;
            }
        }
        if ($this->checkRole('68') || $this->checkRole('69') || in_array((int)$this->role_id, [68, 69])) {
            return $this->getStaffHasUser();
        }
        return null;
    }

    /**
     * Xác định phạm vi xem Báo cáo của user hiện tại:
     * - 'all': Admin, CEO, Kế toán hoặc có canViewAllSale
     * - 'team': Trưởng nhóm Kinh doanh (role 69) hoặc có nhân viên cấp dưới
     * - 'self': Nhân viên Sale cá nhân (role 68)
     */
    public function getReportScope()
    {
        if ($this->checkPermission('canViewAllSale') || $this->checkRole('999999') || $this->checkRole('686868') || $this->checkRole('1000002') || in_array((int)$this->role_id, [999999, 686868, 1000002])) {
            return 'all';
        }
        if ($this->checkRole('69') || (int)$this->role_id === 69) {
            return 'team';
        }
        $staffIds = explode(',', $this->getStaffHasUser());
        if (count($staffIds) > 1) {
            return 'team';
        }
        return 'self';
    }

    /**
     * AI Permission relationship
     */
    public function aiPermission()
    {
        return $this->hasOne('App\Models\AiUserPermission', 'user_id');
    }
}
