<?php
require_once "../../tijnk/public_html/pages/temp/db.php"
?>;


<!DOCTYPE html>
<html lang = "eng">

<head>
    <link rel="stylesheet" href="style_instel.css">
    <script src = "script_settings.js"></script>
    <title>Settings</title>
</head>


<body>
    <h1 id = "center">Settings</h1>


    <div class = "border">
        <br><h2>Thema</h2>
   
        <div>
            <!-- Start with dark mode -->
            <!-- <label class = "switch"  id = "center"> 
                <input type = "checkbox" checked onclick = "myFunction()">
                <span class = "dot"></span>
            </label> -->
            <!-- Start with light mode -->
            <label class = "switch"  id = "center"> 
                <input type = "checkbox" onclick = "myFunction()">
                <span class = "dot"></span>
            </label>
            <p class = "testt">Dark mode</p>
        </div><br>

        <br><h2>Taal</h2>

        <!-- php moet in de opties aangeroepen worden -->
        <div>
            <select>
                <option value="En">English</option>
                <option value="NL">Nederlands</option>
            </select>
        </div>
    
    
        <br><h2>Regio</h2>
        <!-- php moet in de opties aangeroepen worden -->
        <div>
            <select>
                <option value="0">Default</option>
                <option value="1">The Netherlands</option>
                <option value="2">UK</option>
                <option value="3">USA</option>
                <option value="4">Germany</option>
                <option value="5">France</option>
                <option value="6">France</option>
                <option value="7">France</option>
                <option value="8">France</option>
                <option value="9">France</option>
                <option value="10">France</option>
                <option value="11">France</option>
                <option value="12">France</option>
                <option value="13">France</option>
                <option value="14">France</option>
                <option value="15">France</option>
            </select>
        </div> <br><br><br>
    </div>

</body>

</html>
