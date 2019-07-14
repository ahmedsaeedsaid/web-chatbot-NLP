<html>
    <head>
        <script src="https://ap-gateway.mastercard.com/checkout/version/51/checkout.js"
                data-error="errorCallback"
                data-cancel="cancelCallback"
        data-complete="<?php echo base_url(); ?>subscribe/successOrder">
            </script>

        <script type="text/javascript">
            function errorCallback(error) {
                  console.log(JSON.stringify(error));
            }
            function cancelCallback() {
                  console.log('Payment cancelled');
            }
            
            Checkout.configure({
                merchant:'TEST222204466001',
                order: {
                    amount: 50,
                    currency: 'USD',
                    description: 'Ordered goods',
                   id: '2'
                    },
                    session: {
                      id: '<?=$session_id?>'
                    },
                interaction: {
                    merchant: {
                        name: 'optimal solution',
                        address: {
                            line1: '200 Sample St',
                            line2: '1234 Example Town'     
                        }    
                    }
                }
            });
            Checkout.showPaymentPage();
        </script>
    </head>
    <body>
    </body>
</html>