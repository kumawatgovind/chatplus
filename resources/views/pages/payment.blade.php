<form method="post" name="redirect"
    action="<?php echo $paymentUrl ?>/transaction/transaction.do?command=initiateTransaction">
    <input type='hidden' name='encRequest' value='{{ $encryptedData }}' />
    <input type='hidden' name='access_code' value='{{ $accessCode }}' />
    <!-- <button type="submit">Payment</button> -->
    <script language='javascript'>document.redirect.submit();</script>
</form>