<?php
// Edit contact of client.
if($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET["updateContact"])) {

  // Curent loged user.
  $user_id = $_SESSION['user_id'];
  $user = $entityManager->find("\App\Entity\User", $user_id);

  $contact_id = $_POST["contact_id"];
  $client_id = basicValidation($_GET["client_id"]);

  $contact_type_id = $_POST["contact_type_id"];
  $contact_type = $entityManager->find("\App\Entity\ContactType", $contact_type_id);
  $body = basicValidation($_POST["body"]);
  $note = basicValidation($_POST["note"]);

  $contact = $entityManager->find('\App\Entity\Contact', $contact_id);

  if ($contact === null) {
    echo "Contact with ID $contact_id does not exist.\n";
    exit(1);
  }

  $contact->setType($contact_type);
  $contact->setBody($body);
  $contact->setNote($note);
  $contact->setModifiedByUser($user);
  $contact->setModifiedAt(new DateTime("now"));

  $entityManager->flush();

  die('<script>location.href = "?view&client_id='.$client_id.'" </script>');
}
