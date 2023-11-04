<?php
/*
 * Copyright (c) 2023. Manuel Daniel Dahmen
 *
 *
 *    Copyright 2012-2023 Manuel Daniel Dahmen
 *
 *    Licensed under the Apache License, Version 2.0 (the "License");
 *    you may not use this file except in compliance with the License.
 *    You may obtain a copy of the License at
 *
 *        http://www.apache.org/licenses/LICENSE-2.0
 *
 *    Unless required by applicable law or agreed to in writing, software
 *    distributed under the License is distributed on an "AS IS" BASIS,
 *    WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *    See the License for the specific language governing permissions and
 *    limitations under the License.
 */
?>
<html>
<head><title>Vers l'application Agenda ou le blog <strong><?php echo isset($_GET["page"])?$_GET["page"]:""; ?></strong></title></head>
<body>
<h1><a href="src/index.php">Page d'accueil de l'application agenda<?php if(isset($_GET["page"]))
    echo "Erreur".rand(0, 1000);
else
    echo "";
?></a></h1>
<h1><a href="../">Retour au blog</a></h1>
</body>
</html>
