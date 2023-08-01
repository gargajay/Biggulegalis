<div class="modal-dialog">
    <div class="modal-content">
        <form method="POST" action="{{ route('workout-hours.form.save', ['id' => $workoutHoursObject->id]) }}" data-form-reset="true"  data-form-model-hide="{{ $workoutHoursObject->id ? 'true' : ''}}">
            <div class="modal-header">
                <h5 class="modal-title">{{ $workoutHoursObject->id ? 'Edit' : 'Add' }} Workout Hours</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-sm-12 form-group">
                                    <label class="control-label">Name</label>
                                    <input class="form-control" type="text" placeholder="Name" name="name" value="{{$workoutHoursObject->name}}">
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
