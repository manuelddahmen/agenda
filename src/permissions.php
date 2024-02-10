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

require_once "framework.php";
?>

Table user_role( userid, role, permission, role2, @Nullable  userid2)
Table manage_role(userid, role, permission, role2, @Nullable userid2)
class UserRole {

    function detailActionPossible (db, crud, userid_require,userid_required)
}
Associés utilisateurs
Liste
id, role ->
Autorisation :
Rôles: (plusieurs possibles, détails)
 - Admin
- Gérer des utilisateurs patients (connection, rôles)
- Gérer des utilisateurs personnel (connection, rôles)
 - Coordinateur
- consulter la liste des patients
- modifier la liste des patients
- gérer des activités
 - Equipe
 - Patient
Consulter ses données
