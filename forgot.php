<?php
$data = json_decode(file_get_contents("db.json"), true);

$email = $_POST['email'];
$new_pass = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
$updated = false;

foreach ($data as &$user) {
  if ($user['email'] === $email) {
    $user['password'] = $new_pass;
    $updated = true;
    break;
  }
}

if ($updated) {
  file_put_contents("db.json", json_encode($data, JSON_PRETTY_PRINT));
  echo "Password updated. <a href='login.html'>Login</a>";
} else {
  echo "Email not found.";
}
?>
