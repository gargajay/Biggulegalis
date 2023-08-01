<div class="modal-dialog">
    <div class="modal-content">
        <form method="POST" action="{{ route('translate.form.save', ['id' => $translateObject->getRawOriginal('id')]) }}" data-form-reset="true" data-form-model-hide="{{ $translateObject->getRawOriginal('id') ? 'true' : ''}}">
            <div class="modal-header">
                <h5 class="modal-title">{{ $translateObject->getRawOriginal('id') ? 'Edit' : 'Add' }} Translate</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                @foreach(TRANSLATE_LANGUAGE as $key=>$value)
                                <div class="col-sm-12 form-group">
                                    <label class="control-label">{{$value}}</label>
                                    <input class="form-control" type="text" placeholder="{{$value}}" name="{{$key}}" value="{{$translateObject->getRawOriginal($key)}}">
                                </div>
                                @endforeach
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