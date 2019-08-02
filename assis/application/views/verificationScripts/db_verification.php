<meta name="optimal-bot-verification" content="<?= $company->token ?>" />
<?php

function connect($host, $username, $password, $DBName, $DBType){
    switch ($DBType) {
        case "mysqli":
            $conn = new mysqli($host, $username, $password, $DBName);
            // Check connection
            if ($conn->connect_error) {
                return false;
            }
            break;
        case "postgresql":
            $conn = pg_connect("host=$host dbname=$DBName user=$username password=$password");
            // Check connection
            if ($conn->connect_error) {
                return false;
            }
            break;
    }
    return true;
}

$status = connect("localhost", $company->db_username, $company->db_password, $company->db_name, $company->db_driver);
?>
<script src="https://code.jquery.com/jquery-3.4.1.min.js" type="text/javascript"></script>
<script>
    $(document).ready(function() {
        var status = "<?= $status ?>";
        if (status) {
            var content = document.querySelector("meta[name=optimal-bot-verification]").getAttribute("content");
            $.ajax({
                type: "POST",
                url: "http://localhost:5002/validateDatabase",
                data: {
                    token: content,
                },
                success: function(result) {
                    if (result.status == 'success') {
                        document.write("Success, Database verified successfully, Thank you!");
                    } else {
                        document.write("Sorry, Connection failed, please verify your credentials in our support portal :)");
                    }
                }
            });
        }
    });

</script>
