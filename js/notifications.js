/*
 * Copyright (c) 2024. Manuel Daniel Dahmen
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

/***
 dayInWeek: 1=monday, 7=sunday
 */
function getNextWeeklyDay(dayInWeek, hour, minutes) {
    const now = new Date();
    const nextMonday = new Date(now);

    // Adjust to start of the day
    nextMonday.setHours(0, 0, 0, 0);

    // Number of days till next Monday
    let days = (dayInWeek + (7 - now.getDay())) % 7;

    if (days === 0 && now.getHours() >= hour && now.getMinutes() >= minutes) {
        // It is Monday right now but the alarm time has passed
        // So schedule it for next week
        days = 7;
    }

    nextMonday.setDate(now.getDate() + days);
    nextMonday.setHours(8);
    nextMonday.setMinutes(50);

    return nextMonday;
}

function eventNotification(strEventDesc, dayInWeek, hour, minutes) {
    var nextMondayAt850 = getNextWeeklyDay(dayInWeek, hour, minutes); // Get next monday at 08:50

    var timeDifferenceInMilliseconds = nextMondayAt850.getTime() - new Date().getTime();
    if (timeDifferenceInMilliseconds > 0) {
        setTimeout(function () {
            // The function you want to run at the next Monday 8:50
            if (Notification.permission === "granted") {
                new Notification(strEventDesc);
            } else if (Notification.permission !== "denied") {
                Notification.requestPermission().then(function (permission) {
                    if (permission === "granted") {
                        new Notification(strEventDesc);
                    }
                });
            }
        }, timeDifferenceInMilliseconds);
    }
}