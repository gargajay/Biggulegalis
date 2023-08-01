<div class="modal-dialog">
    <div class="modal-content">
        <form method="POST" action="{{ route('payment.form.save', ['id' => $paymentObject->id]) }}" data-form-reset="true"  data-form-model-hide="{{ $paymentObject->id ? 'true' : ''}}">
            <div class="modal-header">
                <h5 class="modal-title">{{ $paymentObject->id ? 'Edit' : 'Add' }} User</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-sm-12 form-group">
                                    <label class="control-label">Name</label>
                                    <input class="form-control" type="text" placeholder="Name" name="name" value="{{$paymentObject->name}}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-sm-12 form-group">
                                    <label class="control-label">Transaction Id</label>
                                    <input class="form-control" type="text" placeholder="Transaction Id" name="transaction_id" value="{{$paymentObject->transaction_id}}">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-sm-12 form-group">
                                    <label class="control-label">Amount</label>
                                    <input class="form-control" type="text" placeholder="Amount" name="amount" value="{{$paymentObject->amount}}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-sm-12 form-group">
                                    <label class="control-label">Currency</label>
                                    <input class="form-control" type="text" placeholder="Currency" name="currency" value="{{$paymentObject->currency}}">
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
