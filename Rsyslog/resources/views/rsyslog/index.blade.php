@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">Rsyslog</div>

                <div class="panel-body">
                   
                {!! Form::open(['url' => '/log/viewfile']) !!}
                    {{ Form::token() }}

                    <div class="col-sm-4">
                        <div class="form-group">
                            {{ Form::label('length', 'length of entries') }}
                            {{ Form::select('length', $length, $lengthselect, ['class'=>'form-control', 'placeholder'=>'Please select.' ]) }}
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group">
                            {{ Form::label('namefile', 'File') }}
                            {{ Form::select('namefile', $contents, $namefileselect,['class'=>'form-control', 'placeholder'=>'Please select file.' ]) }}
                        </div>
                    </div>

                    <div class="col-xs-10">
                        <div class="form-group">
                            {{ Form::submit('view', ['class'=>'btn btn-success']) }}
                            {{ Form::button('download',['class'=>'btn btn-primary', 'onclick'=>'downloadfile()']) }}
                            {{ Form::button('delete',['class'=>'btn btn-danger', 'onclick'=>'deletefile()']) }}
                        </div>
                    </div>

                {!! Form::close() !!}

                    
                </div>
            </div><!-- panel-->
        </div>


        <div class="col-md-12 ">
            <div class="panel panel-default">
                
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-9">
                            <p id="count_Showing" >Showing {{ number_format(count($arrayMerge)) }} entries</p>
                        </div>
                    </div><!-- row-->
                </div>
                    
                

                <div class="panel-body">
                <div class="table-responsive">

                    <table class= "table table-striped table-bordered table-hover table-condensed" id = "tb-showlist">
                        <tr>
                            <th style="text-align:center;">NO.</th>
                            <th style="text-align:center;">CUSGROUP</th>
                            <th style="text-align:center;">CID</th>
                            <th style="text-align:center;">IP</th>
                            <th style="text-align:center;">Service</th>
                            <th style="text-align:center;">Message</th>
                            <th style="text-align:center;">Log Date/Time</th>
                        </tr>

                        <tr>
                            <th></th>
                            <th>
                                <input name="search" id="myInput1" type="text" placeholder="Search" onkeyup="mySearchfill()" class="form-control input-md" >
                            </th>
                            <th>
                                <input name="search" id="myInput2" type="text" placeholder="Search" onkeyup="mySearchfill()" class="form-control input-md" >
                            </th>
                            <th>
                                <input name="search" id="myInput3" type="text" placeholder="Search" onkeyup="mySearchfill()" class="form-control input-md" >
                            </th>
                            <th>
                                <input name="search" id="myInput4" type="text" placeholder="Search" onkeyup="mySearchfill()" class="form-control input-md" >
                            </th>
                            <th>
                                <input name="search" id="myInput5" type="text" placeholder="Search" onkeyup="mySearchfill()" class="form-control input-md" >
                            </th>
                            <th>
                                <input name="search" id="myInput6" type="text" placeholder="Search" onkeyup="mySearchfill()" class="form-control input-md" >
                            </th>
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
            </div><!-- panel-->
        </div>
        

    </div>
</div>
@endsection

@section('footer')
<script src="{{ asset('js/sweetalert.min.js')}}"></script>


    @if (count($errors) > 0)
        <script>
            swal({
                title: "Please select",
                text: "<?php foreach( $errors->all() as $error ){ echo $error.' '; } ?>",
                icon: "warning",
                dangerMode: true,
            })
        </script>
    @endif


<script>
    
function mySearchfill() {
    var input1, input2, input3, input4, input5, input6;
    var filter1, filter2, filter3, filter4, filter5, filter6;
    var td1, td2, td3, td4, td5, td6;
    var table, tr, i, r=0;

    input1 = document.getElementById("myInput1");
    filter1 = input1.value.toUpperCase();

    input2 = document.getElementById("myInput2");
    filter2 = input2.value.toUpperCase();

    input3 = document.getElementById("myInput3");
    filter3 = input3.value.toUpperCase();

    input4 = document.getElementById("myInput4");
    filter4 = input4.value.toUpperCase();

    input5 = document.getElementById("myInput5");
    filter5 = input5.value.toUpperCase();

    input6 = document.getElementById("myInput6");
    filter6 = input6.value.toUpperCase();


    table = document.getElementById("tb-showlist");
    tr = table.getElementsByTagName("tr");

    for (i = 0; i < tr.length; i++) {
        td1 = tr[i].getElementsByTagName("td")[1];
        td2 = tr[i].getElementsByTagName("td")[2];
        td3 = tr[i].getElementsByTagName("td")[3];
        td4 = tr[i].getElementsByTagName("td")[4];
        td5 = tr[i].getElementsByTagName("td")[5];
        td6 = tr[i].getElementsByTagName("td")[6];

        if (td1 || td2 || td3 || td4 || td5 || td6) {
            if ( td1.innerHTML.toUpperCase().indexOf(filter1) > -1 &&
                td2.innerHTML.toUpperCase().indexOf(filter2) > -1 &&
                td3.innerHTML.toUpperCase().indexOf(filter3) > -1 &&
                td4.innerHTML.toUpperCase().indexOf(filter4) > -1 &&
                td5.innerHTML.toUpperCase().indexOf(filter5) > -1 &&
                td6.innerHTML.toUpperCase().indexOf(filter6) > -1 ) {

                    tr[i].style.display = "";
                    r++;
            } else {
                    tr[i].style.display = "none";
            }
        }       
    }
    document.getElementById("count_Showing").innerHTML = "Showing "+r+" entries";

}//f.mySearchfill


function downloadfile() {

    var namefile = document.getElementById("namefile").value;
    if(namefile==""){
        swal({
            title: "Please select file.",
            icon: "warning",
            dangerMode: true,
        })
    }else{
        window.location.href="{{ url('log/download/') }}/"+namefile;
    }
        

}//f.downloadfile


function deletefile(){

    var namefile = document.getElementById("namefile").value;
    if(namefile==""){
        swal({
            title: "Please select file.",
            icon: "warning",
            dangerMode: true,
        })
    }else{
        swal({
            title: "Are you sure?",
            text: "Once deleted, you will not be able to recover this file!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
        .then((willDelete) => {
            if (willDelete) {
                window.location.href="{{ url('log/delete/') }}/"+namefile;
            } else {
                return false;
            }
        });

        /*
        var answer 	= confirm("Do you want to delete the file?");
        if(answer==true) {
            window.location.href="{{ url('log/delete/') }}/"+namefile;
        }else{
            return false;
        }
        */
    }
    

}//f.deletefile




/*
function show() {

     
     $.ajax({
        'data': {
            '_token': '<?php echo csrf_token(); ?>',
            'namefile': namefile
        },
        "url": "{{ url('/log/show') }}",
        "type": "POST"
    }).done(function (result) {
        console.log(result); 
    });
    


}//f.show
*/




</script>

@endsection