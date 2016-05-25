<?php
include "config.php";
?>

<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>Slack invitation</title>
<link rel="stylesheet" href="/css/pure-min.css">
<meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>

<?php
$method = $_SERVER['REQUEST_METHOD'];

if($method === "POST") {
	$secret = $_POST['secret'];
	$hashed = hash("sha256", $SALT.$secret);
	if($hashed !== $SECRET) {
		die('wrong secret');
	}
	else {
		$email = $_POST['email'];
		$url = 'https://tuatmcc.slack.com/api/users.admin.invite';
		$options = array('http' => array(
			'method' => 'POST',
			'content' => http_build_query(array(
				'email' => $email,
				'token' => $TOKEN
			))
		));
		$req = file_get_contents($url, false, stream_context_create($options));
		if($req === FALSE) {
			echo 'An Error occured.'.PHP_EOL;
			echo 'This issue will be reported to admin.';

			ob_start();
			var_dump($_POST);
			var_dump($_SERVER);
			var_dump($req);
			var_dump($data);
			$log = ob_get_contents();
			ob_end_clean();
			mail($ADMIN_EMAIL, 'issue from slack invitation', $log);
		}

		if($req) {
			$data = json_decode($req);
			if($data->{'ok'}) {
				echo 'send invitation to '.htmlspecialchars($email).".".PHP_EOL;
			}
			else {
				echo $data->{'error'};
			}
		}
	}
}
else {
?>
<form class="pure-form pure-form-stacked" method="POST">
<fieldset>
<legend>Slack invitation form</legend>

<label for="email">Email</label>
<input id="email" name="email" type="email" placeholder="Email">
<label for="secret">Secret</label>
<input id="secret" name="secret" type="password" placeholder="Secret">

<button type="submit" class="pure-button pure-button-primary">Submit</button>
</fieldset>
</form>
<?php
}
?>
</body>
</html>
