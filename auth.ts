/*
 * Copyright (c) 2025. Manuel Daniel Dahmen
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

import NextAuth from "next-auth"
import Google from "next-auth/providers/google"
// Define your configuration in a separate variable and pass it to NextAuth()
// This way we can also 'export const config' for use later
export const config = {
    providers: [Google],
    pages: {
        signIn: "/agenda/src/index.php",
    },
}

export const {handlers, signIn, signOut, auth} = NextAuth({
    providers: [Google],
})