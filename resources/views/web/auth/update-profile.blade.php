<div class="modal-dialog">
    <div class="modal-content">
        <form method="POST" action="{{route('profile-update')}}">
            <div class="modal-header">
                <h5 class="modal-title">Profile</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">

                            <div class="row">
                                <div class="col-sm-12 form-group">
                                    <label class="control-label">First Name</label>
                                    <input class="form-control" type="text" placeholder="First Name" name="first_name" value="{{!empty($data['first_name']) ? $data['first_name'] : ''}}">
                                </div>

                                <div class="col-sm-12 form-group">
                                    <label class="control-label">Last Name</label>
                                    <input class="form-control" type="text" placeholder="Last Name" name="last_name" value="{{!empty($data['last_name']) ? $data['last_name'] : ''}}">
                                </div>

                                <div class="col-sm-12 form-group">
                                    <label class="control-label">Email</label>
                                    <input class="form-control" type="text" placeholder="Email" name="email" value="{{!empty($data['email']) ? $data['email'] : ''}}">
                                </div>

                                <div class="col-sm-12 form-group">
                                    <label class="control-label">Image</label>
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input upload-file-input" name="image" aria-describedby="inputGroupFileAddon01">
                                            <label class="custom-file-label upload-file-label" for="image">Choose file</label>
                                        </div>
                                        <div class="input-group-append">
                                            <span class="input-group-text app-icon-link" data-url="{{!empty($data['image']) ? $data['image'] : ''}}">
                                                <i class="fa fa-file" aria-hidden="true"></i>
                                            </span>
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