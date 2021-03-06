<?php
    /*
    Name:           Ramandeep Rathor
    Name:           Musab Nazir
    Name:           Kevin Astilla
    Name:           Nathan Morris
    Description:    databse functions for Homes for Gnomes
    Date:
    */

    function db_connect()
    {
        //return pg_connect("host=127.0.0.1 dbname=astillak_db user=astillak password=100396393" );
        return pg_connect("host=127.0.0.1 dbname=group24_db user=group24_admin password=buffetboys48" );
        //return pg_connect("host=127.0.0.1 dbname=nazirm_db user=nazirm password=Iamaboy1" );
    }

    function updateLastAccess($conn)
    {
        $sql = "UPDATE users SET last_access = '". date("Y-m-d", time()) . "' WHERE user_id = '".$login."'";
        return pg_query($conn, $sql);
    }

    function login($login, $password)
    {
        $conn = db_connect();
        $result = pg_prepare($conn, "my_query", 'SELECT * FROM users WHERE user_id = $1 AND password= $2');
        $result = pg_execute($conn, "my_query", array($login,hash("md5",$password)));

        return $result;
    }
    function personalInformation($userId)
    {
        $conn = db_connect();
        $result = pg_prepare($conn, "my_query_p", 'SELECT * FROM persons WHERE user_id = $1');
        $result = pg_execute($conn, "my_query_p", array($userId));

        return $result;

    }

    function userExists($login)
    {
        $conn = db_connect();
        //make the query
        $sql = "SELECT user_type, email_address, enrol_date, last_access
        FROM users WHERE user_id = '".$login."'";
        $result = pg_query($conn, $sql);
        //if nothing was found
        if(pg_num_rows($result) > 0)
        {
            return True;
        }
        else
        {
            return False;
        }
    }

    function build_dropdown($table, $selected)
    {
        $conn = db_connect();
        $sql = "SELECT value, property FROM ".$table;
        $results = pg_query($conn, $sql);
        $dropdown = '<select style="border-radius: .25em; width:100px; margin-left:2em; border: 1px solid #ced4da;" name="'.$table.'">';
        $dropdown .='\n\t <option value =""></option>';
        For($rows = 0; $rows < pg_num_rows($results); $rows++)
        {
            $select ="";
            $value = pg_fetch_result($results,$rows, "value");
            $property = pg_fetch_result($results,$rows, "property");
            if($selected == $value)
                {$select =  'selected =\"selected\"';}
            $dropdown .='\n\t <option value ="'.$value.'" '.$select.' >'.$property.'</option>';
        }
        $dropdown  .= '\n\t</select>';
        return $dropdown;
    }
    function GetProperty($value, $table)
    {
        $conn = db_connect();
        $sql = "SELECT value, property FROM $table WHERE value = '".$value."'";
        $results = pg_query($conn, $sql);

        return pg_fetch_result($results, "property");
    }
    function build_simple_dropdown($table, $selected)
    {
        $conn = db_connect();
        $sql = "SELECT value FROM ".$table;
        $results = pg_query($conn, $sql);
        if (!$results)
        {
            echo "An error occurred.\n";
            exit;
        }

        $dropdown = '<select style="border-radius: .25em; width:100px;margin-left:2em; border: 1px solid #ced4da;" name="'.$table.'">';
        $dropdown .='\n\t <option value =""></option>';
        For($rows = 0; $rows < pg_num_rows($results); $rows++)
        {
            $select = "";
            $value = pg_fetch_result($results,$rows, "value");
            if($selected == trim($value))
                {$select =  'selected ="selected"';}
            $dropdown .='\n\t <option value ="'.trim($value).'" '.$select.' >'.$value.'</option>';
        }
        $dropdown  .= '\n\t</select>';
        return $dropdown;
    }
    function build_radio($table, $selected)
    {
        $conn = db_connect();
        $sql = "SELECT * FROM ".$table;

        $results = pg_query($conn, $sql);
        $radio = "";

        For($rows = 0; $rows<pg_num_rows($results); $rows++)
        {
            $select ="";
            $value = pg_fetch_result($results,$rows, "value");
            $property = pg_fetch_result($results,$rows, "property");

            if($selected == trim($value))
               {$select =  'checked="checked"';}
            $radio .= '<input type ="radio" style="margin-right:1em;" name="'.$table.'" value="'.$value.'" '.$select.'/>'.$property.'<br/>';
        }
        return $radio;
    }
    function get_property($table, $value)
    {
        $conn = db_connect();
        $sql = "SELECT property FROM ".$table." WHERE value = '".$value."'";
        return pg_query($conn, $sql);
    }

    function LoadSession($userInfo)
    {
        $_SESSION['userID'] = $userInfo["user_id"];
        $_SESSION['password'] = $userInfo["password"];
        $_SESSION['userType'] = $userInfo["user_type"];
        $_SESSION['emailAddress'] = $userInfo["email_address"];
        $_SESSION['last_access'] = $userInfo["last_access"];
        $_SESSION['salutation'] = $userInfo["salutation"];
        $_SESSION['firstName'] = $userInfo["first_name"];
        $_SESSION['lastName'] = $userInfo["last_name"];
        $_SESSION['streetAddress1'] = $userInfo["street_address1"];
        $_SESSION['streetAddress2'] = $userInfo["street_address2"];
        $_SESSION['city'] = $userInfo["city"];
        $_SESSION['province'] = $userInfo["province"];
        $_SESSION['postalCode'] = $userInfo["postal_code"];
        $_SESSION['primaryPhoneNumber'] = $userInfo["primary_phone_number"];
        $_SESSION['secondaryPhoneNumber'] = $userInfo["secondary_phone_number"];
        $_SESSION['faxNumber'] = $userInfo["fax_number"];
        $_SESSION['preferredContactMethod'] = $userInfo["preferred_contact_method"];

        return;
    }
        function build_multiselect_dropdown($table, $selected)
    {
        //gather information here
        $conn = db_connect();
        $sql = "SELECT value, property FROM ".$table;
        $results = pg_query($conn, $sql);
        $dropdown  = '\n\t</select>';
        //process information here

        For($rows = pg_num_rows($results) -1; $rows >= 0 ; $rows--)
        {
            $value = pg_fetch_result($results,$rows, "value");
            $property = pg_fetch_result($results,$rows, "property");

            if(($selected - $value) >= 0)
            {
                $select = 'selected =\"selected\"';
                $selected -= $value;
            }
            else
            {
                $select = '';
            }

            $dropdown ='\n\t <option value ="'.$value.'" '.$select.' >'.$property.'</option>' .$dropdown;
        }
        $dropdown ='\n\t <option value =""></option>'.$dropdown;
        $dropdown = '<select style="border-radius: .25em; width:100px; margin-left:2em; border: 1px solid #ced4da;" name="'.$table.'[]" multiple>' .$dropdown;

        return $dropdown;
    }

    function get_listing_information($user_id, $listing_id)
    {
        $conn = db_connect();
        $result = pg_prepare($conn, "my_query_get_listing", 'SELECT * FROM listings WHERE user_id = $1 AND listing_id = $2');
        $result = pg_execute($conn, "my_query_get_listing", array($user_id, $listing_id));

        return $result;

    }
    function get_listing_information_only($listing_id)
    {
        $conn = db_connect();
        $result = pg_prepare($conn, "my_query_get_listing", 'SELECT * FROM listings WHERE listing_id = $1');
        $result = pg_execute($conn, "my_query_get_listing", array($listing_id));

        return $result;

    }
    function build_checkbox($table, $selected)
    {
        $conn = db_connect();
        $sql = "SELECT value, property FROM ".$table;
        $results = pg_query($conn, $sql);
        $checkBox = "";

        For($rows = pg_num_rows($results) -1; $rows >= 0 ; $rows--)
        {
            $value = pg_fetch_result($results,$rows, "value");
            $property = pg_fetch_result($results,$rows, "property");
            if(($selected - $value) >= 0)
            {
                $select = 'selected =\"selected\"';
                $selected -= $value;
            }
            else
            {
                $select = '';
            }

            $checkBox ='<input type="checkbox" name="'.$table.'[]" value="'.$value.'" '.$selected.' >'.$property.'<br/>'.$checkBox;
        }
                return $checkBox;
    }

    function build_listing_card(&$listing)
    {
        $output = '<h5 class="card-title">'.$listing["headline"].'</h5>';
        $output .= '<img src="./images/Hobbiton-Waikato-IanBrodie-800x600.jpg" width="300px">';
        $output .= '<table><tr><td>Bedrooms</td><td>'.$listing["bedrooms"].'</td><tr/>';
        $output .= '<tr><td>Bathrooms</td><td>'.$listing["bathrooms"].'</td><tr/>';
        if($listing["status"] == 's'){$status = 'SOLD';}
        else if($listing["status"] == 'o'){$status = 'OPEN';}
        else if($listing["status"] == 'c'){$status = 'CLOSED';}
        else if($listing["status"] == 'h'){$status = 'HIDDEN';}
        $output .= '<tr><td>Status</td><td>'.$status.'</td><tr/>';
        $output .= '<tr><th>Price</th><th>$'.$listing["price"].'</th><tr/>';
        $output .= '</table><br/>';
        $output .= '<a href="listing-display.php?listingID='.$listing["listing_id"].'" class="btn btn-outline-success">View Listing</a>';
        return $output;
    }
    function build_favourites_card(&$listing)
    {
        $output = '<h5 class="card-title">'.$listing["headline"].'</h5>';
        $output .= '<img src="./images/Hobbiton-Waikato-IanBrodie-800x600.jpg" width="300px">';
        $output .= '<form method="post">';
        $output .= '<table><tr><td>Bedrooms</td><td>'.$listing["bedrooms"].'</td><tr/>';
        $output .= '<tr><td>Bathrooms</td><td>'.$listing["bathrooms"].'</td><tr/>';
        if($listing["status"] == 's'){$status = 'SOLD';}
        else if($listing["status"] == 'o'){$status = 'OPEN';}
        else if($listing["status"] == 'c'){$status = 'CLOSED';}
        else if($listing["status"] == 'h'){$status = 'HIDDEN';}
        $output .= '<tr><td>Status</td><td>'.$status.'</td><tr/>';
        $output .= '<tr><th>Price</th><th>$'.$listing["price"].'</th><tr/>';
        $output .= '</table><br/>';
        $output .= '<button type="submit" class="btn btn-outline-success" name=\'unfavourite\' value='.$listing["listing_id"].'>Un-Favourite</button>';
        $output .= '<a href="listing-display.php?listingID='.$listing["listing_id"].'" class="btn btn-outline-success" style="margin-left:2em;">View Listing</a>';
        $output .= '</form>';
        return $output;
    }

    function build_report_card(&$listing)
    {
        $status='';
        $output = '<h5 class="card-title">'.$listing["headline"].'</h5>';
        $output .= '<img src="./images/Hobbiton-Waikato-IanBrodie-800x600.jpg" width="300px">';
        $output .= '<form method="post">';
        $output .= '<table><tr><td>Bedrooms</td><td>'.$listing["bedrooms"].'</td><tr/>';
        $output .= '<tr><td>Bathrooms</td><td>'.$listing["bathrooms"].'</td><tr/>';
        if($listing["status"] == 's'){$status = 'SOLD';}
        else if($listing["status"] == 'o'){$status = 'OPEN';}
        else if($listing["status"] == 'c'){$status = 'CLOSED';}
        else if($listing["status"] == 'h'){$status = 'HIDDEN';}
        $output .= '<tr><td>Status</td><td>'.$status.'</td><tr/>';
        $output .= '<tr><th>Price</th><th>$'.$listing["price"].'</th><tr/>';
        $output .= '</table><br/>';
        $output .= '<button type="submit" class="btn btn-outline-success" name=\'hide\' value='.$listing["listing_id"].'>Hide Listing</button>';
        $output .= '<a href="listing-display.php?listingID='.$listing["listing_id"].'" class="btn btn-outline-success" style="margin-left:2em;">View Listing</a>';
        $output .= '</form>';
        return $output;
    }

    function build_multiselect_dropdown_checkbox($table, $selected)
    {
        //gather information here
        $conn = db_connect();
        $sql = "SELECT value, property FROM ".$table;
        $results = pg_query($conn, $sql);
        //process information here
        $dropdown = '</div></div>';

        For($rows = pg_num_rows($results) -1; $rows >= 0 ; $rows--)
        {
            $value = pg_fetch_result($results,$rows, "value");
            $property = pg_fetch_result($results,$rows, "property");

            if(($selected - $value) >= 0)
            {
                $select = 'checked';
                $selected -= $value;
            }
            else
            {
                $select = '';
            }
            $dropdown = '<label for="'.$property.'"><input type="checkbox" name="'.$table.'[]" id="'.$property.'" value="'.$value.'" '.$select.'>'.$property.'</label>' .$dropdown;
        }
        $dropdown ='<div class="multiselect"><div class="selectBox" onclick="showCheckboxes(this)"><select><option>Select all that applies</option></select><div class="overSelect"></div></div><div id="checkboxes" class="dropdownContent">' .$dropdown;

        return $dropdown;
    }

    function userEmail($userId)
    {
      $conn = db_connect();
      $result = pg_prepare($conn, "my_query_email", 'SELECT email_address FROM users WHERE user_id = $1');
      $result = pg_execute($conn, "my_query_p", array($userId));

      return $result;
    }
?>
