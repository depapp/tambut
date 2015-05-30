@section('asset')
    <script src="{{ asset('js/Chart.min.js')}}"></script>
@stop

@section('title')
    {{ $title }}
@stop

@section('breadcrumb')
    <li>{{ $title }}</li>
@stop

@section('content')
    Selamat datang di Menu Administrasi Larapus. Silahkan pilih menu administrasi yang diinginkan.
    <hr>
    <h4>Statistik Penulis</h4>
    <canvas id="chartPenulis" width="400" height="400"></canvas>
    <script>
    var data = {
        labels: {{ json_encode($authors) }},
        datasets: [
            {
                fillColor: "rgba(151,187,205,0.5)",
                strokeColor: "rgba(151,187,205,0.8)",
                pointColor: "rgba(220,220,220,1)",
                pointStrokeColor: "#fff",
                pointHighlightFill: "#fff",
                pointHighlightStroke: "rgba(220,220,220,1)",
                highlightFill: "rgba(151,187,205,0.75)",
                highlightStroke: "rgba(151,187,205,1)",
                data: {{ json_encode($books) }}
            }
        ]
    };

    var ctx = document.getElementById("chartPenulis").getContext("2d");
    var myLineChart = new Chart(ctx).Bar(data);

    </script>
@stop