<?php
/*
"Reserve System" is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
"Reserve Course" is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with "Reserve Course". If not, see https://www.gnu.org/licenses/gpl-3.0.en.html.
*/

/**
 * Plugin Name:       Reserve Course
 * Plugin URI:    
 * Description:       Handle the basics with this plugin
 * Version:           0.0.1
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Clay Atlas
 * Author URI:        https://clay-atlas.com/us/
 * License:           GPLv3
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.en.html
 * Text Domain:
 * Text Path:
 */


// ABSPATH
defined( 'ABSPATH' ) || exit;


// Create menu
add_action('admin_menu', 'create_reserve_menu');

function create_reserve_menu() {
    //create new top-level menu
    add_menu_page(
        'Reserve System',
        'Reserve System',
        'administrator',
        'reserve-sysetm-id-0',
        'reserve_system_setting_page',
        'dashicons-book',
        99
    );

    //call register settings function
    add_action( 'admin_init', 'register_reserve_options_settings' );
}


function register_reserve_options_settings() {
    // Sample data
    $test_array = array(
        'teacher'         => 'Clay',
        'year'            => '2021',
        'Mon'             => '12',
        'Day'             => '20',
        'start_time'      => '0800',
        'end_time'        => '0900',
        'price'           => '29',
        'status'          => '1',
        'student_name'    => 'Chen Tung Chi',
        'sudent_email'    => 'skyonsame@gmail.com',
        'student_comment' => 'I am not sure whether I reserve it or not'
    );

    $ca_reserve_list = array(
        $test_array,
    );
    
    // Register our settings
    register_setting( 'tnt-settings-group', 'event_name' );
    register_setting( 'tnt-settings-group', 'ca_reserve_list', $ca_reserve_list );

    // Update
    // update_option( 'event_name' , 'Hello' );
    // update_option( 'ca_reserve_list', $ca_reserve_list );
    // array_push( $ca_reserve_list, $test_array );
    // update_option( 'ca_reserve_list', $ca_reserve_list );
}


// Reserve System wp-admin setting page
function reserve_system_setting_page() {
?>
<div class="wrap">
    <h1>CA Reserve System</h1>
    <form method="post" action="options.php">
        <?php settings_fields( 'tnt-settings-group' ); ?>
        <?php do_settings_sections( 'tnt-settings-group' ); ?>
        
        <table class="form-table">
            <p>
                <?php 
                    echo json_encode(get_option( 'event_name' ));
                    echo json_encode(get_option( 'ca_reserve_list' ));

                    global $new_whitelist_options;
                    $option_names = $new_whitelist_options[ 'tnt-settings-group' ];
                    echo json_encode( $option_names );
                ?>
            </p>
        </table>
        <?php submit_button(); ?>
    </form>
</div>

<?php 
}



// Try to edit the reseve page
add_filter( 'the_content', 'reserve_system_page_init' );

function reserve_system_page_init( $content ) {
    if ( get_the_title() == 'Reserve System Test' ) {
        // Variable
        $current_user = wp_get_current_user();
        $all_users = get_users( array( 'role__in' => array( 'author', '' )) );

        // Content
        $content = "
<html lang='en'>
<script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js'></script>
<head>
    <script>
        function Search() {
            $.ajax({
                type: 'POST',
                url: 'https://allthing-can.com/test.php',
                data: {
                    'type': 'search',
                    'id': $('#search_id').val(),
                    '_mon': $('#current_mon').text(),
                    '_tue': $('#current_tue').text()
                },
                dataType: 'json',
                success: function (str) {
                    document.getElementById('result').innerHTML = str.name;
                },
                error: function (e) {
                    alert('something error');
                },
                beforeSend: function() {
                    
                }
            });
        }
    </script>
</head>

<style>
table, tr, td {
    border: 1px solid black;
    text-align: center;
}

caption {
    text-align: center;
}

</style>


<body>
    <!-- Weekly calendar --!>
    <table>
        <caption id='current_month'><h3></h3></caption>
        <tr>
            <td>Mon</td>
            <td>Tue</td>
            <td>Wed</td>
            <td>Thu</td>
            <td>Fri</td>
            <td style='color: red;'>Sat</td>
            <td style='color: red;'>Sun</td>
        </tr>
        <tr>
            <td id='current_mon'></td>
            <td id='current_tue'></td>
            <td id='current_wed'></td>
            <td id='current_thu'></td>
            <td id='current_fri'></td>
            <td id='current_sat'></td>
            <td id='current_sun'></td>
        </tr>    
    </table>

    <button align='left' type='button' onclick='prev()'>PREV</button>
    <button align='right' type='button' onclick='next()'>NEXT</button>
    <br>

    My name is <span id='result'></span>.<hr />
    <input id='search_id' />
<button onclick='Search()'>Search</button>

<p>get_option('event'): " . get_option( 'event_name' ) . "</p>
<p>Username: $current_user->user_login</p>
<p>User email: $current_user->email</p>
<p>User first name: $current_user->firstname</p>
<p>User last name: $current_user->lastname</p>
<p>User display name: $current_user->display_name</p>
<p>User ID: $current_user->ID</p>
</body>
</html>

<script>
    const _months = [
        'January',
        'February',
        'March',
        'April',
        'May',
        'June',
        'July',
        'August',
        'September',
        'October',
        'November',
        'December'
    ];

    const _weekdays = [
        'Sunday',
        'Monday',
        'Tuesday',
        'Wednesday',
        'Thursday',
        'Friday',
        'Saturday',
    ];

    // Init
    var _date = new Date();
    _date.setDate(_date.getDate()-_date.getDay()+1);

    // Format method
    function formatDate(format_date) {
        let month = (1 + format_date.getMonth()).toString().padStart(2, '0');
        let day = format_date.getDate().toString().padStart(2, '0');

        return month + '/' + day;
    }

    // Set date function
    function setWeekDate(direction) {
        // Direction
        if (direction == -1) {
            _date.setDate(_date.getDate()-14);
        }

        // Months and Years
        const months = [];
        const years = [];
        
        // Current weekday
        months[0] = _date.getMonth();
        years[0] = _date.getFullYear();

        document.getElementById('current_mon').innerHTML = formatDate(_date);
        _date.setDate(_date.getDate()+1);

        if (months[0] != _date.getMonth()) {
            months[1] = _date.getMonth();
        }
        if (years[0] != _date.getFullYear()) {
            months[1] = _date.getFullYear();
        }

        document.getElementById('current_tue').innerHTML = formatDate(_date);
        _date.setDate(_date.getDate()+1);

        if (months[0] != _date.getMonth()) {
            months[1] = _date.getMonth();
        }
        if (years[0] != _date.getFullYear()) {
            months[1] = _date.getFullYear();
        }

        document.getElementById('current_wed').innerHTML = formatDate(_date);
        _date.setDate(_date.getDate()+1);
 
        if (months[0] != _date.getMonth()) {
            months[1] = _date.getMonth();
        }
        if (years[0] != _date.getFullYear()) {
            months[1] = _date.getFullYear();
        }
       
        document.getElementById('current_thu').innerHTML = formatDate(_date);
        _date.setDate(_date.getDate()+1);
 
        if (months[0] != _date.getMonth()) {
            months[1] = _date.getMonth();
        }
        if (years[0] != _date.getFullYear()) {
            months[1] = _date.getFullYear();
        }
       
        document.getElementById('current_fri').innerHTML = formatDate(_date);
        _date.setDate(_date.getDate()+1);
 
        if (months[0] != _date.getMonth()) {
            months[1] = _date.getMonth();
        }
        if (years[0] != _date.getFullYear()) {
            months[1] = _date.getFullYear();
        }
       
        document.getElementById('current_sat').innerHTML = formatDate(_date);
        _date.setDate(_date.getDate()+1);

        if (months[0] != _date.getMonth()) {
            months[1] = _date.getMonth();
        }
        if (years[0] != _date.getFullYear()) {
            months[1] = _date.getFullYear();
        }

        document.getElementById('current_sun').innerHTML = formatDate(_date);
        _date.setDate(_date.getDate()+1);

        // Current Month and Year
        if (months.length == 2 && years.length == 2) {
            document.getElementById('current_month').innerHTML = _months[months[0]] + ' ' + years[0] + ' - ' + _months[months[1]] + years[1];
        }
        else if (months.lenth == 2) {
             document.getElementById('current_month').innerHTML = _months[months[0]] + ' ' + years[0] + ' - ' + _months[months[1]] + years[0];         
        }
        else {
            document.getElementById('current_month').innerHTML = _months[_date.getMonth()] + ' ' + _date.getFullYear();
        }
    }

    function next() {
        setWeekDate(1);
        Search();
    }

    function prev() {
        setWeekDate(-1);
        Search();
    }

    
    // Load the script
    window.onload = function() {
        setWeekDate(1);
    };

</script>



";
    }

    return $content;
}

?>
