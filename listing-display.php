<?php
/*
Name:           Ramandeep Rathor
Name:           Musab Nazir
Name:           Kevin Astilla
Name:           Nathan Morris
Description:    Listing-Display File For Homes For Gnomes
Date:           10th December 2018
*/

require "header.php";
$error = "";
$output = "";

if(isPost())
{
	if(isset($_POST["favourite"]))
	{
		$listing_id = $_POST["favourite"];
	    $sql = "INSERT INTO favourites(user_id, listing_id)
	    VALUES ('".$_SESSION['userID']."','".$listing_id."')";
	    $result = pg_query(db_connect(), $sql);
	    $output .= "Listing added to favourites";
	}
	else if (isset($_POST["unfavourite"])) {
		$listing_id = $_POST["unfavourite"];
	    $sql = "DELETE FROM favourites WHERE user_id = '".$_SESSION['userID']."' AND listing_id = '".$listing_id."'";
	    $result = pg_query(db_connect(), $sql);
	    $output .= "Listing removed from your favorites";
	}
	if(isset($_POST["report"]))
	{
		$listing_id = $_POST["report"];
		$sqlListing = "SELECT * FROM listings WHERE listing_id = '".$listing_id."'";
		$resultListing = pg_query(db_connect(), $sqlListing);
		$ListingDetails = pg_fetch_assoc($resultListing);
	    $sql = "INSERT INTO offensives(user_id, listing_id, reported_on, status)
	    VALUES ('".$_SESSION['userID']."','".$listing_id."', '". date("Y-m-d", time()) . "', '".$ListingDetails['status']."')";
	    $result = pg_query(db_connect(), $sql);
		$sql2 = "UPDATE users SET user_type='d' WHERE user_id='".$ListingDetails['user_id']."'";
		$result = pg_query(db_connect(), $sql2);
	    $output .= "Listing reported";
	}
}

	if(isset($_GET["listingID"]))
	{
		$listing_id = $_GET["listingID"];
		$_SESSION['listingID'] = $_GET["listingID"];
	}
	else{
		$_SESSION['RedirectError'] = "No listing was selected<br/>";
		header("Location:listing-match.php");
		ob_flush();
	}

	if(isset($_SESSION['userType']) && $_SESSION['userType'] == "a"){
		$sql = "SELECT * FROM listings WHERE listing_id = '".$listing_id."'";
		$result = pg_query(db_connect(), $sql);
		$listingDetails = pg_fetch_assoc($result);
		if($_SESSION['userID'] == $listingDetails['user_id'])
		{
			header("Location:listing-update.php");
			ob_flush();
		}
	}

    //end of the function
    $listingInformation = pg_fetch_assoc(get_listing_information_only($_SESSION['listingID']));

    $listingStatus =  $listingInformation['status'];
    $price = $listingInformation['price'];
    $headline = $listingInformation['headline'];
    $description = $listingInformation['description'];
    $postalCode = $listingInformation['postal_code'];
    $images = $listingInformation['images'];
    $city = $listingInformation['city'];
    $propertyOptions = $listingInformation['property_type'];
    $bedroom = $listingInformation['bedrooms'];
    $bathroom = $listingInformation['bathrooms'];
    $propertyType = $listingInformation['property_type'];
    $flooring = $listingInformation['flooring'];
    $parking = $listingInformation['parking'];
    $buildingType = $listingInformation['building_type'];
    $basementType = $listingInformation['basement_type'];
    $interiorType = $listingInformation['interior_type'];

    //$images = $listingInformation['images'];
?>

  <div class="container">
  <div class="row" style="margin-top:75px">
    <div class="col"></div>
    <div class="col-8">
        <br/>
        <?php echo $error; ?>
		<?php echo $output; ?>
        <div class="card">
            <div class="card-body">
                <form method="post" action="<?php sticky();?>" >
                    <div class="form-group">
                        <h3 style="text-decoration:underline;"><?php echo $headline ?></h3>
                        <br/>
                        <h6>Description</h6>
                        <p><?php echo $description ?></p>

						<table style="width:100%;">
							<tr>
								<td><h6>Postal Code</h6></td>
								<td><p><?php echo $postalCode ?></p></td>
							</tr>
							<tr>
								<td><h6>City</h6></td>
								<td><p><?php echo GetProperty($city,"city");?></p></td>
							</tr>
							<tr>
								<td><h6>Bedroom Count</h6></td>
								<td><p><?php echo $bedroom ?></p></td>
							</tr>
							<tr>
								<td><h6>Bathroom Count</h6></td>
								<td><p><?php echo $bathroom ?></p></td>
							</tr>
							<tr>
								<td><h6>Property Options</h6></td>
								<td><p><?php echo GetProperty($propertyOptions,"property_options");?></p></td>
							</tr>
							<tr>
								<td><h6>Property Type</h6></td>
								<td><p><?php echo GetProperty($propertyType,"property_type");?></p></td>
							</tr>
                            <tr>
                                <td><h6>Property Parking</h6></td>
								<td><p><?php echo GetProperty($parking,"property_parking");?></p></td>
                            </tr>
                            <tr>
                                <td><h6>Property Building Type</h6></td>
								<td><p><?php echo GetProperty($buildingType,"property_building_type");?></p></td>
                            </tr>
                            <tr>
                                <td><h6>Property Basement Type</h6></td>
								<td><p><?php echo GetProperty($basementType,"property_basement_type");?></p></td>
                            </tr>
							<tr>
                                <td><h6>Property Flooring</h6></td>
                                <td><?php echo (build_multiselect_dropdown("property_flooring","$flooring"));?></td>
                            </tr>
                            <tr>
                                <td><h6>Property Interior Type</h6></td>
                                <td><?php echo (build_multiselect_dropdown("property_interior_type","$interiorType"));?></td>
                            </tr>
							<tr>
								<td colspan="2"><hr/></td>
							</tr>
							<tr>
								<td><h5 style="color:green;">Price</h5></td>
								<td><h6><?php echo "$".$price ?></h6></td>
							</tr>
							<tr>
								<td colspan="2"><hr/></td>
							</tr>
						</table>
                        <h6>Images<h6/>
                        <br/>
                        <?php //echo (build_radio("listing_status","$listingStatus"));?>
                    </div>
					<div class="d-flex justify-content-between">
	                    <div>
							<?php if( isset($_SESSION['userType'])){
								// echo "<a href=\"listing-display.php?favorite=$listing_id\" class=\"btn btn-outline-success\">Favourite</a>";
								echo '<button type="submit" class="btn btn-outline-success" name=\'favourite\' value='.$_SESSION['listingID'].'>Favourite</button>';
							}?>
	                    </div>
	                    <div>
							<?php if( isset($_SESSION['userType'])){
								// echo "<a href=\"listing-display.php?remove=$listing_id\" class=\"btn btn-outline-success\">Un-Favourite</a>";
								echo '<button type="submit" class="btn btn-outline-success" name=\'unfavourite\' value='.$_SESSION['listingID'].'>Un-Favourite</button>';
							}?>
	                    </div>
						<div>
							<?php if( isset($_SESSION['userType'])){
								// echo "<a href=\"listing-display.php?report=$listing_id\" class=\"btn btn-outline-danger\">Report Listing</a>";
								echo '<button type="submit" class="btn btn-outline-danger" name=\'report\' value='.$_SESSION['listingID'].'>Report Listing</button>';
							}?>
	                    </div>
	                </div>
                </form>
            </div>
        </div>
        <br/>
    </div>
    <div class="col"></div>
  </div>
</div>

<!-- Footer Start -->
<?php
  include 'footer.php'
?>
<!-- Footer End -->
