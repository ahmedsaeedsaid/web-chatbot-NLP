function check() {

    var lvl = $('#lev_container');

    var student = document.getElementById("drole-st").checked;

    var professor = document.getElementById("drole-pro").checked;

    var instructor = document.getElementById("drole-inst").checked;

    if (student) {

        if (document.getElementById("phd")) {

            document.getElementById("phd").remove();

        } else if (document.getElementById("master")) {

            document.getElementById("master").remove();

        }

        if (!document.getElementById("lvl")) {

            lvl.append(`

            <div id="lvl">

                <!-- Faculty_ID -->
                <div class="form-group">
                    <label for="facultyID" class="col-md-4 control-label" id="facultyID-label">Faculty ID</label>

                    <input type="number" id="facultyID" name="facultyID" class="form-control" placeholder="Faculty ID" style="border-radius:0;padding:10px;height:100%;" required="" />

                    <span id="facultyID_result"></span>
                </div>

                <div class="form-group">
                    <label for="level" class="col-md-4 control-label" id="level-label">Level</label>

                    <div id="level">

                        <select class="btn btn-primary dropdown-toggle" id="dlevel" name="dlevel" style="color:#000; border-radius:0; padding:10px;" required>

                            <option value="">--Level--</option>


                        </select>

                    </div>
                </div>

                <div id="depart">

                </div>

            </div>

            <script>
                var faculty = document.getElementById("dfac").value;

                if(faculty != ''){

                    if(document.getElementById("dlevel"))
                        $('#dlevel').children('option:not(:first)').remove();

                    $.ajax({
                        url: base_url + "RegisterCont/get_faculty_levels/",
                        method: "POST",
                        data: {[name] : hash, faculty: faculty},
                        success: function(data){
                            for(i = 1; i <= data; i++){

                                $("#dlevel").append('<option value="'+i+'">Level '+i+'</option>');
                            }
                        }
                    });

                }

                $('#dlevel').on('change', function(){
                    if(document.getElementById("dlevel")){
                        level = document.getElementById("dlevel").value;
                    
                        uni = document.getElementById("duni").value;
                    
                        fac = document.getElementById("dfac").value;
                        var data = {};
                        data[name] = hash;
                        data['level'] = level;
                        data['uni'] = uni;
                        data['fac'] = fac;

                        $.ajax({
                            url: base_url + "RegisterCont/get_faculty_departments/",
                            method: "POST",
                            data: data,
                            success: function(data){

                                if(data != false){

                                    if(document.getElementById("departments")){
                                        $('#ddepartment').children('option:not(:first)').remove();
                                    }else{
                                        $("#depart").append(\`
                                            <div class="form-group">
                                                <div id="departments">
                                                    <label for="department" class="col-md-4 control-label" id="department-label">Department</label>

                                                    <div id="department">

                                                        <select class="btn btn-primary dropdown-toggle" id="ddepartment" name="ddepartment" style="color:#000; border-radius:0; padding:10px;" required>
                                                            <option value="">--Department--</option>
                                                        </select>

                                                    </div>
                                                </div>
                                            </div>
                                        \`);
                                    }
                                    $('#ddepartment option[value=""]').attr("disabled", "disabled");

                                    depart = JSON.parse(data);

                                    level = document.getElementById("dlevel").value;

                                    depart.forEach(function(dep){
                                        if(level >= dep.starting_level)
                                            $("#ddepartment").append('<option value="'+dep.id+'">'+dep.acronym+'</option>');
                                        else{
                                            if(document.getElementById("departments"))
                                                document.getElementById("departments").remove();
                                            return false;
                                        }
                                    });
                                }
                                
                            }
                        });
                        
                    }
                });
            </script>



            `);

            $('#dlevel option[value=""]').attr("disabled", "disabled");

            $('#facultyID').change(function () {

                var facultyID = $('#facultyID').val();
                
                var data = {};
                data[name] = hash;
                data['facultyID'] = facultyID;

                $.ajax({

                    url: base_url + "RegisterCont/check_facultyID/",

                    method: "POST",

                    data: data,

                    success: function (data) {

                        if(facultyID != '')
                            $('#facultyID_result').html(data);
                        else
                            $('#facultyID_result').html('');

                    }

                });



            });

        }

    } else if (professor) {

        if (document.getElementById("lvl")) {

            document.getElementById("lvl").remove();

        } else if (document.getElementById("master")) {

            document.getElementById("master").remove();

        }

        if (!document.getElementById("phd")) {

            lvl.append(`
                <div class="form-group">
                    <div id="phd">

                        <label for="level" class="col-md-4 control-label" id="level-label">PhD.</label>

                        <input type="text" name="dphd" id="dphd" class="form-control" placeholder="Enter you phD..." required="" style="border-radius:0;padding:10px;height:100%;">

                    </div>
                </div>

            `);

        }

    } else if (instructor) {

        if (document.getElementById("lvl")) {

            document.getElementById("lvl").remove();

        } else if (document.getElementById("phd")) {

            document.getElementById("phd").remove();

        }

        if (!document.getElementById("master")) {

            lvl.append(`
                <div class="form-group">
                    <div id="master">

                        <label for="level" class="col-md-4 control-label" id="level-label">Master</label>

                        <input type="text" name="dmaster" id="dmaster" class="form-control" placeholder="Enter you Master..." required="" style="border-radius:0;padding:10px;height:100%;">

                    </div>
                </div>

            `);

        }

    }

}


$('#dfac').on('change', function(){
    if(document.getElementById("dlevel"))
        $('#dlevel').children('option:not(:first)').remove();
    if(document.getElementById("ddepartment"))
        $('#ddepartment').children('option:not(:first)').remove();


    $('#dfac option[value=""]').attr("disabled", "disabled");

    var faculty = document.getElementById("dfac").value;
    
    if(faculty != ''){

        $('#dlevel').children('option:not(:first)').remove();
        var data = {};
        data[name] = hash;
        data['faculty'] = faculty;

        $.ajax({
            url: base_url + "RegisterCont/get_faculty_levels/",
            method: "POST",
            data: data,
            success: function(data){
                for(i = 1; i <= data; i++){

                    $("#dlevel").append('<option value="'+i+'">Level '+i+'</option>');
                }
            }
        });

    }
});


$('#duni, #dfac').on('change', function(){
    if(document.getElementById("dlevel")){
        level = document.getElementById("dlevel").value;
    
        uni = document.getElementById("duni").value;
    
        fac = document.getElementById("dfac").value;
        var data = {};
        data[name] = hash;
        data['level'] = level;
        data['uni'] = uni;
        data['fac'] = fac;

        $.ajax({
            url: base_url + "RegisterCont/get_faculty_departments/",
            method: "POST",
            data: data,
            success: function(data){

                if(document.getElementById("departments")){
                    document.getElementById("departments").remove();
                }

                if(data != false){

                    if(document.getElementById("departments")){
                        $('#ddepartment').children('option:not(:first)').remove();
                    }else{
                        $("#depart").append(`
                            <div class="form-group">
                                <div id="departments">
                                    <label for="department" class="col-md-4 control-label" id="department-label">Department</label>

                                    <div class="id="department">

                                        <select class="btn btn-primary dropdown-toggle" id="ddepartment" name="ddepartment" style="color:#000; border-radius:0; padding:10px;" required>
                                            <option value="">--Department--</option>
                                        </select>

                                    </div>
                                </div>
                            </div>
                        `);
                    }
                    $('#ddepartment option[value=""]').attr("disabled", "disabled");

                    depart = JSON.parse(data);

                    level = document.getElementById("dlevel").value;

                    depart.forEach(function(dep){

                        if(level >= dep.starting_level)
                            $("#ddepartment").append('<option value="'+dep.id+'">'+dep.acronym+'</option>');
                        else{
                            if(document.getElementById("departments"))
                                document.getElementById("departments").remove();
                            return false;
                        }

                    });
                }
                
            }
        });
        
    }
});