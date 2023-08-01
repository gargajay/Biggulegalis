<div class="modal-dialog">
    <div class="modal-content">
        <form method="POST" action="{{route('change-password')}}">
            <div class="modal-header">
                <h5 class="modal-title">Change Password</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">

                            <div class="row">
                                <div class="col-lg-12 form-group">
                                    <label class="control-label">Old Password</label>
                                    <div class="input-group">
                                        <input class="form-control" type="password" name="old_password">
                                        <div class="input-group-append password-input">
                                            <span class="input-group-text"><i class="fa fa-eye-slash" aria-hidden="true"></i></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 form-group">
                                    <label class="control-label">New Password</label>
                                    <div class="input-group">
                                        <input class="form-control" type="password" name="password">
                                        <div class="input-group-append password-input">
                                            <span class="input-group-text"><i class="fa fa-eye-slash" aria-hidden="true"></i></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 form-group">
                                    <label class="control-label">Confirm Password</label>
                                    <div class="input-group">
                                        <input class="form-control" type="password" name="password_confirmation">
                                        <div class="input-group-append password-input">
                                            <span class="input-group-text"><i class="fa fa-eye-slash" aria-hidden="true"></i></span>
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary submitBtn" type="button"><i class="fa fa-fw fa-lg fa-check-circle"></i>Submit</button>
            </div>
        </form>
    </div>
</div>