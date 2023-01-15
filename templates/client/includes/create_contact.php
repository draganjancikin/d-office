<?php
// Create new contact.
if ($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET["createContact"])) {
    $current_user_id = $_SESSION['user_id'];
    $current_user = $entityManager->find("\App\Entity\User", $current_user_id);

    $client_id = $_POST["client_id"];
    $client = $entityManager->find("\App\Entity\Client", $client_id);

    $type_id = $_POST["contact_type_id"];
    $type = $entityManager->find("\App\Entity\ClientType", $type_id);

    $contact_type_id = $_POST["contact_type_id"];
    $contact_type = $entityManager->find("\App\Entity\ContactType", $contact_type_id);
    $body = basicValidation($_POST["body"]);
    $note = basicValidation($_POST["note"]);

    $newContact = new \App\Entity\Contact();

    $newContact->setType($contact_type);
    $newContact->setBody($body);
    $newContact->setNote($note);
    $newContact->setCreatedAt(new DateTime("now"));
    $newContact->setCreatedByUser($current_user);
    $newContact->setModifiedAt(new DateTime("0000-01-01 00:00:00"));

    $entityManager->persist($newContact);
    $entityManager->flush();

    // Add $newContact to table v6_client_contacts.
    $client->getContacts()->add($newContact);

    $entityManager->flush();

    die('<script>location.href = "?view&client_id='.$client_id.'" </script>');
}
