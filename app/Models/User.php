<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Helper\Helper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'full_name',
        'first_name',
        'last_name',
        'email',
        'country_code',
        'phone',
        'user_type',
        'facebook_id',
        'google_id',
        'apple_id',
        'twitter_id',
        'instagram_id',
        'account_type',
        'device_type',
        'device_token',
        'push_notification',
        'email_push_notification',
        'phone_push_notification',
        'image',
        'social_image_url',
        'email_verified_at',
        'phone_verified_at',
        'password',
        'timezone',
        'date_of_birth',
        'biography',
        'fitness_level',
        'workout_hours_id',
        'goal_id',
        'screen_color',
        'gender',
        'language',
        'is_profile_completed',
        'address_id',
        'stripe_id',
        'blocked',
        'default_subscription_plan_id',
        'cancel_subscription',
        'account_verified'
    ];


    protected $hidden = [
        'password',
        'remember_token',
        'user_type',
        'facebook_id',
        'google_id',
        'apple_id',
        'twitter_id',
        'instagram_id',
        'account_type',
        'email_verified_at',
        'phone_verified_at',
        'timezone',
        'language',
        'address_id',
        'stripe_id',
        'blocked',
        'default_subscription_plan_id',
        'cancel_subscription',
        'deleted_at',
        'created_at',
        'updated_at',
    ];


    protected $casts = [
        'id' => 'integer',
        'full_name' => 'string',
        'first_name' => 'string',
        'last_name' => 'string',
        'email' => 'string',
        'country_code' => 'string',
        'phone' => 'string',
        'user_type' => 'string',
        'facebook_id' => 'string',
        'google_id' => 'string',
        'apple_id' => 'string',
        'twitter_id' => 'string',
        'instagram_id' => 'string',
        'account_type' => 'string',
        'device_type' => 'string',
        'device_token' => 'string',
        'push_notification' => 'string',
        'email_push_notification' => 'string',
        'phone_push_notification' => 'string',
        'image' => 'string',
        'social_image_url' => 'string',
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'password' => 'string',
        'timezone' => 'string',
        'date_of_birth' => 'string',
        'biography' => 'string',
        'fitness_level' => 'string',
        'workout_hours_id' => 'int',
        'screen_color' => 'string',
        'goal_id' => 'integer',
        'gender' => 'string',
        'language' => 'string',
        'is_profile_completed' => 'string',
        'address_id' => 'integer',
        'stripe_id' => 'string',
        'blocked' => 'boolean',
        'default_subscription_plan_id' => 'integer',
        'cancel_subscription' => 'boolean',
        'account_verified' => 'boolean',
    ];


    public static $customAppend = [
        'Auth' => [],
        'Profile' => []
    ];

    public static $customRelations = [
        'Auth' => ['addresses', 'userAssociation'],
        'Profile' => ['addresses', 'userAssociation'],
        'Update' => ['addresses', 'userAssociation']
    ];

    public $appends = ['notification_count'];



    protected function getFullNameAttribute($value)
    {
        return ucwords(strtolower($value ?? trim($this->first_name . ' ' . $this->last_name)));
    }

    public function getNotificationCountAttribute()
    {
        $count = 0;
        $links = Invitation::latest();

        $ass =   UserAssociation::where('user_id', Auth::id())->first();

        if (!empty($ass)) {

            $officeBearesIds =   UserAssociation::where(function ($query) {
                $query->orWhereJsonContains('roles', 5)
                    ->orWhereJsonContains('roles', 6)
                    ->orWhereJsonContains('roles', 4)
                    ->orWhereJsonContains('roles', 7);
            })->where('status', 1)
                ->where('association_id', $ass->id)
                ->pluck('user_id')->toArray();

            if (in_array(Auth::id(), $officeBearesIds)) {
                $count =   $links->where('association_id', $ass->id)->where('type', 'from_association')->count();
            }else{
                $count =  $links->where('user_id', Auth::id())->where('type', 'from_user')->count();

            }
        }else{
            $count =  $links->where('user_id', Auth::id())->where('type', 'from_user')->count();
        }


        return $count;
    }

    // protected function setFullNameAttribute($value)
    // {
    //     $this->attributes['full_name'] = ucwords(strtolower($value ?? trim($this->attributes['first_name'] . ' ' . $this->attributes['last_name'])));
    // }

    public static function getAllPermissions($user_id = 1, $isIdsOnly = false, $onlytrue = false)
    {
        $userAsso =   UserAssociation::where('user_id', $user_id)->first();

        $userPermissions = [];

        $permissions =  [
            [
                'id' => 1,
                'name' => 'Staff Add',
                'is_selected' => false
            ],
            [
                'id' => 2,
                'name' => 'Announcements',
                'is_selected' => false
            ],
            [
                'id' => 3,
                'name' => 'Gallery',
                'is_selected' => false
            ],
            [
                'id' => 4,
                'name' => 'Links',
                'is_selected' => false
            ],
            [
                'id' => 5,
                'name' => 'courts',
                'is_selected' => false
            ],
            [
                'id' => 6,
                'name' => 'Announcments',
                'is_selected' => false
            ],

            [
                'id' => 8,
                'name' => 'Compliant',
                'is_selected' => false
            ],
            [
                'id' => 9,
                'name' => 'Old members',
                'is_selected' => false,
            ],
            [
                'id' => 10,
                'name' => 'Invitation',
                'is_selected' => false
            ],
            [
                'id' => 7,
                'name' => 'office bearers mangement',
                'is_selected' => false
            ],
            [
                'id' => 12,
                'name' => 'all members mangement',
                'is_selected' => false
            ],

        ];


        if (!empty($userAsso)) {
            $userPermissions = $userAsso->permissions;

            if (!empty($userPermissions)) {

                $userPermissions = json_decode($userPermissions);
                foreach ($permissions as &$per) {
                    if (in_array($per['id'], $userPermissions)) {
                        $per['is_selected'] = true;
                    }
                }


                if ($isIdsOnly && $onlytrue) {
                    return array_map(function ($per) {
                        if (($per['is_selected'])) {
                            return $per['id'];
                        }
                    }, $permissions);
                }
            }
        }

        if ($isIdsOnly) {
            return array_map(function ($per) {

                return $per['id'];
            }, $permissions);
        }




        return $permissions;
    }

    public function tabs()
    {
        $associationTabs = [];
        $Userassociation =   UserAssociation::where('user_id', auth('api')->id())->first();
        if(!empty($Userassociation)){
            $association = Association::where('id', $Userassociation->association_id)->first();
            $userIds =   UserAssociation::where('association_id', $association->id)->where('status', 1)->pluck('user_id');
    
            $president_id = UserAssociation::where('association_id', $association->id)
                ->whereJsonContains('roles', 4)
                ->where('user_id', auth('api')->id())
                ->pluck('user_id')->where('status', 1)->toArray();
    
            $officeId =   UserAssociation::where(function ($query) {
                $query->orWhereJsonContains('roles', 5)
                    ->orWhereJsonContains('roles', 6)
                    ->orWhereJsonContains('roles', 7);
            })->where('status', 1)
                ->where('association_id', $association->id)
                ->pluck('user_id')->toArray();
    
            $officeId =  array_merge($officeId, $president_id);
    
    
            $office =   User::with('userAssociation', 'addresses')->whereIn('id', $officeId)->latest()->get();
    
            $staffIds =   UserAssociation::where(function ($query) {
                $query->whereJsonContains('roles', 9);
            })->where('status', 1)
                ->where('association_id', $association->id)
                ->pluck('user_id')->toArray();
    
    
            $members =   User::whereIn('id', $staffIds)->with('addresses', 'userAssociation')->latest()->get();
    
            $gallerys = Gallery::where('association_id', $association->id)->latest()->get();
            $links = Link::where('association_id', $association->id)->latest()->get();
            $announcements = Announcement::where('user_id',auth('api')->id())->latest()->get();
    
    
            $compliants = Compliant::where('association_id', $association->id)->latest()->get();
    
            $quotes = Quote::where('association_id', $association->id)->latest()->get();
            $others = OtherPerson::where('association_id', $association->id)->latest()->get();
    
            $oldMembers = OldMember::where('association_id',  $association->id)->latest()->get();
            $committee = Committee::where('association_id',  $association->id)->latest()->first();
            $cmembers = [];
            if (!empty($committee)) {
                $committee->members;
    
                $cmembers =   User::whereIn('id', $committee->members)->with('addresses', 'userAssociation')->latest()->get();
            }
    
            $OtherAllMemberIds  =     UserAssociation::where(function ($query) {
                $query->orWhereJsonContains('roles', 8)
                    ->orWhereJsonContains('roles', 2)
                    ->orWhereJsonContains('roles', 3);
            })->where('status', 1)
                ->where('association_id', $association->id)
                ->pluck('user_id')->toArray();
    
    
            $Allothers =   User::whereIn('id', $OtherAllMemberIds)->with('addresses', 'userAssociation')->latest()->get();
    
    
            $allpermissions =  User::getAllPermissions(auth('api')->id());
    
            $associationTabs = [
                [
                    'id' => 1,
                    'name' => 'Staff',
                    'type' => 'Clearks',
                    'information' => $members,
                ]
            ];
    
            // checking is user roles in prisent or vice prisent 
            $userRoles = $Userassociation->roles->pluck('id')->toArray();
            $rolesToCheck = [4, 5, 6, 7];
    
            $checkPresent = array_intersect($userRoles, [4]);
            // Check if there are common elements between $userRoles and $rolesToCheck
            $commonRoles = array_intersect($userRoles, $rolesToCheck);
    
            Log::info("iuser roles" . json_encode($userRoles));
    
    
            $rolesNotInUserRoles = array_intersect($rolesToCheck, $userRoles);
            Log::info("iuserintersection" . json_encode($rolesNotInUserRoles));
    
    
            if (!$rolesNotInUserRoles) {
                $associationTabs[] = [
                    'id' => 8,
                    'name' => 'Complaint',
                    'type' => 'compliant',
                    'information' => $compliants
                ];
            }
    
    
    
    
            if ($commonRoles) {
    
                $associationTabs[] =   [
                    'id' => 3,
                    'name' => 'gallery',
                    'type' => 'gallery',
                    'information' => $gallerys
                ];
                $associationTabs[] =  [
                    'id' => 4,
                    'name' => 'links',
                    'type' => 'links',
                    'information' => $links
                ];
                $associationTabs[] = [
                    'id' => 7,
                    'name' => 'office bearers',
                    'type' => 'offcebear',
                    'information' => $office
                ];
                $associationTabs[] =   [
                    'id' => 9,
                    'name' => 'Old members',
                    'type' => 'old_member',
                    'information' => $oldMembers
                ];
    
                $associationTabs[] =  [
                    'id' => 10,
                    'name' => 'disciplinary committee',
                    'type' => 'committee',
                    'information' => $cmembers
                ];
    
                $associationTabs[] =     [
                    'id' => 11,
                    'name' => 'Others',
                    'type' => 'others',
                    'information' => $others,
                ];
    
                $associationTabs[] =   [
                    'id' => 5,
                    'name' => 'Courts',
                    'type' => 'quotes',
                    'information' => $quotes
                ];
    
                $associationTabs[] =   [
                    'id' => 2,
                    'name' => 'Announcments',
                    'type' => 'Announcments',
                    'information' => $announcements
                ];
    
                $associationTabs[] =     [
                    'id' => 12,
                    'name' => 'All Others Members',
                    'type' => 'offcebear',
                    'information' => $Allothers,
                ];
            }
    
    
    
            if (!$checkPresent) {
                // Filter $associationTabs based on permission IDs
                $filteredAssociationTabs = array_filter($associationTabs, function ($tab) use ($allpermissions) {
                    // Check if tab id is in the $allpermissions array and the corresponding permission is selected
                    $permission = collect($allpermissions)->firstWhere('id', $tab['id']);
                    return $permission && $permission['is_selected'];
                });
    
                // Reindex the array to ensure keys are consecutive
                $filteredAssociationTabs = array_values($filteredAssociationTabs);
    
                return $filteredAssociationTabs;
            }
    
            return $associationTabs;
        }
       
    }

    protected function setFirstNameAttribute($value)
    {
        $this->attributes['first_name'] = ucwords(strtolower($value));
    }

    protected function setLastNameAttribute($value)
    {
        $this->attributes['last_name'] = ucwords(strtolower($value));
    }

    protected function setEmailAttribute($value)
    {
        $this->attributes['email'] = strtolower($value);
    }

    protected function getImageAttribute($value)
    {
        return Helper::FilePublicLink($value, USER_IMAGE_INFO);
    }

    protected function getPrimaryCardStripeIdAttribute()
    {
        $primaryCard = StripeCard::where('user_id', Auth::id())->where('is_active', true)->first();
        return $primaryCard ? $primaryCard->stripe_card_id : null;
    }

    public static function logoutFromAllDevices(int $userId): void
    {
        // Find the user with the specified ID
        $user = User::find($userId);

        // Check if the user exists
        if (!empty($user)) {
            // Revoke all tokens for the user
            $user->tokens()->delete();
        }
    }


    public function addresses()
    {
        return $this->belongsTo(Address::class, 'address_id');
    }

    public function userAssociation()
    {
        return $this->hasOne(UserAssociation::class, 'user_id')->with('userRole');
    }

    public function getStatusAttribute()
    {
        if ($this->deleted_at === null) {
            return 'Active';
        } else {
            return 'Inactive';
        }
    }

    public static function boot()
    {
        parent::boot();

        self::updated(function ($model) {
            self::deleteFiles($model);
            self::refreshAuthData();
        });

        self::deleted(function ($model) {
            self::deleteFiles($model);
        });
    }


    protected static function refreshAuthData()
    {
        if (Auth::check()) {
            Auth::setUser(User::find(Auth::user()->id));
        }
    }

    protected static function deleteFiles($model)
    {
        foreach (['image'] as $key) {
            // Check if the field was changed or is force delete
            if ($model->wasChanged($key) || $model->isForceDeleting()) {
                $imagePath = $model->getRawOriginal($key);
                // Delete the file
                Helper::FileDelete($imagePath, USER_IMAGE_INFO);
            }
        }
    }

    public function getSubscriptionAttribute()
    {
        $subscriptionObject = Subscription::where('company_id', $this->company_id)->where('status', SUBSCRIPTION_STATUS['Active'])->where('start_date', '<=', Carbon::now())->where('end_date', '>=', Carbon::now())->with('subscription_plan')->first();
        $stripeCardObject = StripeCard::where('user_id', Auth::id())->where('is_active', true)->first();

        return [
            'subscription_status' => IsEmpty($subscriptionObject) ? false : true,
            'is_free_plan' => IsEmpty($subscriptionObject) ? null : ($subscriptionObject->subscription_plan->category == 1 ? true : false),
            'is_company' => $this->parent_user_id ? false : true,
            'stripe_card' => IsEmpty($stripeCardObject) ? false : true,
            'default_subscription_plan_id' => $this->default_subscription_plan_id,
            'cancel_subscription' => $this->cancel_subscription
        ];
    }
}
