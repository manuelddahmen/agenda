<?php
/*
 * Copyright (c) 2023-2024. Manuel Daniel Dahmen
 *
 *

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

$js_start_errors = array();

function printJsPhpErrors()
{
    global $js_start_errors;
    ?>
    <script type="text/javascript" language="JavaScript">

        function printErrors() {
            let elementById = document.getElementById("errors");
            <?php
            foreach ($js_start_errors as $key => $text) { ?>
                elementById.innerHTML += "<?php echo htmlspecialchars($text); ?>";
            <?php } ?>
        }
        printErrors();
    </script>
    <?php

}

    function addError(string $string, string $sql)
    {
        global $js_start_errors;

        $js_start_errors[count($js_start_errors)] =  $sql;

    }
