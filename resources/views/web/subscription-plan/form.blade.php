<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <form method="POST" action="{{ route('subscription-plan.form.save', ['id' => $subscriptionPlanObject->getRawOriginal('id')]) }}" data-form-reset="true" data-form-model-hide="{{ $subscriptionPlanObject->getRawOriginal('id') ? 'true' : ''}}">
            <div class="modal-header">
                <h5 class="modal-title">{{ $subscriptionPlanObject->getRawOriginal('id') ? 'Edit' : 'Add' }} Subscription Plan</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">

                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-sm-12 form-group">
                                    <label class="control-label">Category</label>
                                    <select name="category" class="form-control">
                                        <option value="">Select Category</option>
                                        @foreach(SUBSCRIPTION_PLAN_CATEGORY as $key=>$value)
                                        <option value="{{$value}}" {{selected($subscriptionPlanObject->getRawOriginal('category'),$value)}}>{{$key}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-sm-12 form-group">
                                    <label class="control-label">Name</label>
                                    <input class="form-control" type="text" placeholder="Name" name="name" value="{{$subscriptionPlanObject->getRawOriginal('name')}}">
                                </div>
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-sm-12 form-group">
                                    <label class="control-label">Min Users</label>
                                    <input class="form-control" type="text" placeholder="Min Users" name="min_users" value="{{$subscriptionPlanObject->getRawOriginal('min_users')}}">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-sm-12 form-group">
                                    <label class="control-label">Max Users (Set 0 for unlimited)</label>
                                    <input class="form-control" type="text" placeholder="Max Users" name="max_users" value="{{$subscriptionPlanObject->getRawOriginal('max_users')}}">
                                </div>
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-sm-12 form-group">
                                    <label class="control-label">Duration</label>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <input class="form-control" type="text" placeholder="Duration" name="duration" value="{{$subscriptionPlanObject->getRawOriginal('duration')}}">
                                        </div>
                                        <div class="col-sm-6">
                                            <select name="interval" class="form-control">
                                                @foreach(SUBSCRIPTION_PLAN_INTERVAL as $key=>$value)
                                                <option value="{{$value}}" {{selected($subscriptionPlanObject->getRawOriginal('interval'),$value)}}>{{$key}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-sm-12 form-group">
                                    <label class="control-label">Per User Price</label>
                                    <input class="form-control" type="text" placeholder="Price" name="price" value="{{$subscriptionPlanObject->getRawOriginal('price')}}">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-sm-12 form-group">
                                    <label class="control-label">Currency</label>
                                    <select name="currency" class="form-control">
                                        @foreach(SUBSCRIPTION_CURRENCIES as $key=>$value)
                                        <option value="{{$value}}" {{selected($subscriptionPlanObject->getRawOriginal('currency'),$value)}}>{{$key}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-sm-12 form-group">
                                    <label class="control-label">Display Order</label>
                                    <input class="form-control" type="text" placeholder="Display Order" name="sort_order" value="{{$subscriptionPlanObject->getRawOriginal('sort_order')}}">
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