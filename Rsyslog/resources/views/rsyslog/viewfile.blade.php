@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading"> {{ $namefile}} </div>

                <div class="panel-body">
                    <table class= "table table-bordered" >
                        <tr>
                            <th>NO.</th>
                            <th>CUSGROUP</th>
                            <th>CID</th>
                            <th>IP</th>
                            <th>Service</th>
                            <th>Message</th>
                            <th>Log Date/Time</th>
                        </tr>
                        @if (count($arrayMerge) > 0)
                        <?php $n=1 ?>
                            @foreach ($arrayMerge as $row)
                                <tr>
                                    <td> {{ $n++ }} </td>
                                    <td> {{ $row['name_cusgroup'] }} </td>
                                    <td> {{ $row['cid'] }} </td>
                                    <td> {{ $row[1] }} </td>
                                    <td> {{ $row[2] }} </td>
                                    <td> {{ $row[3] }} </td>
                                    <td> {{ $row[0] }} </td>
                                </tr>
                            @endforeach
                        @endif
                    </table>

                    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
