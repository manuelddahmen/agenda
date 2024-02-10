/*
 * Copyright (c) 2024. Manuel Daniel Dahmen
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

$(document).ready(function() {

    // Get page-content original position
    var contentTop = $('#page-content').offset().top;

    // Show/hide top link on page load
    showHideTopLink(contentTop);

    // Show/hide top link on scroll
    $(window).scroll(function() {
        showHideTopLink(contentTop);
    });

    // Scroll page on click action
    $('#page-top-link').click(function() {
        $('html, body').animate({ scrollTop: 0 }, 'fast');
        return false;
    });

    // Hash button on click action
    $('.file-info-button').click(function(event) {

        // Get the file name and path
        var name = $(this).closest('li').attr('data-name');
        var path = $(this).closest('li').attr('data-href');

        // Set modal title value
        $('#file-info-modal .modal-title').text(name);

        $('#file-info .md5-hash').text('Loading...');
        $('#file-info .sha1-hash').text('Loading...');
        $('#file-info .filesize').text('Loading...');

        $.ajax({
            url:     '?hash=' + path,
            type:    'get',
            success: function(data) {

                // Parse the JSON data
                var obj = jQuery.parseJSON(data);

                // Set modal pop-up hash values
                $('#file-info .md5-hash').text(obj.md5);
                $('#file-info .sha1-hash').text(obj.sha1);
                $('#file-info .filesize').text(obj.size);

            }
        });

        // Show the modal
        $('#file-info-modal').modal('show');

        // Prevent default link action
        event.preventDefault();

    });

});

function showHideTopLink(elTop) {
    if($('#page-navbar').offset().top + $('#page-navbar').height() >= elTop) {
        $('#page-top-nav').show();
    } else {
        $('#page-top-nav').hide();
    }
}
