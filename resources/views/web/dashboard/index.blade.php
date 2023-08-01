@extends('web.layouts.admin')

@section('title', config('app.name'))

@section('content')

<main class="app-content">

    <div class="app-title">
        <div>
            <h1><i class="fa {{$page_icon}}"></i> {{$page_title}}</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 col-lg-3">
            <div class="widget-small primary coloured-icon"><i class="icon fa fa-users fa-3x"></i>
                <div class="info">
                    <h4>Users</h4>
                    <p><b>{{$totalUser}}</b></p>
                </div>
            </div>
        </div>
        <!-- <div class="col-md-6 col-lg-3">
            <div class="widget-small info coloured-icon"><i class="icon fa fa-thumbs-o-up fa-3x"></i>
                <div class="info">
                    <h4>Likes</h4>
                    <p><b>25</b></p>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="widget-small warning coloured-icon"><i class="icon fa fa-files-o fa-3x"></i>
                <div class="info">
                    <h4>Uploades</h4>
                    <p><b>10</b></p>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="widget-small danger coloured-icon"><i class="icon fa fa-star fa-3x"></i>
                <div class="info">
                    <h4>Stars</h4>
                    <p><b>500</b></p>
                </div>
            </div>
        </div> -->
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="tile">
                <h3 class="tile-title">New Users</h3>
                <div class="embed-responsive embed-responsive-16by9">
                    <!-- Create a canvas element where the chart will be displayed -->
                    <canvas class="embed-responsive-item" id="barChartNewUser"></canvas>
                </div>
            </div>
        </div>
    </div>
</main>
@stop

@section('custom-js')
<script type="text/javascript">
    
    const chartData = <?php echo json_encode($monthlyUserData) ?>;
    createBarChart(chartData, 'barChartNewUser');

</script>
@stop