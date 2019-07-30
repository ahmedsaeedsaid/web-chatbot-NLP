
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>-->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" />
    <link href="<?php echo base_url(); ?>styles/css/BotQuestionGetterForm.css" rel="stylesheet" />
    
</head>

<body>
    <?php

?>
    <div class="container box" >

        <div class="row" style="    margin-bottom: 40px;">
            <div class="col-lg-8">
            <table id="questionsTable" class="display" >
                <thead>
                    <tr>
                        <th>SL</th>
                        <th>Question</th>
                        <th>Answer</th>
                        <th>Flow Question</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    
                    
                </tbody>
            </table>
            </div>
            <div class="col-lg-4 ">
                    <div class="form-group">
                        <label for="question">Question:</label>
                        <input type="question" class="form-control" id="question">
                    </div>
                
                <div class="form-group">
                    <label for="answer">Answer:</label>
                    <input type="answer" class="form-control" id="answer">
                </div>

                <div class="form-group">
                    <label for="parent_question">Answer:</label>
                    <select type="parent_question" class="form-control" id="parent_question" style="height: 35px;">
                        <option value="0">primary Question</option>
                    </select>
                </div>

                <div class="col-lg-12 text-center">
                    <button id="addRow" class="btn btn-default">Add Question</button>
                </div>
                
            </div>
        </div>
        <div class="row" style="    margin-bottom: 40px;">
                <div class="col-lg-12 text-center">
                    <button id="submit_question" class="btn btn-primary">Submit Question</button>
                </div>
        </div>
    </div>
</body>

</html>
<script>

$(document).ready( function () {
    var editing_SL="1";
    function refreshIndexes()
    {
        $( "#questionsTable tbody tr td:first-child" ).each(function( index ) {

            if($(this).text() != "No data available in table")
                $( this ).text(index+1);
        });
    }

    function redrowSelect()
    {
        $("#parent_question").html("");
        $("#parent_question").append('<option value="0">primary Question</option>');
        $( "#questionsTable tbody tr td:first-child" ).each(function( index ) {
            
            if($(this).text() != "No data available in table")
            {
                
                $("#parent_question").append('<option value="'+(index+1)+'">'+(index+1)+' - '+$(this).parent().children(":nth-child(2)").text()+' | ans: '+$(this).parent().children(":nth-child(3)").text()+'</option>');
            }
        });
    }


    var myTable =$('#questionsTable').DataTable( {
        "columnDefs": [
            { "searchable": false, "targets": 3 },
        ]
    });

    
    $('#addRow').on( 'click', function () {
        var addRowText = $('#addRow').text();
        if (addRowText == "Add Question")
        {
            var Question_value = $("#question").val().trim();
            var Answer_value = $("#answer").val().trim();
            var parent_question = $("#parent_question").val();
            if (Question_value != "" && Answer_value != "")
            {
                    myTable.row.add( [
                        "",
                        Question_value,
                        Answer_value,
                        parent_question,
                        '<button  class="btn btn-primary btn-xs Edit-row">Edit</button> <button  class="btn btn-danger btn-xs Delete-row" >Delete</button>  '
                    ] ).draw( false );
                    refreshIndexes();
                    redrowSelect();
                    $("#question").val("");
                    $("#answer").val("");
            }
        }
        else
        {
            var Question_value = $("#question").val().trim();
            var Answer_value = $("#answer").val().trim();
            var parent_question = $("#parent_question").val();
            if (Question_value != "" && Answer_value != "")
            {
                var row_editing = $( "#questionsTable tbody tr:nth-child("+editing_SL+")");
                row_editing.children(":nth-child(2)").text(Question_value);
                row_editing.children(":nth-child(3)").text(Answer_value);
                if(parent_question != editing_SL)
                    row_editing.children(":nth-child(4)").text(parent_question);
                $("#question").val("");
                $("#answer").val("");
                $("#parent_question").val("0");
            }
            $('#addRow').text("Add Question");
        }
        
        
        
    } );
 
    // Automatically remove a row of data
    $("#questionsTable").on('click', '.Delete-row', function () {
        var addRowText = $('#addRow').text();
        if (addRowText == "Add Question")
        {
            var removel_SL = $(this).parent().parent().children(":first").text();
            myTable
            .row( $(this).parent().parent() )
            .remove()
            .draw();

            refreshIndexes();
            

            $( "#questionsTable tbody tr td:nth-child(4)" ).each(function( index ) {
                if (removel_SL == $(this).text())
                {
                    $( this ).text("0");

                }
                else if ($(this).text() > removel_SL)
                {
                    current_value = $(this).text();
                    $(this).text(current_value-1);
                }
            });
            redrowSelect();
        }
        else
        {
            Swal.fire(
            'Failed!',
            'can\'t delete any Question when editing' ,
            'error'
        );
        }
        

        
        
    });

    $("#questionsTable").on('click', '.Edit-row', function () {
        editing_SL = $(this).parent().parent().children(":first").text();
        var Question_value = $(this).parent().parent().children(":nth-child(2)").text();
        var Answer_value = $(this).parent().parent().children(":nth-child(3)").text();
        var parent_question = $(this).parent().parent().children(":nth-child(4)").text();
        $("#question").val(Question_value);
        $("#answer").val(Answer_value);
        $("#parent_question").val(parent_question);
        $('#addRow').text("Update Question");
    });
    
    $('#submit_question').on( 'click', function () {
        var Questions_generated = [];
        
        $( "#questionsTable tbody tr td:first-child" ).each(function( index ) {
            
            if($(this).text() != "No data available in table")
            {
                var Question_ob ={
                    id : index+1,
                    Question : $(this).parent().children(":nth-child(2)").text(),
                    Answer : $(this).parent().children(":nth-child(3)").text(),
                    parent : $(this).parent().children(":nth-child(4)").text(),
                }
                Questions_generated.push(Question_ob);

            }
        });
        console.log(Questions_generated);
        
        
    } );

} );
</script>
