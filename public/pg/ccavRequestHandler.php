<html>

<head>
	<title> Non-Seamless-kit</title>
</head>

<body>
	<center>

		<?php include('Crypto.php') ?>
		<?php

		error_reporting(1);

		$merchant_data = '';
		$working_key = '2A84CBAC562221A876366ADDA39012DB'; //Shared by CCAVENUES
		$access_code = 'AVLX05KK34BT53XLTB'; //Shared by CCAVENUES
		echo "<pre>";
		print_R($_POST);
		foreach ($_POST as $key => $value) {
			$merchant_data .= $key . '=' . $value . '&';
		}

		$encrypted_data = encrypt($merchant_data, $working_key); // Method for encrypting the data.
		$urlTest = 'https://test.ccavenue.com';
		$urlLive = 'https://secure.ccavenue.com';
		?>
		<form method="post" name="redirect"
			action="<?php echo $urlTest ?>/transaction/transaction.do?command=initiateTransaction">
			<?php
			echo "<input type='hidden' name='encRequest' value='$encrypted_data'>";
			echo "<input type='hidden' name='access_code' value='$access_code'>";
			echo "<button type='submit'>Payment</button>";
			?>
		</form>
	</center>
	<!-- <script language='javascript'>document.redirect.submit();</script> -->
</body>

</html>