<?php
    //Gestione accesso senza permesso
    session_start();
    if(isset($_SESSION["user"]))
    {
        header("Location: ../index.php");
        exit;
    }
?>
<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <title>Ingresso</title>
        
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        <link rel="stylesheet" type="text/css" href="../css/StilePrincipale.css">
        <link rel="stylesheet" type="text/css" href="../css/StileLogin.css">
        
        <link rel="icon" href="../logo.bmp"  type="image/bmp">
        
        <script type="text/javascript" src="../js/UploadSubscriptionRequest.js"></script>
        
    </head>
    <body>
        <div class="wrapper">
            <header>
                <h1>Ingresso</h1>
            </header>
            <?php require_once 'inline/Navbar.php'; ?>
            <div class="container">
            <h3>Cos'è questo posto?</h3>
            <p>
                Questo è il portale di iscrizione per il sito del Circolo di 
                Biliardino. Se sei membro del circolo non ti resta che compilare
                il form e attendere che un amministratore ti permetta di 
                accedere al sito, se invece non sai a che mi riferisco allora 
                probabilmente non dovresti essere qui.
            </p>
            

            <div class="iscrizione" id="diviscrizione">
                <form id="request_form"> <!-- action="/CoseBuffe/php/SubRequest.php" method="post" autocomplete="off"-->
                    <table id="Generalita">
                        <caption>
                            Richiesta di iscrizione
                            
                        <tbody>
                            <tr>
                                <th><label for="name">Nome:</label>
                                <td><input type="text" name="name" id="name" required="" class="userdata">

                            <tr>
                                <th><label for="surname">Cognome:</label>
                                <td><input type="text" name="surname" id="surname" required="" class="userdata">

                            <tr>
                                <th><label for="username">Username:</label>
                                <td><input type="text" name="username" id="username" required="" class="userdata">

                            <tr>
                                <th><label for="email">Indirizzo email:</label>
                                <td><input type="email" name="email" id="email" required="" class="userdata">

                            <tr>
                                <th><label for="pass">Password:</label>
                                <td><input type="password" name="password" id="pass" required="" class="userdata">

                            <tr>
                                <th><label for="pass2">Conferma password:</label>
                                <td><input type="password" name="password2" id="pass2" required="" class="userdata">

                            <tr>
                                <th><label for="birth">Data di nascita:</label>
                                <td><input type="date" name="birthday" id="birth" required="" class="userdata" min="1920-01-01">

                            <tr>
                                <th>Sesso:
                                <td>
                                    <input type="radio" name="sex" value="M" id="male" required="">
                                    <label for="male">M</label>
                                    <input type="radio" name="sex" value="F" id="female" required="">
                                    <label for="female">F</label>
                        </tbody>
                        <tbody>
                            <tr>
                                <th colspan="2"><label for="note">Note (opzionali):</label>
                            <tr>
                                <td colspan="2">
                                    <textarea rows="5" id="note" name="note" maxlength="256"></textarea>
                        </tbody>
                    </table>
                    <input type="reset" value="Annulla" class="button">
                    <input type="submit" value="Richiedi" class="button">
                </form>
                </div>
            </div>
            <div class="push"></div>
        </div>
        <?php require_once 'inline/Footer.php'; ?>
    </body>
</html>
