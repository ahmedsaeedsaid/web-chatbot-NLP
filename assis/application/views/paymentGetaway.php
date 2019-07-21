<html>
    <head>
        

       
    </head>
    <body>
    <form id="myForm" action="https://www.optimalsolutionscorp.com/payment/redirectPaymentPage.php" method="post">
        <input type="hidden" name="order_id"  value="<?=$order_id?>">
        <input type="hidden" name="description_order" value="<?=$description_order?>">
        <input type="hidden" name="price" value="<?=$price?>">
        <input type="hidden" name="currency" value="<?=$currency?>">
        <input type="hidden" name="id" value="2">
    </form>
    <script type="text/javascript">
        
            document.getElementById('myForm').submit();
        
    </script>
    </body>
</html>