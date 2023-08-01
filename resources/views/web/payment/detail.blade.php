<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Payment Details</h5>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
            <div class="container-fluid">

                <div class="row">

                    <div class="col-md-8" >
                        <h5 class="card-title">{{$paymentObject['users']['full_name']}}</h5>
                        <p class="card-text"><b>Name</b>: {{$paymentObject['users']['full_name']}}</p>
                        <p class="card-text"><b>Transaction Id</b>: {{$paymentObject->transaction_id}}</p>
                        <p class="card-text"><b>Amount</b>: {{$paymentObject->amount}}</p>
                        <p class="card-text"><b>Currency</b>: {{$paymentObject->currency}}</p>
                        <p class="card-text"><b>Payment Status</b>: {{$paymentObject->payment_status}}</p>


                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
