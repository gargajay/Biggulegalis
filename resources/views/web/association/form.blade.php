<?php

use App\Models\Association;

$assocationTypes =  Association::assocationTypes();
$permissonTypes =  Association::permissonTypes();
$all =  Association::getAllAssociations();



?>

<div class="modal-dialog">
    <div class="modal-content">
        <form method="POST" action="{{ route('association.form.save', ['id' => $associationObject->id]) }}" data-form-reset="true"  data-form-model-hide="{{ $associationObject->id ? 'true' : ''}}">
            <div class="modal-header">
                <h5 class="modal-title">{{ $associationObject->id ? 'Edit' : 'Add' }} Association</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-sm-12 form-group">
                                    <label class="control-label">Name</label>
                                    <input class="form-control" type="text" placeholder="Name" name="name" value="{{$associationObject->name}}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 form-group">
                                    <label class="control-label">Location</label>
                                    <input class="form-control" type="text" placeholder="Name" name="location" value="{{$associationObject->location}}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 form-group">
                                    <label class="control-label">Desscription</label>
                                    <textarea class="form-control" id="editor" type="text" placeholder="Name" name="description"> {{$associationObject->description}}</textarea>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-12 form-group">
                                    <label class="control-label">Association Type</label>
                                    <select name="association_type" id="association_type" class="form-control">
                                        <option value="">Select Association Type</option>
                                        @foreach($assocationTypes as $key => $type)
                                            <option value="{{ $key+1 }}"  <?= $associationObject->association_type == ($key+1) ? 'selected' : '' ?>>
                                                {{ $type }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-12 form-group">
                                    <label class="control-label">Permission Type</label>
                                    <select name="permission_type" id="association_type" class="form-control">
                                        <option value="">Select Permisson Type</option>
                                        @foreach($permissonTypes as $key => $type)
                                            <option value="{{ $key+1 }}"  <?= $associationObject->permission_type == ($key+1) ? 'selected' : '' ?>>
                                                {{ $type }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-12 form-group">
                                    <label class="control-label">Parent Assocation</label>
                                    <select name="parent_id" id="association_type" class="form-control">
                                        <option value="">Select Parent Assocation </option>
                                        @foreach($all as $key => $type)
                                            <option value="{{ $type->id }}"  <?= $associationObject->parent_id == $type->id ? 'selected' : '' ?>>
                                                {{ $type->name }}
                                            </option>
                                        @endforeach
                                    </select>
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


@section('custom-js')

<script>
    CKEDITOR.replace('editor');
</script>

@stop
