<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">User Details</h5>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
            <div class="container-fluid">

                <div class="row">
                    <div class="col-md-4">
                        <img src="{{$userObject->image}}" style="max-width:200px" class="img-fluid" alt="User Avatar">
                    </div>
                    <div class="col-md-8">
                        <h5 class="card-title">{{$userObject->full_name}}</h5>
                        <p class="card-text"><b>First Name</b>: {{$userObject->first_name}}</p>
                        <p class="card-text"><b>Last Name</b>: {{$userObject->last_name}}</p>
                        <p class="card-text"><b>Email</b>: {{$userObject->email}}</p>
                        <p class="card-text"><b>Phone</b>: {{$userObject->country_code}} {{$userObject->phone}}</p>
                        <p class="card-text"><b>User Type</b>: {{$userObject->user_type}}</p>
                        <p class="card-text"><b>Push Notification</b>: {{ucwords(strtolower(array_flip(PUSH_NOTIFICATION_USER_SETTING)[$userObject->push_notification]))}}</p>
                        <p class="card-text"><b>Timezone</b>: {{$userObject->timezone}}</p>
                        <p class="card-text"><b>Date Of Birth</b>: {{$userObject->date_of_birth}}</p>
                        <p class="card-text"><b>Biography</b>: {{$userObject->biography}}</p>
                        <p class="card-text"><b>Gender</b>: {{$userObject->gender}}</p>
                        <p class="card-text"><b>Language</b>: {{$userObject->language}}</p>
                        <p class="card-text"><b>Profile Completed</b>: {{ucwords(strtolower(array_flip(PROFILE_COMPLETE)[$userObject->is_profile_completed]))}}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>